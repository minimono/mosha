<?php
session_start();

if(empty($_SESSION['id'])){
  header('Location: ../index.php');
  exit();
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>投稿完了｜MOSHA</title>
  <link rel="stylesheet" href="../css/stylesheet.css">
  <link rel="stylesheet" href="../css/responsive.css">
</head>
<body>
  <?php include('../common/header.php'); ?>
  <div class="form-box">
    <p>投稿が完了しました！</p>
    <button onclick="location.href='../index.php'">トップへ戻る</button>
  </div>
  <?php include('../common/footer.php'); ?>
</body>
</html>