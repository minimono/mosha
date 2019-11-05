<?php
session_start();

$_SESSION = array();
if(isset($_COOKIE["PHPSESSID"])){
  setcookie("PHPSESSID", '', time() - 1800, '/');
}
session_destroy();
?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ログアウト｜MOSHA</title>
  <link rel="stylesheet" href="css/stylesheet.css">
  <link rel="stylesheet" href="css/responsive.css">
</head>
<body>
  <?php include('common/header.php'); ?>
  <div class="form-box">
    <p>
      <?php
      if(isset($_GET['action'])){
        if($_GET['action'] === 'timeout'){
          echo '長時間操作が行われなかったためログアウトされました。';
        }else if($_GET['action'] === 'unsubscribe'){
          echo '退会が完了しました';
        }
      }else{
        echo 'ログアウトしました。';
      }
      ?>
    </p>
    <button onclick="location.href='login.php'">ログインする</button>
  </div>
  <?php include('common/footer.php'); ?>
</body>
</html>