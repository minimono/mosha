<?php
session_start();

if(empty($_SESSION['id'])){
  header('Location: ../login.php');
  exit();
}

if(isset($_SESSION['time'])){
  if($_SESSION['time'] < time() - 3600){
    header('Location: ../logout.php?action=timeout');
    exit();
  }
  $_SESSION['time'] = time();
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>編集完了｜MOSHA</title>
  <link rel="stylesheet" href="../css/stylesheet.css">
  <link rel="stylesheet" href="../css/responsive.css">
</head>
<body>
  <?php include('../common/header.php'); ?>
  <div class="form-box">
    <p>変更を保存しました。</p>
    <button onclick="location.href='../index.php'">トップに戻る</button>
  </div>
  <?php include('../common/footer.php'); ?>
</body>
</html>