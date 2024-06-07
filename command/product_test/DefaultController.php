<?php

namespace App\Command\product_test;

use Minicli\Command\CommandController;

class DefaultController extends CommandController
{
    const ID = 'ID';
    const MODEL = 'Model';
    const NAME = 'Name';

    /**
     * Handles the logic for printing a table of products.
     *
     * @return void
     */
    public function handle(): void
    {
        $products = $this->getProducts();
        $printTable = $this->buildTable($products);
        $this->getPrinter()->printTable($printTable);
    }

    /**
     * Retrieves an array of products.
     *
     * @return array The array of products.
     */
    private function getProducts(): array
    {
        $load = $this->getApp()->opencart->registry->get('load');
        $load->model('catalog/product');

        return $this->getApp()->opencart->model_catalog_product->getProducts([
            'start' => 0,
            'limit' => 10,
        ]);
    }

    /**
     * Builds a table from an array of products.
     *
     * @param array $products The array of products to build the table from.
     *
     * @return array Returns the table with the products.
     */
    private function buildTable(array $products): array
    {
        $printTable = [[self::ID, self::MODEL, self::NAME]];
        return array_merge($printTable, array_map(fn($p) => [$p['product_id'], $p['model'], $p['name']], $products));
    }
}