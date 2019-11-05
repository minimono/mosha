<?php
session_start();

require_once('common/function.php');

require_once('common/db.php');

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

$stmt = $db->prepare('SELECT name, mail FROM members WHERE id = ?');
$stmt->execute(array($_SESSION['id']));
$record = $stmt->fetch(PDO::FETCH_ASSOC);

if(isset($_SESSION['token']) && isset($_POST['token']) && $_SESSION['token'] === $_POST['token']){
  unset($_SESSION['token']);

  try{
    $db->beginTransaction();
    try{
      $stmt = $db->prepare('DELETE FROM pre_members WHERE mail = ?');
      $stmt->execute(array($record['mail']));

      $stmt = $db->prepare('DELETE FROM favorites WHERE member_id = ?');
      $stmt->execute(array($_SESSION['id']));
      
      $stmt = $db->prepare('DELETE FROM websites WHERE member_id = ?');
      $stmt->execute(array($_SESSION['id']));

      $stmt = $db->prepare('DELETE FROM reset_password WHERE mail = ?');
      $stmt->execute(array($record['mail']));

      $stmt = $db->prepare('DELETE FROM members WHERE id = ?');
      $stmt->execute(array($_SESSION['id']));

      $db->commit();
    }catch(PDOException $e){
      $db->rollback();
      throw $e;
    }
  }catch(PDOException $e){
    echo $e->getMessage();
  }
  
  header('Location: logout.php?action=unsubscribe');
  exit();
}

$_SESSION['token'] = base64_encode(openssl_random_pseudo_bytes(32));
?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>退会｜MOSHA</title>
  <link rel="stylesheet" href="css/stylesheet.css">
  <link rel="stylesheet" href="css/responsive.css">
</head>
<body>
  <?php include('common/header.php'); ?>
  <div class="unsubscribe">
    <div class="main-box">
      <h2>本当に退会しますか？</h2>
      <p class="name">ニックネーム：<?php echo h($record['name']); ?></p>
      <p class="notice">退会した場合は投稿した内容も削除されます。</p>
      <form action="" method="post">
        <input type="hidden" name="token" value="<?php echo $_SESSION['token']; ?>">
        <button class="danger" type="submit">退会する</button>
      </form>
    </div>
  </div>
  <?php include('common/footer.php'); ?>
</body>
</html>