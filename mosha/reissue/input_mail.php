<?php
session_start();

require_once('../common/function.php');

require_once('../common/db.php');

$errors = array();

if(isset($_SESSION['id'])){
  header('Location: ../index.php');
  exit();
}

if(isset($_POST['token']) && isset($_SESSION['token']) && $_POST['token'] === $_SESSION['token']){
  unset($_SESSION['token']);
  
  if(empty($_POST['mail'])){
    $errors['mail'] = 'メールアドレスを入力してください。';
  }else if(!preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $_POST['mail'])){
    $errors['mail'] = 'メールアドレスが不適切です。';
  }

  if(empty($errors)){
    $stmt = $db->prepare('SELECT COUNT(*) AS cnt FROM members WHERE mail = ?');
    $stmt->execute(array($_POST['mail']));
    $record = $stmt->fetch(PDO::FETCH_ASSOC);
    if($record['cnt'] == 0){
      $errors['mail'] = 'そのメールアドレスは登録されていません。';
    }
  }

  if(empty($errors)){
    $urltoken = hash('sha256',uniqid(mt_rand(),true));
    $stmt = $db->prepare('INSERT INTO reset_password(urltoken, mail, date) VALUES(?, ?, NOW())');
    $stmt->execute(array($urltoken, $_POST['mail']));
    $_SESSION['mail'] = $_POST['mail'];
    $_SESSION['urltoken'] = $urltoken;
    header('Location: send_mail.php');
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
    <p>パスワード再設定用メールを送信します。</p>
    <?php if(!empty($errors)){
      echo '<ul class="errors">';
      foreach($errors as $error){
        echo '<li>'.$error.'</li>';
      }
      echo '</ul>';
    }
    ?>
    <form action="" method="post">
      <label for="mail">メールアドレス</label>
      <input type="text" name="mail" id="mail" value="<?php if(isset($_POST['mail'])){echo h($_POST['mail']);}?>" autocomplete="email">
      <input type="hidden" name="token" value="<?php echo $_SESSION['token']; ?>">
      <button type="submit">送信する</button>
    </form>
  </div>
  <?php include('../common/footer.php'); ?>
</body>
</html>