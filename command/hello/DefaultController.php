<?php

namespace App\Command\hello;

use Minicli\Command\CommandController;

class DefaultController extends CommandController
{
    public function handle(): void
    {
        $this->getPrinter()->display("Hello World!");
    }
}