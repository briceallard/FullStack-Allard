<?php

/**
 * MongoDB helper
 * @author - Tom Johnson
 * @description - a less messy mongo helper.
 */

ini_set("display_errors", "-1");
//
class mongo {

  public function __construct($db=null,$col=null){

    $this->db = $db;
    $this->collection = $col;
    $this->target = $this->db.'.'.$this->collection;

    $this->mgnr = new MongoDB\Driver\Manager("mongodb://localhost:27017");

  }

  /**
   * insert - inserts an array of documents into the db
   *
   * @params:
   *     $docs array : associative array, or array of associative arrays, of documents
   * @returns:
   *     ids array : array of document id's that got inserted
   */

  public function insert($docs=[]){

    $_ids = [];

    if(is_array($docs)){

      $bulk = new MongoDB\Driver\BulkWrite;

      foreach($docs as $doc){
        $_ids[] = $bulk->insert($doc);
      }

      $result = $this->mgnr->executeBulkWrite($this->target, $bulk);

      return $_ids;

    }

    return false;

  }

  /**
   * delete - deletes documents matching the array or deletes them all
   *
   * @params:
   *     $docs array : documents
   * @returns:
   *     integer : amount of deleted documents
   */

  public function delete($docs=null){

    $bulk = new MongoDB\Driver\BulkWrite;

    if(isset($docs) && is_array($docs)){

      foreach($docs as $doc){

        if(array_key_exists('_id',$doc) && strlen($doc['_id']) >= 24){
            $doc['_id'] = new MongoDB\BSON\ObjectID($doc['_id']);
        }

        $bulk->delete($doc);

      }

    } else {
      $bulk->delete([]);
    }

    $result = $this->mgnr->executeBulkWrite($this->target, $bulk);
    return $result->getDeletedCount();

  }

  /**
   * query - selects documents from database
   *
   * @params:
   *     $docs array : documents
   * @returns:
   *     $results array : documents returned from the query
   */

  public function query($docs=[]){

    $results = [];

    // if(array_key_exists('_id',$docs) && strlen($docs['_id']) >= 24){
    //     $docs['_id'] = new MongoDB\BSON\ObjectID($docs['_id']);
    // }

    $options = [
      'sort' => [
        '_id' => 1
      ]
    ];

    $query = new MongoDB\Driver\Query($docs, $options);

    $cursor = $this->mgnr->executeQuery($this->target, $query);

    foreach($cursor as $result){
      $results[] = $result;
    }
    return $results;

  }

  /**
   * update - updates documents
   *
   * @params:
   *     $doc array : doc to update
   *     $set array : values to update $doc
   * @returns:
   *     $results array : documents returned from the query
   */

   public function update($doc,$set){

     $bulk = new MongoDB\Driver\BulkWrite;

     $bulk->update($doc,['$set' => $set],['multi'=>false,'upsert'=>false]);

     $result = $this->mgnr->executeBulkWrite($this->target, $bulk);

     return $result;

   }

}
