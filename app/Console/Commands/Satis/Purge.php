<?php

namespace App\Console\Commands\Satis;

use App\Satis\PackageSelection\PackageSelection;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class Purge extends AbstractSatisCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'satis:purge
                        {dry-run? : Dry run, allows to inspect what might be deleted}';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Purge packages';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $config = $this->getConfig();

        /*
         * Check whether archive is defined
         */
        if (!isset($config['archive']) || !isset($config['archive']['directory'])) {
            $this->output->writeln('<error>You must define "archive" parameter in your config</error>');

            return 1;
        }

        $outputDir = config('satis.output_dir');

        $dryRun = (bool)$this->argument('dry-run');
        if ($dryRun) {
            $this->output->writeln('<notice>Dry run enabled, no actual changes will be done.</notice>');
        }

        $packageSelection = new PackageSelection($this->output, $outputDir, $config, false);
        $packages = $packageSelection->load();

        $prefix = sprintf(
            '%s/%s/',
                $config['archive']['prefix-url'] ?? getenv('SATIS_HOMEPAGE') ?: $config['homepage'],
            $config['archive']['directory']
        );


        $length = strlen($prefix);
        $needed = [];
        foreach ($packages as $package) {
            if (!$package->getDistType()) {
                continue;
            }
            $url = $package->getDistUrl();
            if (substr($url, 0, $length) === $prefix) {
                $needed[] = substr($url, $length);
            }
        }

        $distDirectory = sprintf('%s/%s', $outputDir, $config['archive']['directory']);
        $finder = new Finder();
        $finder
            ->files()
            ->in($distDirectory);

        if (!$finder->count()) {
            $this->output->writeln('<warning>No archives found.</warning>');

            return 0;
        }

        /** @var SplFileInfo[] $unreferenced */
        $unreferenced = [];
        foreach ($finder as $file) {
            $filename = strtr($file->getRelativePathname(), DIRECTORY_SEPARATOR, '/');
            if (!in_array($filename, $needed)) {
                $unreferenced[] = $file;
            }
        }

        if (empty($unreferenced)) {
            $this->output->writeln('<warning>No unreferenced archives found.</warning>');

            return 0;
        }

        foreach ($unreferenced as $file) {
            if (!$dryRun) {
                unlink($file->getPathname());
            }

            $this->output->writeln(
                sprintf(
                    '<info>Removed archive</info>: <comment>%s</comment>',
                    $file->getRelativePathname()
                )
            );
        }

        if (!$dryRun) {
            $this->removeEmptyDirectories($this->output, $distDirectory);
        }

        $this->output->writeln('<info>Done.</info>');

        return 0;
    }

    private function removeEmptyDirectories(OutputInterface $output, string $dir, int $depth = 2): bool
    {
        $empty = true;
        $children = @scandir($dir);

        if (false === $children) {
            return false;
        }

        foreach ($children as $child) {
            if ('.' === $child || '..' === $child) {
                continue;
            }

            $path = $dir . DIRECTORY_SEPARATOR . $child;

            if (is_dir($path)
                && $depth > 0
                && $this->removeEmptyDirectories($output, $path, $depth - 1)
                && rmdir($path)
            ) {
                $output->writeln(sprintf('<info>Removed empty directory</info>: <comment>%s</comment>', $path));
            } else {
                $empty = false;
            }
        }

        return $empty;
    }
}
