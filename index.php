<?php

require 'config.env.php';

define( 'MIONE_DIR_PATH', __DIR__ . DIRECTORY_SEPARATOR );
define( 'MIONE_APP_PATH', MIONE_DIR_PATH . 'app' . DIRECTORY_SEPARATOR );

require( MIONE_APP_PATH . 'application.class.php' );

mione\Application::init();

exit;

function parseHook() {
  $request_uri = $_SERVER['REQUEST_URI'];
  $regex = str_replace( '/', '\/', ENV['BASEPATH'] );
  if( preg_match("/$regex(.*)/", $request_uri, $matches) && isset($matches[1]) ){
    return rtrim($matches[1], '/');
  }
  return false;
}

function currentHook(){
  static $hook = null;
  if( $hook === null ){
    $hook = parseHook();
  }
  return $hook;
}

function hookExists( $name ){
  return $name == 'github';
}

function getPayload(){
  switch( currentHook() ){
    case 'github':
      return json_decode(file_get_contents('php://input'));
  }
}

if( hookExists( currentHook() ) ){
  $payload = getPayload();
  $response = [ 
    'hello' => $_SERVER['REQUEST_URI'], 
    'env' => ENV, 
    'matches' => currentHook(),
    'payload' => $payload,
  ];
} else {
  http_response_code(404);
  $response = '';
}

header('Content-Type: application/json');
echo ( $response ) ? json_encode($response) : '';