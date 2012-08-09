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
     * List of valid command aliases and their corresponding command
     *
     * @var array
     * @author Aziz Light
     */
    private static $valid_aliases = array('g' => 'generate');

    /**
     * List of valid subjects
     *
     * @var array
     * @author Aziz Light
     */
    private static $valid_subjects = array('controller', 'model', 'scaffold');

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
        return $process->run();
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
            $args[0] = self::check_and_get_command_alias($args[0]);
            if (empty($args[0]))
            {
                throw new InvalidArgumentException("Invalid task", INVALID_TASK_EXCEPTION);
            }
            else
            {
                # FIXME: Try to remove the duplication here.
                # NOTE: This is a good case for a goto: :-{)
                $parsed_args['command'] = $args[0];
                array_shift($args);
            }
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
            $unparsed_name = array_shift($args);

            // NOTE: I have to use this $tmp variable in order to avoid getting a "Stict Standards" notice by php
            $tmp = explode(DIRECTORY_SEPARATOR, $unparsed_name);
            $parsed_args['name'] = end($tmp);
            $parsed_args['filename'] = ApplicationHelpers::underscorify($unparsed_name) . ".php";
        }

        if (!empty($args))
        {
            $parsed_args['extra'] = $args;
        }

        return $parsed_args;
    }

    /**
     * Check if the given alias is valid.
     * If it is, return the corresponding command,
     * otherwise return an empty string.
     *
     * @param string $alias An alias
     * @return string The corresponding command
     * @access private
     * @author Aziz Light
     */
    private static function check_and_get_command_alias($alias)
    {
        return (array_key_exists($alias, self::$valid_aliases)) ? self::$valid_aliases[$alias] : "";
    }
}
