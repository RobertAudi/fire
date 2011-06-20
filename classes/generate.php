<?php if (!defined('BASE_PATH')) exit('No direct script access allowed');

/**
* Generate task
*/
class Generate
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
    private function controller($force_views_creation = false)
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
            $message  = ApplicationHelpers::colorize($message, 'light_blue');
            $message .= $this->args['application_folder'] . '/controllers/' . $this->args['filename'];
        }
        elseif (file_put_contents($filename, $controller))
        {
            $message .= 'Created controller: ';
            $message  = ApplicationHelpers::colorize($message, 'green');
            $message .= $this->args['application_folder'] . '/controllers/' . $this->args['filename'];
        }
        else
        {
            $message .= 'Unable to create controller: ';
            $message  = ApplicationHelpers::colorize($message, 'red');
            $message .= $this->args['application_folder'] . '/controllers/' . $this->args['filename'];
        }



        if ($force_views_creation === true || $this->should_we_generate_views())
        {
            fwrite(STDOUT, $message . PHP_EOL);

            // Create the view files.
            $this->views();
        }
        else
        {
            fwrite(STDOUT, $message . PHP_EOL);
        }

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
            "class_name"         => $this->args['name'],
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
            $message  = ApplicationHelpers::colorize($message, 'light_blue');
            $message .= $this->args['application_folder'] . '/models/' . $this->args['filename'];
        }
        elseif (file_put_contents($filename, $model))
        {
            $message .= 'Created model: ';
            $message  = ApplicationHelpers::colorize($message, 'green');
            $message .= $this->args['application_folder'] . '/models/' . $this->args['filename'];
        }
        else
        {
            $message .= 'Unable to create model: ';
            $message  = ApplicationHelpers::colorize($message, 'red');
            $message .= $this->args['application_folder'] . '/models/' . $this->args['filename'];
        }

        fwrite(STDOUT, $message . PHP_EOL);
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
        $views_folder = $this->args['application_folder'] . '/views/' . $controller;
        $location = $this->args['location'] . '/views/' . $controller;
        if (!file_exists($location) || !is_dir($location))
        {
            $message = "\t";
            if (mkdir($location, 0755))
            {
                $message .= 'Created folder: ';
                $message  = ApplicationHelpers::colorize($message, 'green') . $views_folder;
                fwrite(STDOUT, $message . PHP_EOL);
                unset($message);
            }
            else
            {
                $message .= 'Unable to create folder: ';
                $message  = ApplicationHelpers::colorize($message, 'red') . $views_folder;
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
                $message = ApplicationHelpers::colorize($message, 'light_blue') . $views_folder . '/' . $view . '.php';
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
                $message  = ApplicationHelpers::colorize($message, 'green') . $views_folder . '/' . $view . '.php';
            }
            else
            {
                $message .= 'Unable to create view ';
                $message  = ApplicationHelpers::colorize($message, 'red') . $views_folder . '/' . $view . '.php';
            }

            fwrite(STDOUT, $message . PHP_EOL);
            unset($message);
        }

        return true;
    }

    private function scaffold()
    {
        if (isset($this->args['extras']))
        {
            $message = "The following arguments were ignored: ";
            fwrite(STDOUT, ApplicationHelpers::colorize($message, 'red') . implode(", ", $this->args['extra']) . PHP_EOL);
            unset($this->args['extra']);
        }

        $this->args['extra'] = array('index', 'create', 'view', 'edit', 'delete');

        $this->controller(true);

        $this->model();
    }

    /**
     * Asks the user if he wants to generate views
     * Any response starting with the letter "Y" (case-insensitive)
     * is considered positive. Any other response is considered negative.
     *
     * @access private
     * @return bool
     */
    private function should_we_generate_views()
    {
        fwrite(STDOUT, 'Do you want to create views? ');
        $generate_views = trim(fgets(STDIN));

        return strncasecmp($generate_views, 'y', 1) === 0;
    }

    /**
     * Generate the extra stuff that will go in the controller or model.
     * That is, methods and actions.
     *
     * @access private
     * @param  array $args : Extra stuff to generate.
     * @return string : The generated extra stuff.
     * @author Aziz Light
     */
    private function generate_extra(array $args)
    {
        $extra = "";

        if (!array_key_exists('extra', $args) || !is_array($args['extra']) || empty($args['extra']))
        {
            return $extra;
        }

        foreach ($args['extra'] as $arg)
        {
            $arguments = array(
                "class_name" => $args['name'],
                "extra" => $arg
            );
            $template = new TemplateScanner("extra", $arguments);
            $extra   .= $template->parse();
        }
        return $extra;
    }

}
