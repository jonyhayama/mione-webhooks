<?php
namespace mione\services;
use \mione\config\Database as DB;

class Github extends Service{
  public function run(){
    $sql = 'INSERT INTO hooks(service, payload) VALUES ( :service, :payload )';
    $stmt = DB::prepare( $sql );
    $stmt->execute( [':service' => 'github', ':payload' => $this->getPayload()] );
    return true;
  }
}