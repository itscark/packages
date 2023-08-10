<?php

namespace App\Console\Commands\Satis;

use App\Satis\Builder\ArchiveBuilder;
use App\Satis\Builder\PackagesBuilder;
use App\Satis\Builder\WebBuilder;
use App\Satis\Application;
use App\Satis\PackageSelection\PackageSelection;
use Composer\Config;
use Composer\Json\JsonValidationException;
use Composer\Package\Loader\RootPackageLoader;
use Composer\Package\Version\VersionGuesser;
use Composer\Package\Version\VersionParser;
use Composer\Util\ProcessExecutor;
use JsonSchema\Validator;
use Seld\JsonLint\JsonParser;
use Seld\JsonLint\ParsingException;
use UnexpectedValueException;

class SatisBuildCommand extends AbstractSatisCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'satis:build
                            {packages? : Packages that should be built (comma-separated). If not provided, all packages are built.}
                            {--repository-url= : Only update the repository at given URL(s).}
                            {--repository-strict : Also apply the repository filter when resolving dependencies}
                            {--no-html-output : Turn off HTML view}
                            {--skip-errors : Skip Download or Archive errors}
                            {--stats : Display the download progress bar}
                            {--minify : Minify output}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Builds a composer repository out of a json file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $verbose = $this->option('verbose');
        $packagesFilter = $this->argument('packages');
        if ($packagesFilter !== null) {
            $packagesFilter = explode(',', $packagesFilter);
        } else {
            $packagesFilter = [];
        }

        $repositoryUrl = $this->option('repository-url');
        if ($repositoryUrl !== null) {
            $repositoryUrl = explode(',', $repositoryUrl);
        } else {
            $repositoryUrl = [];
        }

        $skipErrors = (bool)$this->option('skip-errors');
        $minify = (bool)$this->option('minify');
        $outputDir = config('satis.output_dir');

        $config = $this->getConfig();

        try {
            $this->check($config);
        } catch (JsonValidationException $e) {
            foreach ($e->getErrors() as $error) {
                $this->output->writeln(sprintf('<error>%s</error>', $error));
            }
            if (!$skipErrors) {
                throw $e;
            }
            $this->output->writeln(sprintf('<warning>%s: %s</warning>', get_class($e), $e->getMessage()));
        } catch (ParsingException|UnexpectedValueException $e) {
            if (!$skipErrors) {
                throw $e;
            }
            $this->output->writeln(sprintf('<warning>%s: %s</warning>', get_class($e), $e->getMessage()));
        }

        // disable packagist by default
        unset(Config::$defaultRepositories['packagist'], Config::$defaultRepositories['packagist.org']);

        if ($homepage = getenv('SATIS_HOMEPAGE')) {
            $config['homepage'] = $homepage;
            $this->output->writeln(sprintf('<notice>Homepage config used from env SATIS_HOMEPAGE: %s</notice>', $homepage));
        }

        $application = new Application();
        $composer = $application->getComposerWithConfig($config);
        $composerConfig = $composer->getConfig();

        // Feed repo manager with satis' repos
        $manager = $composer->getRepositoryManager();

        foreach ($config['repositories'] as $repo) {
            $manager->addRepository($manager->createRepository($repo['type'], $repo, $repo['name'] ?? null));
        }

        // Make satis' config file pretend it is the root package
        $parser = new VersionParser();
        /**
         * In standalone case, the RootPackageLoader assembles an internal VersionGuesser with a broken ProcessExecutor
         * Workaround by explicitly injecting a ProcessExecutor with enableAsync;
         */
        $process = new ProcessExecutor();
        $process->enableAsync();
        $guesser = new VersionGuesser($composerConfig, $process, $parser);
        $loader = new RootPackageLoader($manager, $composerConfig, $parser, $guesser);
        $satisConfigAsRootPackage = $loader->load($config);
        $composer->setPackage($satisConfigAsRootPackage);

        $packageSelection = new PackageSelection($this->output, $outputDir, $config, $skipErrors);
        if (null !== $repositoryUrl && [] !== $repositoryUrl) {
            $packageSelection->setRepositoriesFilter($repositoryUrl, (bool)$this->input->getOption('repository-strict'));
        } else {
            $packageSelection->setPackagesFilter($packagesFilter);
        }

        $packages = $packageSelection->select($composer, $verbose);

        if (isset($config['archive']['directory'])) {
            $downloads = new ArchiveBuilder($this->output, $outputDir, $config, $skipErrors);
            $downloads->setComposer($composer);
            $downloads->setInput($this->input);
            $downloads->dump($packages);
        }

        $packages = $packageSelection->clean();

        if ($packageSelection->hasFilterForPackages() || $packageSelection->hasRepositoriesFilter()) {
            // in case of an active filter we need to load the dumped packages.json and merge the
            // updated packages in
            $oldPackages = $packageSelection->load();
            $packages += $oldPackages;
            ksort($packages);
        }

        $packagesBuilder = new PackagesBuilder($this->output, $outputDir, $config, $skipErrors, $minify);
        $packagesBuilder->dump($packages);

        if ($htmlView = !$this->input->getOption('no-html-output')) {
            $htmlView = !isset($config['output-html']) || $config['output-html'];
        }

        if ($htmlView) {
            $web = new WebBuilder($this->output, $outputDir, $config, $skipErrors);
            $web->setRootPackage($composer->getPackage());
            $web->dump($packages);
        }
    }

    /**
     * @throws ParsingException         if the json file has an invalid syntax
     * @throws JsonValidationException  if the json file doesn't match the schema
     * @throws UnexpectedValueException if the json file is not UTF-8
     */
    private function check(array $config): bool
    {
        $content = json_encode($config, false);
        $parser = new JsonParser();
        $result = $parser->lint($content);
        if (null === $result) {
            if (defined('JSON_ERROR_UTF8') && JSON_ERROR_UTF8 === json_last_error()) {
                throw new UnexpectedValueException('The config is not UTF-8, could not parse as JSON');
            }

            $data = json_decode($content);

            $schemaFile = __DIR__ . '/../../../Satis/satis-schema.json';
            $schema = json_decode(file_get_contents($schemaFile));
            $validator = new Validator();
            $validator->check($data, $schema);

            if (!$validator->isValid()) {
                $errors = [];
                foreach ((array)$validator->getErrors() as $error) {
                    $errors[] = ($error['property'] ? $error['property'] . ' : ' : '') . $error['message'];
                }

                throw new JsonValidationException('The json config file does not match the expected JSON schema', $errors);
            }

            return true;
        }

        throw new ParsingException('The config does not contain valid JSON' . "\n" . $result->getMessage(), $result->getDetails());
    }
}
