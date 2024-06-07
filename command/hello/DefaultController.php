<?php

namespace App\Command\hello_there;

use Minicli\Command\CommandController;

/**
 * This is a command without Opencart, just a plain command
 * 
 * The command would be `hello_there`
 **/   
class DefaultController extends CommandController
{
    public function handle(): void
    {
        $this->getPrinter()->display("Hello World!");
    }
}
