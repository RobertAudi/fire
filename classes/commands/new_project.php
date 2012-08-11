<?php if (!defined('BASE_PATH')) exit('No direct script access allowed');

require_once BASE_PATH . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'github_helpers.php';

/**
 * The new_project command
 */
class NewProject extends BaseCommand
{
    /**
     * The name of the new project
     *
     * @access private
     * @var string
     */
    private $name;

    /**
     * The location where the new project will reside
     *
     * @access private
     * @var string
     */
    private $location;

    /**
     * The Github repo to clone from
     *
     * @access private
     * @var string
     */
    private $repo;

    /**
     * El Constructor!
     *
     * @access public
     * @param array $args Parsed command line arguments
     * @author Aziz Light
     **/
    public function __construct(array $args)
    {
        $this->name = $args['name'];
        $this->location = getcwd() . DIRECTORY_SEPARATOR . $args['name'];
        $this->repo = 'git://github.com/' . $args['github_repo'] . '.git';

        // Check that git is installed
        exec('which git > /dev/null 2>&1 && echo "FOUND" || echo "NOT_FOUND"', $output);
        if ($output[0] === "NOT_FOUND")
        {
            throw new RuntimeException("Git is required to create a new CodeIgniter project");
        }
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
        // First let's download CodeIgniter
        if (GithubHelpers::git_clone($this->repo, $this->location) === FALSE)
        {
            throw new RuntimeException("Unable to clone CodeIgniter from Github");
        }
        else
        {
            fwrite(STDOUT, "\t" . ApplicationHelpers::colorize('CodeIgniter project created', 'green') . ' ' . $this->name . "\n");
        }
    }
}
