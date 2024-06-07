<?php

namespace App\Command\Services;

use Action;
use Config;
use Document;
use Event;
use Language;
use Loader;
use Log;
use Minicli\App;
use Minicli\ServiceInterface;
use Registry;
use VQMod;
use Dotenv\Dotenv;

/**
 * Class OpencartService
 *
 * @package App\Command
 */
class OpencartService implements ServiceInterface
{
    protected string $baseDir;

    public Registry $registry;
    public Log $logger;
    public Language $language;
    public Config $config;
    public $db;

    /**
     * Constructs a new instance of the class.
     */
    public function __construct() {
        $this->baseDir = dirname(__DIR__, 2);

        require_once($this->baseDir . '/system/library/config.php');

        $this->loadEnvironmentVariables();
        $this->loadAdminConfig();
        $this->configureConstants();
        $this->configureVirtualQMod();
    }

    /**
     * @param \Minicli\App $app
     * @return true
     * @throws \Exception
     */
    public function load(App $app)
    {
        $this->initializeRegistry();
        $this->configureConfig($_ENV['CLI_CONFIG']);
        $this->configureLog();
        $this->configureTimeZone();
        $this->configureEvents();
        $this->configureDatabaseConnection();
        $this->configureLanguage();
        $this->configureDocument();
        $this->initializeApplicationLoader();
        $this->initializeRegistryProperties();
        $this->configureConfigDefaults();

        return true;
    }

    /**
     * Load environment variables from .env file
     *
     * @throws \Exception if .env file is not found or cannot be loaded
     */
    private function loadEnvironmentVariables(): void
    {
        $dotenv = Dotenv::createImmutable($this->baseDir);
        $dotenv->load();
    }

    /**
     * Loads the admin configuration.
     *
     * @throws \Exception If the configuration file does not exist or cannot be included.
     */
    private function loadAdminConfig(): void
    {
        if (is_file($this->baseDir . '/admin/config.php')) {
            require_once($this->baseDir . '/admin/config.php');
        }
    }

    /**
     * Configure constants for the application
     *
     * @throws \Exception if the required environment variables are not set
     */
    private function configureConstants(): void
    {
        define('VERSION', $_ENV['CLI_VERSION']);
        define('HTTP_SERVER', $_ENV['CLI_HTTP_SERVER']);
        define('HTTPS_SERVER', $_ENV['CLI_HTTPS_SERVER']);
    }

    /**
     * Configures the virtualQMod
     *
     * @throws \Exception
     */
    private function configureVirtualQMod()
    {
        require_once($this->baseDir . '/vqmod/vqmod.php');
        VQMod::bootup();
        require_once(VQMod::modCheck($this->baseDir . '/system/startup.php'));
    }

    /**
     * Initializes the registry
     */
    private function initializeRegistry(): void
    {
        $this->registry = new Registry();
    }

    /**
     * Configure and load the application's config
     *
     * @param string $app_config The configuration to load (default: 'catalog')
     *
     * @throws \Exception If an error occurs while loading the config
     */
    private function configureConfig($app_config = 'catalog')
    {
        $config = new Config();
        $config->load('default');
        $config->load($app_config);
        $this->registry->set('config', $config);
    }

    /**
     * Configures the log object.
     *
     * This method creates a new instance of the Log class using the error filename
     * specified in the application's configuration. The log object is then stored
     * in the registry for easy access.
     *
     * @throws \Exception if the error filename configuration is missing or invalid.
     */
    private function configureLog() {
        $config = $this->registry->get('config');
        $log = new Log($config->get('error_filename'));
        $this->registry->set('log', $log);
    }

    /**
     * Configures the timezone for the application.
     *
     * Sets the default timezone to the value specified in the configuration.
     *
     * @throws \Exception If the configuration value 'date_timezone' is not found in the registry.
     */
    private function configureTimeZone() {
        $config = $this->registry->get('config');
        date_default_timezone_set($config->get('date_timezone'));
    }

    /**
     * Configure events
     *
     * @throws \Exception If an error occurs
     */
    private function configureEvents() {
        $config = $this->registry->get('config');
        $event = new Event($this->registry);
        $this->registry->set('event', $event);

        // Event Register
        if ($config->has('action_event')) {
            foreach ($config->get('action_event') as $key => $value) {
                foreach ($value as $priority => $action) {
                    $event->register($key, new Action($action), $priority);
                }
            }
        }
    }

    /**
     * Configures the database connection.
     *
     * This method reads the database configuration from the registry and creates a new instance of the specified
     * database engine class. It sets the database connection in the registry and syncs the PHP and database time zones.
     *
     * @throws \Exception If the database engine class could not be loaded.
     */
    private function configureDatabaseConnection() {
        $config = $this->registry->get('config');

        if ($config->get('db_autostart')) {
            $class = sprintf('DB\\%s', $config->get('db_engine'));

            if (class_exists($class)) {
                $db = new $class(
                    $config->get('db_hostname'),
                    $config->get('db_username'),
                    $config->get('db_password'),
                    $config->get('db_database'),
                    $config->get('db_port')
                );

                $this->registry->set('db', $db);

                // Sync PHP and DB time zones
                $db->query("SET time_zone = '" . $db->escape(date('P')) . "'");

            } else {
                throw new \Exception('Error: Could not load database adaptor ' . $config->get('db_engine') . '!');
            }
        }
    }

    /**
     * Configure the language for the application.
     *
     * @throws \Exception
     */
    private function configureLanguage() {
        // Language
        $config = $this->registry->get('config');
        $language = new Language($config->get('language_directory'));
        $this->registry->set('language', $language);
    }

    /**
     * Configures the document.
     *
     * @throws \Exception
     */
    private function configureDocument() {
        // Document
        $this->registry->set('document', new Document());
    }

    /**
     * Initialize the application loader.
     *
     * @throws \Exception
     */
    private function initializeApplicationLoader() {
        $config = $this->registry->get('config');

        $loader = new Loader($this->registry);

        // Library Autoload
        if ($config->has('library_autoload')) {
            foreach ($config->get('library_autoload') as $value) {
                $loader->library($value);
            }
        }

        // Model Autoload
        if ($config->has('model_autoload')) {
            foreach ($config->get('model_autoload') as $value) {
                $loader->model($value);
            }
        }

        // Config Autoload
        if ($config->has('config_autoload')) {
            foreach ($config->get('config_autoload') as $value) {
                $loader->config($value);
            }
        }

        $this->registry->set('load', $loader);
    }

    /**
     * Initializes the registry properties.
     */
    private function initializeRegistryProperties(): void
    {
        $this->db = $this->registry->get('db');
        # $this->load = $this->registry->get('load');
        $this->config = $this->registry->get('config');
        $this->language = $this->registry->get('language');
    }

    /**
     * Configures the default values for the configuration.
     */
    private function configureConfigDefaults() {
        $config = $this->registry->get('config');
        $config->set('config_store_id', $_ENV['CLI_STORE_ID']);
        $config->set('config_language_id', $_ENV['CLI_LANGUAGE_ID']);
    }

    /**
     * Magic method to get the value of a property.
     *
     * @param string $name The name of the property.
     * @return mixed Returns the value of the property.
     */
    public function __get($name) {
        return $this->registry->get($name);
    }
}