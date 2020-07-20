<?php
namespace mione\services;

abstract class Service{
  public function getPayload(){
    return file_get_contents('php://input');
  }
  public function getJsonPayload(){
    return json_decode($this->getPayload());
  }
  public function run(){
    return '';
  }
}