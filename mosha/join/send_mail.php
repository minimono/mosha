<?php
session_start();

if(isset($_SESSION['id'])){
  header('Location: ../index.php');
  exit();
}

if(empty($_SESSION['mail']) || empty($_SESSION['urltoken'])){
  header('Location: signup_mail.php');
  exit();
}

$url = 'http://'.$_SERVER['SERVER_NAME'].'/mosha/join/signup.php?urltoken='.$_SESSION['urltoken'];

$mailTo = $_SESSION['mail'];
$returnMail = 'minimono1001@yahoo.co.jp';
$name = 'ミニモノ';
$mail = 'minimono1001@yahoo.co.jp';
$subject = '【MOSHA】会員登録用URLのお知らせ';
$body = <<< EOM
24時間以内に下記のURLから本登録してください。
{$url}

もしこのメールに心当たりがなければお手数ですが破棄してください。
EOM;
mb_language('ja');
mb_internal_encoding('UTF-8');
$header = 'From: '.mb_encode_mimeheader($name).'<'.$mail.'>';
if(mb_send_mail($mailTo, $subject, $body, $header, '-f'.$returnMail)){
  $message = 'メールをお送りしました。<br>24時間以内にメールに記載されたURLからご登録ください。';
}else{
  $message = 'メールの送信に失敗しました。';
}

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
  <title>送信完了｜MOSHA</title>
  <link rel="stylesheet" href="../css/stylesheet.css">
  <link rel="stylesheet" href="../css/responsive.css">
</head>
<body>
  <?php include('../common/header.php'); ?>
  <div class="form-box">
    <p><?php echo $message; ?></p>
  </div>
  <?php include('../common/footer.php'); ?>
</body>
</html>