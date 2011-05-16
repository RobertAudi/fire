<?php if (!defined('BASE_PATH')) exit('No direct script access allowed');

/**
* Inferno, the master class.
*/
class Inferno
{
    /**
     * List of valid arguments
     *
     * @var array
     * @author Aziz Light
     */
    private static $valid_tasks = array('generate');

    /**
     * List of valid subjects
     *
     * @var array
     * @author Aziz Light
     */
    private static $valid_subjects = array('controller', 'model', 'view');

    // Prevent from instantiating the class.
    function __construct()
    {
        throw new RuntimeException("The Fire class can not be instantiated");
    }

    // TODO: add documentation
    public static function init($args)
    {
        if (!is_array($args))
        {
            throw new InvalidArgumentException('Argument 1 passed to Inferno::init() must be an array');
        }

        $location = FolderScanner::check_location();
        if (!$location)
        {
            $error_message  = "No CodeIgniter project detected at your location.\n"
                            . "You must either be in the root or the application folder"
                            . " of a CodeIgniter project!";

            throw new RuntimeException($error_message);
        }

        // Parse the arguments
        $args = self::parse($args);

        // FIXME: Make this more generic
        // Get the config
        $config = parse_ini_file(BASE_PATH . "/config/{$args["command"]}.ini");

        // Add the location to the configuration array.
        $config["location"] = $location;

        // Merge the config and args arrays
        $args = array_merge($config, $args);

        // Example: new Generate()
        $command_class = ucfirst(strtolower($args['command']));

        // Remove the command from the array.
        unset($args["command"]);

        // Finally, call and run the command.
        $process = new $command_class($args);
        return (bool) $process->run();
    }

    /**
     * Get help
     *
     * @param string $spec Name of the specific help text you want to get
     * @return string The help text
     * @author Aziz Light
     */
    public static function help($spec = "")
    {
        if ($spec == "")
        {
            $spec == "main";
        }

        return call_user_func("Help::" . $spec);
    }

    /**
     * Parse the args list retrieved from the command line
     *
     * @param array $args Argument list
     * @return array Parsed argument list
     * @access private
     * @author Aziz Light
     */
    private static function parse($args)
    {
        $parsed_args = array();

        // remove the script name from the commands list.
        array_shift($args);

        if (!empty($args) && in_array($args[0], self::$valid_tasks))
        {
            $parsed_args['command'] = $args[0];
            array_shift($args);
        }
        else
        {
            throw new InvalidArgumentException("Invalid task", INVALID_TASK_EXCEPTION);
        }

        // TODO: Find a better name than "subject"
        // TODO: Try to remove duplication.
        if (!empty($args) && in_array($args[0], self::$valid_subjects))
        {
            $parsed_args['subject'] = $args[0];
            array_shift($args);
        }
        else
        {
            throw new InvalidArgumentException("Invalid subject", INVALID_SUBJECT_EXCEPTION);
        }

        if (empty($args))
        {
            throw new InvalidArgumentException("Missing name", MISSING_NAME_EXCEPTION);
        }
        else
        {
            $parsed_args['name'] = array_shift($args);
            $parsed_args['filename'] = ApplicationHelpers::underscorify($parsed_args['name']) . ".php";
        }

        if (!empty($args))
        {
            $parsed_args['extra'] = $args;
        }

        return $parsed_args;
    }

}
