![image](https://github.com/enovision/minicli_opencart/assets/1536241/c519e93a-63f7-4c04-8cb4-778c40c1fdd2)


# CLI for Opencart using minicli PHP library

## What is in this repository

With this repository comes some supporting files to initialize your own CLI when using Opencart shopping cart.

Opencart Shoppingcart doesn't have a command line (CLI) implementation like Laravel or Craft CMS has. 

In this tutorial I will show you how you can do it easily with the use of the *minicli* php package, a very
low footprint CLI package. It has no dependencies and is very easy to install. 

The hardest part was the Opencart service, which bootstraps Opencart to have it available in your commands.
In this repository I have included this service, for most situations ready to use out of the box.

I have tested this on Opencart version 3.0.3.8. 

Don't be afraid, nothing will be changed on the existing Opencart installation, except for adding some folders (**bin** and **command**)
to the root of your Opencart installation. 

## How to make this work:

### 1. get the minicli library in your installation

```
composer require minicli/minicli 
```

### 3. create some folders

In the **root** folder of your Opencart installation create a folder named `bin`

```
mkdir bin
```

In this **bin** folder create a subfolder named `services`

```
cd bin && mkdir services
```

In the **root** folder of your Opencart installation create a folder named `command`

```
mkdir command
```

### 4. create and modify some files

#### cartisan ####

By default the statement to execute a command is `minicli`, but I didn't think that was sounding
cool. That is why I have changed it to `cartisan`, as in shopping *cart* and *isan* inspired on Laravel's *artisan*.
But you can name it as you like.

If you decide to leave it *cartisan*, then you should copy the *cartisan* file from this repository into the **bin**
folder, created earlier. Make sure that this *cartisan* is executable.

```
chmod +X cartisan
```

Commands will be executed from root folder as:

```
bin\cartisan hello
```
or:
```
bin\cartisan\product_test
```

#### .env ####
Modify in the **root** folder of your Opencart your *.env* file or create one. 
Add the the **CLI_** settings in the *.env.sample* file in this repository to your .env file.
PS. if you haven't installed DOTENV/DOTENV you can install it, or you can replace in the service
later on in this tutorial with config settings in your own implementation. 

Modify the values to your own situation.

```
CLI_VERSION=3.0.3.8
CLI_CONFIG=catalog
CLI_STORE_ID=1
CLI_LANGUAGE_ID=1 
CLI_HTTP_SERVER=http://www.example.com
CLI_HTTPS_SERVER=https://www.example.com
```

#### OpencartService.php ####

Copy from this repository the file *OpencartService.php* to the **services** folder in the **bin** folder you've
created earlier in this tutorial.

Modify the *OpencartService.php* file to your own situation, but if you are using VQMod en DOTENV you should be fine
with the contents as it is. 

#### Some test commands ####

To have a jumpstart, you can copy the **hello** and **product_test** folder in this repository to the **command** folder
of your Opencart installation. 

### 2. modify the composer.json

Follow the recommendations in the composer.json file in this repository.

In the "autoload" section you need to add some psr-4 rules:

```json
"autoload": {
    "psr-4": {
      "App\\Command\\": "./command/",
      "App\\Command\\Services\\": "./bin/services",
      "App\\System\\": "./system/",
      "App\\System\\Library\\": "./system/library/"
    }
  },
```

After you have changed the *composer.json* file, you should do:

```
composer dump-autoload
```

### 5. Happy commanding and where to read more

That should be all. Now you can make your own commands.

* create a folder with the same name as the command to create in the **command** folder, f.e. import_excel_data_into_customers
* copy the *DefaultController.php* from the **product_test** folder into this created folder
* change the namespace in this file and build your own command requirements
* remember that $this->getPrinter()->display('some text') will print text to the console
* and $this->getPrinter()->printTable($arrayWithHeading) will print a table to the console
* Use your command from the command line (CLI) in the root folder of Opencart as: `bin\cartisan import_excel_data_into_customers`

!!! There is no need to register the command before you can use it

#### Documentation of minicli

(Github repo)[https://github.com/minicli/minicli]
(Documentation)[https://docs.minicli.dev/en/latest/]
