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
    public function __construct($args)
    {
        if (!is_array($args))
        {
            throw new InvalidArgumentException("The commands take an array as only argument!");
        }

        $this->extra = $this->generate_extra($args["extra"]);
        unset($args['extra']);

        $this->args  = $args;
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
        $subject = $this->$subject();

        $location = "";
        switch ($this->args['subject'])
        {
            case 'controller':
                $location = $this->args["location"] . "/controllers/";
                break;
        }

        // Now we need to write the content to a file.
        $filename = $location . $this->args['filename'];
        return file_put_contents($filename, $subject);
    }

    /**
     * The method that generates the controller
     *
     * @access private
     * @return string
     * @author Aziz Light
     */
    private function controller()
    {
        $args = array(
            "class_name" => $this->args['name'],
            "filename" => $this->args['filename'],
            "application_folder" => $this->args['application_folder'],
            "extra" => $this->extra,
        );
        $template = new TemplateScanner("controller", $args);
        return $template->parse();
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
    private function generate_extra($args)
    {
        if (!is_array($args))
        {
            throw new InvalidArgumentException("Invalid argument. An array should be passed.");
        }

        $extra = "";
        foreach ($args as $arg)
        {
            $args = array(
                "extra" => $arg
            );
            $template = new TemplateScanner("extra", $args);
            $extra   .= $template->parse();
        }
        return $extra;
    }

}
