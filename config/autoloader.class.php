<?php
namespace mione\config;
class Autoloader{
  public function __construct(){
    $this->register_autoload();
  }

  public function camel2dashed( $class_name ) {
    return strtolower( preg_replace( '/([a-zA-Z])(?=[A-Z])/', '$1-', $class_name ) );
  }
  
  public function register_autoload(){
    spl_autoload_register(function ($class_name) {
      if( strpos( $class_name, 'mione') === 0 ){
				$class_path = MIONE_APP_PATH;
				if( strpos( $class_name, 'mione\config') === 0 ){
					$class_path = MIONE_DIR_PATH;
        }
        $class_name = str_replace( '\\', DIRECTORY_SEPARATOR, $class_name );
				$class_name = str_replace( 'mione' . DIRECTORY_SEPARATOR, $class_path, $class_name );
        $path = dirname( $class_name );
				$filename = $this->camel2dashed( basename( $class_name ) ) . '.class.php';
        require_once $path . DIRECTORY_SEPARATOR . $filename;
      }
    });
  }
}