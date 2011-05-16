<?php if (!defined('BASE_PATH')) exit('No direct script access allowed');

/**
 * Application Helpers
 */
class ApplicationHelpers
{
  private function __construct() {}

  /**
   * Takes a CamelCased string and returns an underscore separated version.
   *
   * This is from FuelPHP, it is under the MIT License.
   *
   * @param   string  the CamelCased word
   * @param   bool    whether to strtolower the result
   * @return  string  an underscore separated version of $camel_cased_word
   *
   * @author     Dan Horrigan
   * @copyright  2011 Dan Horrigan
   * @license    MIT License
   */
  public static function underscorify($camel_cased_word, $lower = true)
  {
      if ($camel_cased_word === strtoupper($camel_cased_word) or $camel_cased_word === strtolower($camel_cased_word))
      {
          return $camel_cased_word;
      }
      $result = preg_replace('/([A-Z]+)([A-Z])/', '\1_\2', preg_replace('/([a-z\d])([A-Z])/', '\1_\2', strval($camel_cased_word)));
      return $lower ? strtolower($result) : $result;
  }
}
