<?php
try{
  $db = new PDO('mysql:dbname=mosha;host=localhost;charset=utf8mb4', 'root', '', array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
}catch(PDOException $e){
  echo 'DB接続エラー：'.$e->getMessage();
  exit();
}

set_exception_handler(function(PDOException $e){
  echo $e->getMessage();
})
?>