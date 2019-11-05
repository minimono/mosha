<?php
session_start();

require_once('../common/function.php');

require_once('../common/db.php');

$errors = array();

if(isset($_SESSION['id'])){
  header('Location: ../index.php');
  exit();
}

if(empty($_GET['urltoken'])){
  header('Location: input_mail.php');
  exit();
}

$stmt = $db->prepare('SELECT mail FROM reset_password WHERE urltoken = ? AND flag = 0 AND date > NOW() - INTERVAL 24 HOUR');
$stmt->execute(array($_GET['urltoken']));
if($stmt->rowCount() == 1){
  $mail_array = $stmt->fetch(PDO::FETCH_ASSOC);
  $mail = $mail_array['mail'];
}else{
  $errors['token'] = 'このURLはご利用できません。有効期限が過ぎた等の問題があります。再設定用メールの送信をやりなおして下さい。';   
}

if(isset($_SESSION['token']) && isset($_POST['token']) && $_SESSION['token'] === $_POST['token']){
  unset($_SESSION['token']);

  if(empty($_POST['password'])){
    $errors['password'] = 'パスワードを入力してください';
  }else if(mb_strlen($_POST['password']) < 8 || mb_strlen($_POST['password']) > 50){
    $errors['password'] = 'パスワードは8文字以上50文字以内で入力してください。';
  }else if(empty($_POST['password-confirm'])){
    $errors['password'] = '確認用パスワードが入力されていません。';
  }else if($_POST['password'] !== $_POST['password-confirm']){
    $errors['password'] = '確認用パスワードが一致していません。';
  }

  if(empty($errors)){
    $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $db->prepare('UPDATE members SET password = ? WHERE mail = ?');
    $stmt->execute(array($pass, $mail));

    $stmt = $db->prepare('UPDATE reset_password SET flag = 1 WHERE mail = ?');
    $stmt->execute(array($mail));
    
    $_SESSION = array();

    if(isset($_COOKIE["PHPSESSID"])) {
      setcookie("PHPSESSID", '', time() - 1800, '/');
    }
    session_destroy();

    header('Location: complete.php');
    exit();
  }
}

$_SESSION['token'] = base64_encode(openssl_random_pseudo_bytes(32));
?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>パスワード再設定｜MOSHA</title>
  <link rel="stylesheet" href="../css/stylesheet.css">
  <link rel="stylesheet" href="../css/responsive.css">
</head>
<body>
  <?php include('../common/header.php'); ?>
  <div class="form-box">
    <?php if(!empty($errors)){
      echo '<ul class="errors">';
      foreach($errors as $error){
        echo '<li>'.$error.'</li>';
      }
      echo '</ul>';
    }
    ?>
    <form action="" method="post">
      <label for="password">パスワード</label>
      <input type="password" name="password" id="password">
      <label for="password-confirm">パスワード(確認)</label>
      <input type="password" name="password-confirm" id="password-confirm">
      <input type="hidden" name="token" value="<?php echo $_SESSION['token']; ?>">
      <button type="submit">再設定する</button>
    </form>
  </div>
  <?php include('../common/footer.php'); ?>
</body>
</html>