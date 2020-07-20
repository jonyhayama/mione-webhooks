<?php
namespace mione\config;

class Router{
  public function __construct(){
    $this->route();
  }

  public function route(){
    $path = substr( $_SERVER['REQUEST_URI'], strlen( ENV['BASEPATH'] ), strlen( $_SERVER['REQUEST_URI'] ) );
    $class = '\mione\\' . str_replace( '/', '\\', rtrim( $path, '/' ) );
    
    header('Content-Type: application/json');

    $controller = new $class();
    $response = $controller->run();
    echo ( $response ) ? json_encode($response) : '';
  }
}