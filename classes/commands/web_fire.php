<?php if (!defined('BASE_PATH')) exit('No direct script access allowed');

/**
 * The web command: used to install WebFire
 */
class FIRE_WebFire extends BaseCommand
{
    /**
     * Location of the application folder
     *
     * @ccess private
     * @var string
     **/
    private $location;

    private $directories = array(
    );

    /**
     * List of WebFire's files, excluding the assets
     *
     * @access private
     * @var array
     **/
    private $files = array(
        'controllers/fire/generate.php',
        'helpers/fire/generate_helper.php',
        'models/fire/generate_model.php',
        'models/fire/template_scanner.php',
        'views/fire/generate/controller_form.php',
        'views/fire/generate/controller_success.php',
        'views/fire/generate/index.php',
        'views/fire/generate/migration_form.php',
        'views/fire/generate/migration_success.php',
        'views/fire/generate/model_form.php',
        'views/fire/generate/model_success.php',
        'views/fire/templates/action.tpl',
        'views/fire/templates/controller.tpl',
        'views/fire/templates/empty_migration.tpl',
        'views/fire/templates/migration.tpl',
        'views/fire/templates/migration_column.tpl',
        'views/fire/templates/model.tpl',
        'views/fire/layout.php',
    );

    /**
     * List of WebFire's assets
     *
     * @access private
     * @var array
     **/
    private $assets = array(
        'assets/css/fire.css',
        'assets/img/webfire.png',
        'assets/js/fire.js',
    );

    /**
     * El Constructor!
     *
     * @access public
     * @param array $params The parsed command line arguments
     * @return void
     * @author Aziz Light
     **/
    public function __construct(array $params)
    {
        $this->location = $params['location'];
    }

    /**
     * The brains of the command
     *
     * @access public
     * @return void
     * @author Aziz Light
     **/
    public function run()
    {
        if ($this->is_webfire_installed())
        {
            throw new RuntimeException('WebFire seems to be installed already!');
        }

        foreach ($this->files as $file)
        {
            $location = $this->location . DIRECTORY_SEPARATOR . $file;
            var_dump($location);
        }
    }

    /**
     * Checks if WebFire is installed
     *
     * @access private
     * @return bool Whether or not WebFire is installed
     * @author Aziz Light
     **/
    private function is_webfire_installed()
    {
        $result = TRUE;
        foreach ($this->files as $file)
        {
            $result = $result && is_file($this->location . DIRECTORY_SEPARATOR . $file);
        }

        foreach ($this->assets as $asset)
        {
            // NOTE: fire assumes that the index.php file is on the same level as the application folder
            $result = $result && is_file(realpath($this->location . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . $asset));
        }

        return $result;
    }
}
