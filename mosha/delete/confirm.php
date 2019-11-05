<?php
session_start();

require_once('../common/function.php');

require_once('../common/db.php');

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

if(empty($_GET['id'])){
  header('Location: ../index.php');
  exit();
}

$stmt = $db->prepare('SELECT title FROM websites WHERE id = ? AND member_id = ?');
$stmt->execute(array($_GET['id'], $_SESSION['id']));
if($stmt->rowCount() === 1) {
  $record = $stmt->fetch(PDO::FETCH_ASSOC);
}else{
  header('Location: ../index.php');
  exit();
}

if(isset($_SESSION['token']) && isset($_POST['token']) && $_SESSION['token'] === $_POST['token']){
  unset($_SESSION['token']);

  try{
    $db->beginTransaction();
    try{
      $stmt = $db->prepare('DELETE FROM favorites WHERE website_id = ?');
      $stmt->execute(array($_GET['id']));
      
      $stmt = $db->prepare('DELETE FROM websites WHERE id = ? AND member_id = ?');
      $stmt->execute(array($_GET['id'], $_SESSION['id']));

      $db->commit();
    }catch(PDOException $e){
      $db->rollback();
      throw $e;
    }
  }catch(PDOException $e){
    echo $e->getMessage();
  }

  header('Location: complete.php');
  exit();
}

$_SESSION['token'] = base64_encode(openssl_random_pseudo_bytes(32));
?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>削除｜MOSHA</title>
  <link rel="stylesheet" href="../css/stylesheet.css">
  <link rel="stylesheet" href="../css/responsive.css">
</head>
<body>
  <?php include('../common/header.php'); ?>
  <div class="delete-confirm">
    <div class="main-box">
      <h2>本当に削除しますか？</h2>
      <p class="title">サイト名：<?php echo h($record['title']); ?></p>
      <form action="" method="post">
        <input type="hidden" name="token" value="<?php echo $_SESSION['token']; ?>">
        <button class="danger" type="submit">削除する</button>
      </form>
    </div>
  </div>
  <?php include('../common/footer.php'); ?>
</body>
</html>