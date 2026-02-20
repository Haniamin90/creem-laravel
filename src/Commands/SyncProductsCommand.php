<?php

namespace Creem\Laravel\Commands;

use Creem\Laravel\Creem;
use Creem\Laravel\Exceptions\CreemApiException;
use Illuminate\Console\Command;

class SyncProductsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'creem:sync-products';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync products from CREEM to your local database';

    /**
     * Execute the console command.
     */
    public function handle(Creem $creem): int
    {
        $this->info('Syncing products from CREEM...');

        try {
            $response = $creem->searchProducts();

            $products = $response['items'] ?? [];

            if (empty($products)) {
                $this->warn('No products found in your CREEM account.');

                return self::SUCCESS;
            }

            $this->table(
                ['ID', 'Name', 'Price', 'Currency', 'Billing Type', 'Status'],
                collect($products)->map(fn (array $product) => [
                    $product['id'] ?? 'N/A',
                    $product['name'] ?? 'N/A',
                    isset($product['price']) ? number_format($product['price'] / 100, 2) : 'N/A',
                    $product['currency'] ?? 'N/A',
                    $product['billing_type'] ?? 'N/A',
                    $product['status'] ?? 'N/A',
                ])->toArray()
            );

            $this->info(count($products).' product(s) found.');

            // Dispatch event for custom sync handling
            event('creem.products.synced', [$products]);

            return self::SUCCESS;
        } catch (CreemApiException $e) {
            $this->error("Failed to sync products: {$e->getMessage()}");

            if ($traceId = $e->getTraceId()) {
                $this->line("Trace ID: {$traceId}");
            }

            return self::FAILURE;
        }
    }
}
