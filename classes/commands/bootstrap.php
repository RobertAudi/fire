<?php if (!defined('BASE_PATH')) exit('No direct script access allowed');

require_once BASE_PATH . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'github_helpers.php';

/**
 * Bootstrap Command
 */
class Bootstrap extends BaseCommand
{
    /**
     * El Constructor!
     *
     * @access public
     * @param array $args Parsed command line arguments
     * @author Aziz Light
     */
    public function __construct(array $args)
    {
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
        fwrite(STDOUT, "Bootstrapping Fire...\n");
        if (GithubHelpers::git_clone($this->get_github_repo_link(), BASE_PATH . '/codeigniter') === FALSE)
        {
            throw new RuntimeException("Unable to clone the sample CodeIgniter project from Github");
        }
        else
        {
            fwrite(STDOUT, "\t" . ApplicationHelpers::colorize('Fire', 'green') . "  Bootstrapped\n");
        }
    }

    /**
     * Get the github repo from the new_project config file
     * and turn it into a link
     *
     * @access private
     * @return string The Github repo link
     * @author Aziz Light
     */
    private function get_github_repo_link()
    {
        $config = parse_ini_file(BASE_PATH . "/config/new_project.ini");
        return 'git://github.com/' . $config['github_repo'] . '.git';
    }
}
