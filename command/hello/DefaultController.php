<?php

namespace App\Command\hello;

use Minicli\Command\CommandController;

/**
 * This is a command without Opencart, just a plain command
 * 
 * The command would be `hello`
 * The class namespace could also be App\Command\Hello, but
 * the folder name is also in lowercase
 * 
 * IMPORTANT: When you name a command in CamelCase then it would
 * become a command in lowercase, so SimpleProductImport would become:
 * `simpleproductimport`.
 * But simple_product_import would become just like that name.
 **/   
class DefaultController extends CommandController
{
    public function handle(): void
    {
        $this->getPrinter()->display("Hello World!");
    }
}
