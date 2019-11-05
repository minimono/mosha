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
  header('Location: signup_mail.php');
  exit();
}

$stmt = $db->prepare('SELECT mail FROM pre_members WHERE urltoken = ? AND flag = 0 AND date > NOW() - INTERVAL 24 HOUR');
$stmt->execute(array($_GET['urltoken']));
if($stmt->rowCount() === 1){
  $mail_array = $stmt->fetch(PDO::FETCH_ASSOC);
  $mail = $mail_array['mail'];
}else{
  $errors['token'] = 'このURLはご利用できません。有効期限が過ぎた等の問題があります。もう一度仮登録をやりなおして下さい。';   
}

if(isset($_SESSION['token']) && isset($_POST['token']) && $_SESSION['token'] === $_POST['token']){
  unset($_SESSION['token']);
  
  if(empty($_POST['name'])){
    $errors['name'] = 'ニックネームを入力してください。';
  }else if(mb_strlen($_POST['name']) > 20){
    $errors['name'] = 'ニックネームは20文字以内で入力してください。';
  }

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
    $stmt = $db->prepare('SELECT COUNT(*) AS cnt FROM members WHERE name = ?');
    $stmt->execute(array($_POST['name']));
    $record = $stmt->fetch();
    if($record['cnt'] > 0){
      $errors['name'] = 'そのニックネームは既に使用されています。';
    }

    $stmt = $db->prepare('SELECT COUNT(*) AS cnt FROM members WHERE mail = ?');
    $stmt->execute(array($mail));
    $record = $stmt->fetch();
    if($record['cnt'] > 0){
      $errors['mail'] = 'そのメールアドレスは既に登録されています。';
    }
  }

  if(empty($errors)){
    $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);

    try{
      $db->beginTransaction();
      try{
        $stmt = $db->prepare('INSERT INTO members (name, mail, password) VALUES (?, ?, ?)');
        $stmt->execute(array($_POST['name'], $mail, $pass));

        $stmt = $db->prepare('UPDATE pre_members SET flag = 1 WHERE mail = ?');
        $stmt->execute(array($mail));

        $db->commit();
      }catch(PDOException $e){
        $db->rollback();
        throw $e;
      }
    }catch(PDOException $e){
      echo $e->getMessage();
    }      

    $_SESSION = array();

    if(isset($_COOKIE["PHPSESSID"])) {
      setcookie("PHPSESSID", '', time() - 1800, '/');
    }
    session_destroy();

    header('Location: thanks.php');
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
  <title>本登録画面｜MOSHA</title>
  <link rel="stylesheet" href="../css/stylesheet.css">
  <link rel="stylesheet" href="../css/responsive.css">
</head>
<body>
  <?php include('../common/header.php'); ?>
  <div class="form-box">
    <h2>本登録画面</h2>
    <?php if(!empty($errors)){
      echo '<ul class="errors">';
      foreach($errors as $error){
        echo '<li>'.$error.'</li>';
      }
      echo '</ul>';
    }
    ?>
    <form action="" method="post">
      <label for="name">ニックネーム</label>
      <input type="text" name="name" id="name" value="<?php if(isset($_POST['name'])){echo h($_POST['name']);} ?>">
      <label for="password">パスワード</label>
      <input type="password" name="password" id="password">
      <label for="password-confirm">パスワード(確認)</label>
      <input type="password" name="password-confirm" id="password-confirm">
      <input type="hidden" name="token" value="<?php echo $_SESSION['token']; ?>">
      <button type="submit">登録する</button>
    </form>
  </div>
  <?php include('../common/footer.php'); ?>
</body>
</html>