<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Str;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $data = [
            ["url" => "git@bitbucket.org:iwaves/shopware-newsletter.git"],
            ["url" => "git@bitbucket.org:iwaves/shopware-advanced-slider.git"],
            ["url" => "git@bitbucket.org:iwaves/shopware-alina-product-listener.git"],
            ["url" => "git@bitbucket.org:iwaves/shopware-customfields.git"],
            ["url" => "git@bitbucket.org:iwaves/shopware-documents.git"],
            ["url" => "git@bitbucket.org:iwaves/shopware-giftpackage.git"],
            ["url" => "git@bitbucket.org:iwaves/shopware-logo-slider.git"],
            ["url" => "git@bitbucket.org:iwaves/shopware-mediaextension.git"],
            ["url" => "git@bitbucket.org:iwaves/shopware-pim-connector.git"],
            ["url" => "git@bitbucket.org:iwaves/shopware-product-extension.git"],
            ["url" => "git@bitbucket.org:iwaves/shopware-promotion.git"],
            ["url" => "git@bitbucket.org:iwaves/netwavesx.git"],
            ["url" => "git@bitbucket.org:iwaves/shopware-order-history.git"],
            ["url" => "git@bitbucket.org:iwaves/shopware-product-manufacturer-extension.git"],
            ["url" => "git@bitbucket.org:iwaves/shopware-product-not-found.git"],
            ["url" => "git@bitbucket.org:iwaves/shopware-blog.git"],
            ["url" => "git@bitbucket.org:iwaves/shopware-lazyloading.git"],
            ["url" => "git@bitbucket.org:iwaves/shopware-alina-erp-sync.git"],
            ["url" => "git@bitbucket.org:iwaves/neusicht-themewaves.git"],
            ["url" => "git@bitbucket.org:iwaves/shopware-afro-mhd.git"],
            ["url" => "git@bitbucket.org:iwaves/shopware-checkout-extras.git"],
            ["url" => "git@bitbucket.org:iwaves/shopware-afro-migration-tools.git"],
            ["url" => "git@bitbucket.org:iwaves/shopware-erp-import-export.git"],
            ["url" => "git@bitbucket.org:iwaves/shopware-custom-tax.git"],
            ["url" => "git@bitbucket.org:iwaves/shopware-checkout-tabs.git"],
            ["url" => "git@bitbucket.org:iwaves/shopware-testimonials-manager.git"],
            ["url" => "git@bitbucket.org:iwaves/shopware-utils.git"],
            ["url" => "git@bitbucket.org:iwaves/shopware-vomfeld-order-fields.git"],
            ["url" => "git@bitbucket.org:iwaves/shopware-vomfeld-navision-connector.git"],
            ["url" => "git@bitbucket.org:iwaves/shopware-vomfeld-compatibility.git"],
            ["url" => "git@bitbucket.org:iwaves/shopware-vomfeld-custom-fields.git"],
            ["url" => "git@bitbucket.org:iwaves/shopware-vomfeld-delivery-day.git"],
            ["url" => "git@bitbucket.org:iwaves/shopware-vomfeld-exports.git"],
            ["url" => "git@bitbucket.org:iwaves/shopware-vomfeld-product-extension.git"],
            ["url" => "git@bitbucket.org:iwaves/shopware-hubspot-connector.git"],
            ["url" => "git@bitbucket.org:iwaves/faq-plugin.git"],
            ["url" => "git@bitbucket.org:iwaves/shopware-product-properties.git"],
            ["url" => "git@bitbucket.org:iwaves/shopware-page-performance.git"],
            ["url" => "git@bitbucket.org:iwaves/shopware-cms-elements.git"],
            ["url" => "git@bitbucket.org:iwaves/shopware-advancedsearch-extension.git"],
            ["url" => "git@bitbucket.org:iwaves/shopware-logging.git"],
            ["url" => "git@bitbucket.org:iwaves/shopware-seo-url-manager.git"],
            ["url" => "git@bitbucket.org:iwaves/shopware-list-image.git"],
            ["url" => "git@bitbucket.org:iwaves/shopware-firebase.git"]
        ];

        foreach ($data as &$item) {
            $technicalName = explode(':', $item['url']);
            $technicalName = end($technicalName);
            $technicalName = str_replace('.git', '', $technicalName);
            $item['technicalName'] = $technicalName;
            $name = explode('/', $technicalName);
            $name = str_replace('.git', '', $name[1]);
            // Replace dashes with spaces
            $formattedName = str_replace('-', ' ', $name);

            // Uppercase the first letter of each word
            $formattedName = ucwords($formattedName);

            // Assign the formatted name back to the item
            $item['name'] = $formattedName;
        }

        unset($item);
        foreach ($data as $item) {
            DB::table('packages')->insert([
                'id' => Str::uuid()->toString(),
                'name' => $item['name'],
                'technical_name' => $item['technicalName'],
                'url' => $item['url'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
