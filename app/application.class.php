<?php
namespace mione;
class Application{
  protected static $autoloader;
  protected static $router;

  private function __construct(){}
  
  public static function init(){
    if( ENV['DEBUG_MODE'] == true ){
      ini_set('display_errors', 1);
      ini_set('display_startup_errors', 1);
      error_reporting(E_ALL);
    }

    self::load_autoloader();

    self::$router = new config\Router;
  }

  protected static function load_autoloader(){
    require_once MIONE_DIR_PATH . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'autoloader.class.php';
    self::$autoloader = new config\Autoloader();
  }
}