<?php
namespace mione\config;
class Database{
  protected static $conn = null;

  private function __construct(){}

  public static function getConn(){
    if( self::$conn == null ){
      self::$conn = new \PDO( ENV['DB']['DSN'], ENV['DB']['USER'], ENV['DB']['PASS'] );
    }

    return self::$conn;
  }

  public static function prepare( $sql ){
    $conn = self::getConn();
    return $conn->prepare( $sql );
  }
}