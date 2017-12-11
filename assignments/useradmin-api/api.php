<?php

require './mongodb.helper.php';

$mongo = new mongo("mongo_tests", "users");

$users["data"] = json_decode(json_encode($mongo->query()),1);

if(empty($users["data"])){
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, "https://randomuser.me/api/?results=100");
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  $results = json_decode(curl_exec($ch),1);
  curl_close($ch);

  $_id = 1;

  foreach($results["results"] as $user){

    $age = round((time()-strtotime($user["dob"]))/60/60/24/365);

    $users["data"][] = [
      "_id" => $_id,
      "first" => $user["name"]["first"],
      "last" => $user["name"]["last"],
      "email" => $user["email"],
      "age" => $age,
      "nat" => $user["nat"]
    ];

    $_id++;

  }

  $mongo->insert($users["data"]);

}

echo json_encode($users,JSON_PRETTY_PRINT);
