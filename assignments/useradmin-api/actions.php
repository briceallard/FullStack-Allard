<?php

header('Content-Type: application/json');
require './mongodb.helper.php';

/**
 * API
 * @author - Tom Johnson
 * @description - processes actions from the index
 */

class API extends mongo {

  public function __construct(){

    $this->mongo = new mongo("mongo_tests", "users");

    $this->method = $_SERVER['REQUEST_METHOD'];

  }

  /**
   * add_user - create a new document
   *
   * @params:
   *     n/a
   * @returns:
   *     n/a
   */

  public function add_user(){
    $query = $this->mongo->query();
    $newId = $query[count($query)-1]->_id+1;
    $this->mongo->insert([
      [
        '_id' => $newId,
        'first' => $_POST['first'],
        'last' => $_POST['last'],
        'email' => $_POST['email'],
        'age' => $_POST['age']
      ]
    ]);
    echo json_encode(["status" => true, "_id" => $newId],JSON_PRETTY_PRINT);
  }

  /**
   * update_user - create a new document
   *
   * @params:
   *     n/a
   * @returns:
   *     n/a
   */

  public function update_user(){
    $this->mongo->update(["_id" => intval($_POST['_id'])],[
      'first' => $_POST['first'],
      'last' => $_POST['last'],
      'email' => $_POST['email'],
      'age' => $_POST['age']
    ]);
    echo json_encode(["status" => true],JSON_PRETTY_PRINT);
  }

  /**
   * delete_user - create a new document
   *
   * @params:
   *     n/a
   * @returns:
   *     n/a
   */

  public function delete_user(){
    if(!isset($_POST['_id'])){
      $this->mongo->delete();
    } else {
      $this->mongo->delete([["_id" => intval($_POST['_id'])]]);
    }
    echo json_encode(["status" => true],JSON_PRETTY_PRINT);
  }

  /**
   * processApi - listen for methods
   *
   * @params:
   *     n/a
   * @returns:
   *     n/a
   */

  public function processApi(){

    if($this->method == "POST"){

      if($_POST['__method'] == "PUT")
        $this->add_user();
      if($_POST['__method'] == "PATCH")
        $this->update_user();
      if($_POST['__method'] == "DELETE")
        $this->delete_user();

    }

    if($this->method == "GET"){
      echo json_encode(["status" => "go away."], JSON_PRETTY_PRINT);
    }

  }

}

$api = new Api;
$api->processApi();
