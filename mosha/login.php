<?php
session_start();

require_once('common/function.php');

require_once('common/db.php');

$errors = array();

if(isset($_SESSION['id'])){
  header('Location: index.php');
  exit();
}

if(isset($_SESSION['token']) && isset($_POST['token']) && $_SESSION['token'] === $_POST['token']){
  if(empty($_POST['mail'])){
    $errors['mail'] = 'メールアドレスを入力してください。';
  }

  if(empty($_POST['password'])){
    $errors['password'] = 'パスワードを入力してください。';
  }

  if(empty($errors)){
    
    $stmt = $db->prepare('SELECT id, password FROM members WHERE mail = ?');
    $stmt->execute(array($_POST['mail']));
    $record = $stmt->fetch(PDO::FETCH_ASSOC);

    if(password_verify($_POST['password'], $record['password'])){
      session_regenerate_id(true);
      $_SESSION['id'] = $record['id'];
      $_SESSION['time'] = time();
      header('Location: index.php');
      exit();
    }else{
      $errors['wrong'] = 'メールアドレスまたはパスワードが間違っています。';
    }
  }
}

$_SESSION['token'] = base64_encode(openssl_random_pseudo_bytes(32));
?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ログイン｜MOSHA</title>
  <link rel="stylesheet" href="css/stylesheet.css">
  <link rel="stylesheet" href="css/responsive.css">
</head>
<body>
  <?php include('common/header.php'); ?>
  <div class="form-box">
    <h2>ログイン</h2>
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
      <input type="text" name="mail" id="mail" value="<?php if(isset($_POST['mail'])){echo h($_POST['mail']);} ?>" autocomplete="email">
      <label for="password">パスワード</label>
      <input type="password" id="password" name="password">
      <input type="hidden" name="token" value="<?php echo $_SESSION['token']; ?>">
      <button type="submit">ログインする</button>
    </form>
    <a href="join/signup_mail.php" class="link">新規登録</a>
    <br>
    <a href="reissue/input_mail.php" class="link">パスワードを忘れた方はこちら</a>
  </div>
  <?php include('common/footer.php'); ?>
</body>
</html>