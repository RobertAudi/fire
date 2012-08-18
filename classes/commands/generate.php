<?php if (!defined('BASE_PATH')) exit('No direct script access allowed');

/**
* Generate task
*/
class Generate extends BaseCommand
{
    /**
     * Arguments passed to the constructor.
     *
     * @var    array
     * @access private
     */
    private $args;

    /**
     * Extra information to be generated seperately.
     * This includes controller actions and model methods.
     *
     * @var    string
     * @access private
     */
    private $extra;

    /**
     * The constructor.
     *
     * @access public
     * @param  array $args : Parsed command line arguments.
     * @author Aziz Light
     */
    public function __construct(array $args)
    {
        $this->extra = $this->generate_extra($args);

        $this->args = $args;
    }

    /**
     * Braaaaains
     *
     * @access public
     * @return void
     * @author Aziz Light
     */
    public function run()
    {
        $subject = $this->args['subject'];
        $this->$subject();
        return;
    }

    /**
     * The method that generates the controller
     *
     * @access private
     * @param array $force_views_creation : Should the method force the creation of views? Used in the scaffold method.
     * @return string
     * @author Aziz Light
     */
    private function controller()
    {
        $args = array(
            "class_name"         => $this->args['name'],
            "filename"           => $this->args['filename'],
            "application_folder" => $this->args['application_folder'],
            "parent_class"       => $this->args['parent_controller'],
            "extra"              => $this->extra,
        );
        $template    = new TemplateScanner("controller", $args);
        $controller  = $template->parse();

        $location = $this->args["location"] . "/controllers/";
        $filename = $location . $this->args['filename'];

        $message = "\t";
        if (file_exists($filename))
        {
            $message .= 'Controller already exists : ';
            if (php_uname("s") !== "Windows NT")
            {
                $message  = ApplicationHelpers::colorize($message, 'light_blue');
            }
            $message .= $this->args['application_folder'] . '/controllers/' . $this->args['filename'];
        }
        elseif (file_put_contents($filename, $controller))
        {
            $message .= 'Created controller: ';
            if (php_uname("s") !== "Windows NT")
            {
                $message  = ApplicationHelpers::colorize($message, 'green');
            }
            $message .= $this->args['application_folder'] . '/controllers/' . $this->args['filename'];
        }
        else
        {
            $message .= 'Unable to create controller: ';
            if (php_uname("s") !== "Windows NT")
            {
                $message  = ApplicationHelpers::colorize($message, 'red');
            }
            $message .= $this->args['application_folder'] . '/controllers/' . $this->args['filename'];
        }

        // The controller has been generated, output the confirmation message
        fwrite(STDOUT, $message . PHP_EOL);

        // Create the view files.
        $this->views();

        return;
    }

    /**
     * The method that generates the models
     *
     * @access private
     * @return void
     * @author Aziz Light
     */
    private function model()
    {
        $args = array(
            "class_name"         => ucfirst(strtolower($this->args['name'])),
            "filename"           => $this->args['filename'],
            "application_folder" => $this->args['application_folder'],
            "parent_class"       => $this->args['parent_model'],
            "extra"              => $this->extra,
        );
        $template = new TemplateScanner("model", $args);
        $model    = $template->parse();

        $location = $this->args["location"] . "/models/";
        $filename = $location . $this->args['filename'];

        $message = "\t";
        if (file_exists($filename))
        {
            $message .= 'Model already exists : ';
            if (php_uname("s") !== "Windows NT")
            {
                $message  = ApplicationHelpers::colorize($message, 'light_blue');
            }
            $message .= $this->args['application_folder'] . '/models/' . $this->args['filename'];
        }
        elseif (file_put_contents($filename, $model))
        {
            $message .= 'Created model: ';
            if (php_uname("s") !== "Windows NT")
            {
                $message  = ApplicationHelpers::colorize($message, 'green');
            }
            $message .= $this->args['application_folder'] . '/models/' . $this->args['filename'];
        }
        else
        {
            $message .= 'Unable to create model: ';
            if (php_uname("s") !== "Windows NT")
            {
                $message  = ApplicationHelpers::colorize($message, 'red');
            }
            $message .= $this->args['application_folder'] . '/models/' . $this->args['filename'];
        }

        fwrite(STDOUT, $message . PHP_EOL);

        // Create the migration for the new model
        $this->migration();

        return;
    }

    /**
     * Creates the view files and the views folder if necessary.
     *
     * @access private
     * @return bool
     */
    private function views()
    {
        $controller = $this->args['name'];
        $views = (array_key_exists('extra', $this->args)) ? $this->args['extra'] : array();

        if (empty($views))
        {
            return true;
        }

        // Check that the views folder exists and create it if it doesn't
        $views_folder = $this->args['application_folder'] . '/views/' . strtolower($controller);
        $location = $this->args['location'] . '/views/' . strtolower($controller);
        if (!file_exists($location) || !is_dir($location))
        {
            $message = "\t";
            if (mkdir($location, 0755))
            {
                $message .= 'Created folder: ';
                if (php_uname("s") !== "Windows NT")
                {
                    $message  = ApplicationHelpers::colorize($message, 'green') . $views_folder;
                }
                else
                {
                    $message .= $views_folder;
                }
                fwrite(STDOUT, $message . PHP_EOL);
                unset($message);
            }
            else
            {
                $message .= 'Unable to create folder: ';
                if (php_uname("s") !== "Windows NT")
                {
                    $message  = ApplicationHelpers::colorize($message, 'red') . $views_folder;
                }
                else
                {
                    $message .= $views_folder;
                }
                fwrite(STDOUT, $message . PHP_EOL);
                return false;
            }
        }

        // Create the views
        foreach ($views as $view)
        {
            // First check that the views doesn't already exist
            if (file_exists($location . '/' . $view . '.php'))
            {
                $message = "\tView already exists: ";
                if (php_uname("s") !== "Windows NT")
                {
                    $message = ApplicationHelpers::colorize($message, 'light_blue') . $views_folder . '/' . $view . '.php';
                }
                else
                {
                    $message .= $views_folder . '/' . $view . '.php';
                }
                fwrite(STDOUT, $message . PHP_EOL);
                unset($message);
                continue;
            }

            $content  = '<h1>' . $controller . '#' . $view . '</h1>';
            $content .= PHP_EOL . '<p>Find me in ' . $views_folder . '/' . $view . '.php</p>';

            $message = "\t";
            if (file_put_contents($location . '/' . $view . '.php', $content))
            {
                $message .= 'Created view: ';
                if (php_uname("s") !== "Windows NT")
                {
                    $message  = ApplicationHelpers::colorize($message, 'green') . $views_folder . '/' . $view . '.php';
                }
                else
                {
                    $message  .= $views_folder . '/' . $view . '.php';
                }
            }
            else
            {
                $message .= 'Unable to create view ';
                if (php_uname("s") !== "Windows NT")
                {
                    $message  = ApplicationHelpers::colorize($message, 'red') . $views_folder . '/' . $view . '.php';
                }
                else
                {
                    $message  .= $views_folder . '/' . $view . '.php';
                }
            }

            fwrite(STDOUT, $message . PHP_EOL);
            unset($message);
        }

        return true;
    }

    // FIXME: Document this bitch!
    private function scaffold()
    {
        if (isset($this->args['extras']))
        {
            $message = "The following arguments were ignored: ";
            if (php_uname("s") !== "Windows NT")
            {
                $message = ApplicationHelpers::colorize($message, 'red') . implode(", ", $this->args['extra']) . PHP_EOL;
            }
            else
            {
                $message .= implode(", ", $this->args['extra']) . PHP_EOL;
            }
            fwrite(STDOUT, $message);
            unset($this->args['extra']);
        }

        $this->args['extra'] = array('index', 'create', 'view', 'edit', 'delete');

        $this->controller(true);

        $this->model();
    }

    /**
     * Create a migration file
     *
     * @access private
     * @return void
     * @author Aziz Light
     **/
    private function migration()
    {
        $location = $this->args['location'] . '/migrations/';
        if (!is_dir($location))
        {
            mkdir($location);
        }

        $backtrace = debug_backtrace();
        $calling_function = $backtrace[1]['function'];

        if ($calling_function === "model")
        {
            $args = array(
                'class_name'         => 'Migration_Add_' . $this->args['name'],
                'table_name'         => strtolower($this->args['name']),
                'filename'           => 'add_' . $this->args['filename'],
                'application_folder' => $this->args['application_folder'],
                'parent_class'       => $this->args['parent_migration'],
                'extra'              => $this->extra
            );

            $template_name = 'migration';
        }
        else
        {
            $args = array(
                'class_name'         => 'Migration_' . $this->args['name'],
                'table_name'         => $this->get_table_name_out_of_migration_name(),
                'filename'           => $this->args['filename'],
                'application_folder' => $this->args['application_folder'],
                'parent_class'       => $this->args['parent_migration'],
                'extra'              => $this->extra
            );

            $template_name = 'empty_migration';
        }

        $template  = new TemplateScanner($template_name, $args);
        $migration = $template->parse();

        $migration_number = $this->get_migration_number();
        $filename = $location . $migration_number . '_' . $this->args['filename'];
        $potential_duplicate_migration_filename = $this->decrement_migration_number($migration_number) . '_' . $this->args['filename'];
        $potential_duplicate_migration = $location . $potential_duplicate_migration_filename;

        $message = "\t";
        if (file_exists($potential_duplicate_migration))
        {
            $message .= 'Migration already exists : ';
            if (php_uname("s") !== "Windows NT")
            {
                $message  = ApplicationHelpers::colorize($message, 'light_blue');
            }
            $message .= $this->args['application_folder'] . '/migrations/' . $potential_duplicate_migration_filename;
        }
        else if (file_put_contents($filename, $migration) && $this->add_migration_number_to_config_file($migration_number))
        {
            $message .= 'Created Migration: ';
            if (php_uname("s") !== "Windows NT")
            {
                $message  = ApplicationHelpers::colorize($message, 'green');
            }
            $message .= $this->args['application_folder'] . '/migrations/' . $migration_number . '_' . $this->args['filename'];
        }
        else
        {
            $message .= 'Unable to create migration: ';
            if (php_uname("s") !== "Windows NT")
            {
                $message  = ApplicationHelpers::colorize($message, 'red');
            }
            $message .= $this->args['application_folder'] . '/migrations/' . $migration_number . '_' . $this->args['filename'];
        }

        fwrite(STDOUT, $message . PHP_EOL);

        return;
    }

    /**
     * Generate the extra content that goes
     * in the main template depending on the
     * subject (controller, model or migration)
     *
     * @access private
     * @param array $args The parsed command line arguments
     * return string The extra code to inject into the main template
     * @author Aziz Light
     */
    private function generate_extra(array $args)
    {
        if (!array_key_exists('extra', $args) || !is_array($args['extra']) || empty($args['extra']))
        {
            $extra = '';
        }
        else
        {
            switch ($args['subject'])
            {
                case 'controller':
                    $extra = $this->generate_controller_actions($args['name'], $args['extra']);
                    break;
                case 'model':
                    $extra = $this->generate_migration_statement($args['name'], $args['extra']);
                    break;
                case 'migration':
                    $extra = $this->generate_migration_statement($args['name'], $args['extra']);
                    break;
            }
        }

        return $extra;
    }

    /**
     * Generate the actions that will go in the controller.
     *
     * @access private
     * @param string $class_name : The name of the controller
     * @param  array $args : The list of actions to generate
     * @return string : The generated actions
     * @author Aziz Light
     */
    private function generate_controller_actions($class_name, array $actions)
    {
        $extra = "";

        foreach ($actions as $action)
        {
            $args = array(
                "class_name" => $class_name,
                "extra" => $action
            );
            $template = new TemplateScanner("actions", $args);
            $extra   .= $template->parse();
            unset($args, $template);
        }
        return $extra;
    }

    /**
     * Generate the body the the migration that will be generated
     *
     * @access private
     * @param string $class_name The name of the model
     * @param array $columns The list of columns with their attributes
     * @return string The migration's body
     * @author Aziz Light
     **/
    private function generate_migration_statement($class_name, array $columns)
    {
        $extra = '';

        foreach ($columns as $column => $attrs)
        {
            $args = array();
            $args['column_name'] = $column;

            foreach ($attrs as $attr => $value)
            {
                $args['column_' . $attr] = $value;
            }

            $template = new TemplateScanner('migration_column', $args);
            $extra .= $template->parse();
        }

        return trim($extra, PHP_EOL) . PHP_EOL;
    }

    /**
     * Get the number of the migration by looking at the existing migrations
     *
     * @access private
     * @return string The number of the migration number in the format 001
     * @author Aziz Light
     **/
    private function get_migration_number()
    {
        $migrations = glob($this->args['location'] . '/migrations/*.php');
        $tmp = end($migrations);
        $tmp = explode(DIRECTORY_SEPARATOR, $tmp);
        $migration = end($tmp);
        $migration_number = intval($migration);
        $migration_number++;

        if ($migration_number < 10)
        {
            $migration_number = '00' . $migration_number;
        }
        else if ($migration_number < 100)
        {
            $migration_number = '0' . $migration_number;
        }
        else
        {
            $migration_number = strval($migration_number);
        }

        return $migration_number;
    }

    /**
     * This method is used in the process of verifying if a migration
     * already exists.
     *
     * @access private
     * @param string $migration_number A migration number in the form 001
     * @return string Decremented migration number
     * @author Aziz Light
     **/
    private function decrement_migration_number($migration_number)
    {
        $migration_number = intval($migration_number);
        $migration_number--;

        if ($migration_number < 10)
        {
            $migration_number = '00' . $migration_number;
        }
        else if ($migration_number < 100)
        {
            $migration_number = '0' . $migration_number;
        }
        else
        {
            $migration_number = strval($migration_number);
        }

        return $migration_number;
    }

    /**
     * This method adds the migration number to the config file
     *
     * @access private
     * @param string $migration_number The number of the migration in the form 001
     * @return bool Wether or not the migration number was added to the config file
     * @author Aziz Light
     **/
    private function add_migration_number_to_config_file($migration_number)
    {
        $config_file = $this->args['location'] . '/config/migration.php';
        if (is_file($config_file))
        {
            $config_file_contents = file_get_contents($config_file);
            $config_file_contents = preg_replace('/\$config\[\'migration_version\'\] = \d+;/', '$config[\'migration_version\'] = ' . intval($migration_number) . ';', $config_file_contents, -1, $count);

            if (file_put_contents($config_file, $config_file_contents))
            {
                return TRUE;
            }
            else
            {
                // TODO: Find a clean way to return an error message saying that the migration succeeded but that the migration count could not be incremented in the config file
                return FALSE;
            }

        }
        else
        {
            return FALSE;
        }
    }

    /**
     * Try to extract the table name out of the migration name
     *
     * @access private
     * @return string The guessed table name
     * @author Aziz Light
     **/
    private function get_table_name_out_of_migration_name()
    {
        $patterns = array(
            '/create_(?P<table_name>\w+)$/',
            '/add_\w+_to_(?P<table_name>\w+)$/',
            '/add_(?P<table_name>\w+)$/'
        );

        $table_name = "";
        foreach ($patterns as $pattern)
        {
            if (preg_match($pattern, $this->args['name'], $matches) === 1)
            {
                $table_name = $matches['table_name'];
                break;
            }
        }

        return $table_name;
    }
}
