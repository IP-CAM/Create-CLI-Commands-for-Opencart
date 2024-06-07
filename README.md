## The free to use Opencart shopping cart software doesn’t have a command line (CLI) implementation like Laravel or Craft CMS, but it is not hard to add it yourself.

In this tutorial I will show how you can do add command functionality to Opencart with the use of the minicli php package, a very low footprint CLI implementation. It has no dependencies and is very easy to install.

> Requirement was an easy to create low footprint command line mechanism which didn't require to modify any core files of Opencart

### Bootstrapping Opencart
The hardest part was bootstrapping Opencart as a service, to have its functionality available in the commands. Inside the minicli_opencart Github repository I have included a PHP class (OpencartService) which is for most situations ready to use out of the box.

> Following instruction I have tested on an Opencart version 3.0.3.8.

### Step 1. get the minicli library in your installation

```
composer require minicli/minicli
```

### Step 2. create some folders
In the root folder of your Opencart create a folder named **bin**
```
mkdir bin
```
In this bin folder create a subfolder named **services**.
```
cd bin && mkdir services
```
In the root folder of your Opencart create a folder named **command**
```
mkdir command
```
Now the result should look like:
```
- root folder of Opencart
| - admin
| - bin
| - - services
| - catalog
| - command
| - image
| - plugin 
```

### Step 3. create and modify some files

#### cartisan
By default the program to execute a command is *minicli*, but I didn't find that sounding cool. Therefor I have changed the name to *cartisan*, as in shopping *cart* and *isan* inspired on Laravel's artisan. But you can name it as you like.

What *cartisan* does is it initializes the minicli application and registeres any services defined. In our situation an OpencartService is registered, which bootstraps an Opencart instance into the minicli object.

If you decide to leave it *cartisan*, then you should copy the **cartisan** file from the Github repository into the **bin** folder, created earlier. Make sure that the cartisan file is executable.

```
chmod +X cartisan
```

Commands will be executed from root folder as:

```
.\bin\cartisan hello
```
or:
```
.\bin\cartisan\product_test
```

#### .env
Modify in the root folder of Opencart your * .env* file or create one with the same name.

Add the the CLI settings in the *.env.sample* file in the repository to the .env file.

PS. if you haven't installed *DOTENV* you can install it, or you can replace in the service later on in this tutorial with config settings in your own implementation.

Modify the values to your own situation.
```
CLI_VERSION=3.0.3.8
CLI_CONFIG=catalog
CLI_STORE_ID=1
CLI_LANGUAGE_ID=1 
CLI_HTTP_SERVER=http://www.example.com
CLI_HTTPS_SERVER=https://www.example.com
```

#### OpencartService.php
Copy from the Github repository the file *OpencartService.php* to the **services** folder inside the **bin** folder you've created earlier.

Modify the *OpencartService.php* file to your own situation, but if you are using *VQMod* en *DOTENV* you should be fine with the contents as it is.

#### Some test commands
To have a jumpstart, you can copy the **hello** and **product_test** folder in the repository to the command folder of Opencart.

### Step 4. Modify composer.json
Follow the recommendations in the composer.json file in the Github repository.

In the "autoload”section you need to add some psr-4 rules:

```
"autoload": {
    "psr-4": {
      "App\\Command\\": "./command/",
      "App\\Command\\Services\\": "./bin/services",
      "App\\System\\": "./system/",
      "App\\System\\Library\\": "./system/library/"
    }
  },
```

After you have changed the composer.json file, you should do:

```
composer dump-autoload
```

### 5. Happy commanding and where to read more
That should be it. Now you can make your own commands.

Reminder

| folder  | namespace   | command  |
| ------------ | ------------ | ------------ |
| command\some_command   | App\Command\some_command   | some_command   |
| command\Some_command   | App\Command\Some_command   | some_command   |
| command\SomeCommand   | App\Command\SomeCommand   | somecommand   |

#### How to create your first command

* Create a folder with the same name as the command to create in the command folder, f.e. import_excel_data_into_customers
* Copy the *DefaultController.php* from the **product_test** folder into this just created folder
* Change the namespace in this file and build your own command requirements
* Remember that *$this->getPrinter()->display(‘some text’)* will print text to the console
* and *$this->getPrinter()->printTable($arrayWithHeading)* will print a table to the console

Execute your command from the command line (CLI) in the root folder of Opencart as: 
```
.\bin\cartisan import_excel_data_into_customers
```

> There is no need to register the command before you can use it

### 6. Useful links
[Minicli CLI package](https://github.com/minicli/minicli)
[Github repository](https://docs.minicli.dev/en/latest/)
