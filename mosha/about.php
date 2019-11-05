<?php
session_start();

require_once('common/function.php');

require_once('common/db.php');

if(isset($_SESSION['time'])){
  if($_SESSION['time'] < time() - 3600){
    header('Location: logout.php?action=timeout');
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
  <title>MOSHAとは｜MOSHA</title>
  <link rel="stylesheet" href="css/stylesheet.css">
  <link rel="stylesheet" href="css/responsive.css">
</head>
<body>
  <?php include('common/header.php'); ?>
  <div class="about">
    <div class="main-box">
      <h2>MOSHAとは</h2>
      <p>MOSHAは、<strong>模写コーディング(HTMLなどの練習のために既存のサイトを模写すること)におすすめのサイトを登録・共有</strong>できるサービスです。</p>
      <p>自分が模写して楽しかったサイト、成長できたサイトを登録することができます。</p>
      <p>気になるサイトを見つけたら、お気に入りに登録してマイページから見ることもできます。</p>
      <p>また、模写するサイトを探すだけでしたら、ログインせずに使うことができます。</p>
      <p>これまでも、模写におすすめのサイトをまとめたサイトはありましたが、全体的に種類が少ないように感じました。</p>
      <p>MOSHAは、<strong>コーディングにおすすめのサイトを共有する場があればいいな</strong>と思って作成したサービスです。</p>
      <p>もし何か模写コーディングをしたことがあれば、<strong>会員登録をしてサイトを登録</strong>して頂きたいです。</p>
    </div>
  </div>
  <?php include('common/footer.php'); ?>
</body>
</html>