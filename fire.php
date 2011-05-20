<?php

define('ENVIRONMENT', 'development');
switch (ENVIRONMENT)
{
    case 'development':
        error_reporting(-1);
        ini_set('display_errors', 1);
    break;

    case 'production':
        error_reporting(0);
    break;

    default:
        exit('The application environment is not set correctly.');
}

define('BASE_PATH', __DIR__);

require_once "config/constants.php";
require_once "helpers/application_helpers.php";

foreach (glob(BASE_PATH . "/classes/*.php") as $php_class)
{
    require_once $php_class;
}

try
{
    $message = Inferno::init($argv);
    if ($message)
    {
        // TODO: add a feedback message to tell the user that the file was generated successfully
        echo $message;
    }
    exit;
}
catch (InvalidArgumentException $e)
{
    if ($e->getCode() == INVALID_TASK_EXCEPTION)
    {
        // TODO: create more help files.
        fwrite(STDOUT, Inferno::help("main"));
        exit;
    }
    elseif ($e->getCode() == INVALID_SUBJECT_EXCEPTION)
    {
        fwrite(STDOUT, Inferno::help("main"));
        exit;
    }
    elseif ($e->getCode() == MISSING_NAME_EXCEPTION)
    {
        fwrite(STDOUT, "Please enter a name:\n");
        $name = trim(fgets(STDIN));
        if (empty($name))
        {
            fwrite(STDOUT, "The name is required!\n");
            exit;
        }
        else
        {
            // retry to run the script.
            Inferno::init(array_merge($argv, array($name)));
            exit;
        }
    }
    else
    {
        //FIXME: Handle this better!
        fwrite(STDOUT, $e->getMessage() . "\n");
        exit;
    }
}
catch (RuntimeException $e)
{
    //FIXME: Handle this better!
    fwrite(STDOUT, $e->getMessage() . "\n");
    exit;
}

/* End of file fire.php */
