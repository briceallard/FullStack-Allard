<?php

require "../scripts/mongo_helper.php";

if(isset($_GET['img'])){

  header('Content-Type: text/plain');

  // scandir game_images
  $dir = array_slice(scandir("../game_images/"), 2);
  // $dest = imagecreatefrompng("../game_images/".$dir[rand() % sizeof($dir)]);
  // echo imagepng($dest);
  echo explode(".", $dir[rand() % sizeof($dir)])[0];


}

if(isset($_GET['generate'])){

  // use php-gd to place waldo on crowd.jpg
  $dest = imagecreatefromjpeg("../images/crowd.jpg");
  $src = imagecreatefrompng("../waldo_images/waldo_walking_200x451.png");

  $src = imagescale($src, 16);

  $sizes = [imagesx($dest), imagesy($dest)];

  $rx = rand() % $sizes[0];
  $ry = rand() % $sizes[1];
  $game_id = time();

  imagecopy($dest, $src, $rx, $ry, 0, 0, 16, 36);

  $mongo = new mongoHelper("waldos", "coords");

  $mongo->insert([
    [
      "game_id" => $game_id,
      "x" => $rx,
      "y" => $ry
    ]
  ]);

  // header('Content-Type: image/png');
  imagepng($dest, "../game_images/".$game_id.".png");
  echo json_encode(["status" => true], JSON_PRETTY_PRINT);

}

if(isset($_GET['x']) && isset($_GET['y']) && isset($_GET['gid'])){

  $mongo = new mongoHelper("waldos", "coords");
  $games = $mongo->query();

  // $gid = 0;
  foreach($games as $k => $v){
    // foreach($game as $data){
      if($v->game_id == (int)$_GET['gid']){
        $gid = $k;
      }
    // }
  }

  $x = $_GET['x'];
  $y = $_GET['y'];

  echo sqrt(pow($games[$gid]->x - $x,2) + pow($games[$gid]->y - $y,2));

  // if(sqrt(pow($games[$gid]->x - $x,2) + pow($games[$gid]->y - $y,2)) < 75){
  //   echo "found";
  // }

}
