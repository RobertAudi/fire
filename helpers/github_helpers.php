<?php if (!defined('BASE_PATH')) exit('No direct script access allowed');

/**
 * Github Helpers
 */
class GithubHelpers
{
    private function __construct() {}

    /**
     * Clone the Codeigniter repository
     *
     * @access public
     * @param string $repo The git repository url
     * @return boolean TRUE/FALSE depending on wheter the repo was cloned or not
     * @author Aziz Light
     */
    public static function git_clone($repo, $location)
    {
        exec('git clone ' . $repo . ' ' . $location . ' > /dev/null 2>&1 && echo "CLONED" || echo "ERROR"', $output);
        if ($output[0] === "CLONED")
        {
            // delete the .git directory
            ApplicationHelpers::delete_git_dir($location . '/.git');
            return TRUE;
        }
        else if ($output[0] == "ERROR")
        {
            return FALSE;
        }
    }
}
