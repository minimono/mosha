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

if(empty($_GET['id'])){
  header('Location: index.php');
  exit();
}


$stmt = $db->prepare('SELECT w.member_id, w.url, w.title, w.img, w.difficulty, w.advice, m.name, m.id FROM websites w, members m WHERE w.member_id = m.id AND w.id = ?');
$stmt->execute(array($_GET['id']));
if($stmt->rowCount() === 1){
  $record = $stmt->fetch(PDO::FETCH_ASSOC);
}else{
  header('Location: index.php');
  exit();
}

if(isset($_SESSION['id'])){
  if(isset($_SESSION['token']) && isset($_POST['token']) && $_SESSION['token'] === $_POST['token']){
    unset($_SESSION['token']);

    if($_POST['action'] === 'favor'){
      $alert = 'お気に入りに追加しました！';
      $stmt = $db->prepare('INSERT favorites (member_id, website_id, date) VALUES (?, ?, NOW())');
      $stmt->execute(array($_SESSION['id'], $_GET['id']));
    }else{
      $alert = 'お気に入りから削除しました！';
      $stmt = $db->prepare('DELETE FROM favorites WHERE member_id = ? AND website_id = ?');
      $stmt->execute(array($_SESSION['id'], $_GET['id']));
    }
  }
  $stmt = $db->prepare('SELECT COUNT(*) AS cnt FROM favorites WHERE member_id = ? AND website_id = ?');
  $stmt->execute(array($_SESSION['id'], $_GET['id']));
  $result = $stmt->fetch(PDO::FETCH_ASSOC);
  if($result['cnt'] > 0){
    $isfavored = true;
  }else{
    $isfavored = false;
  }
}

$thisurl = (empty($_SERVER["HTTPS"]) ? "http://" : "https://").$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
$thistitle = $record['title'].'｜MOSHA';

$_SESSION['token'] = base64_encode(openssl_random_pseudo_bytes(32));
?>

<!DOCTYPE html>
<html lang="ja">
<head prefix="og: http://ogp.me/ns# fb: http://ogp.me/ns/fb# article: http://ogp.me/ns/article#">
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo h($thistitle); ?></title>
  <meta property="og:url" content="<?php echo h($thisurl); ?>">
  <meta property="og:title" content="<?php echo h($thistitle); ?>">
  <meta property="og:type" content="article">
  <meta property="og:description" content="<?php echo h($record['advice']); ?>">
  <meta property="og:site-name" content="<?php echo h($thistitle); ?>">
  <meta property="og:image" content="<?php echo h((empty($_SERVER["HTTPS"]) ? "http://" : "https://").$_SERVER["HTTP_HOST"])?>/mosha/img/screenshot.png">
  <meta name="twitter:card" content="summary">
  <meta property="fb:app_id" content="585068052231455">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.10.0/css/all.css">
  <link rel="stylesheet" href="css/stylesheet.css">
  <link rel="stylesheet" href="css/responsive.css">
</head>
<body>
  <?php include('common/header.php'); ?>
  <div class="view">
    <div class="main-box">
      <?php if(isset($alert)): ?>
        <p class="alert"><?php echo $alert; ?></p>
      <?php endif; ?>
      <h2><a href="<?php echo h($record['url']); ?>"><?php echo h($record['title']); ?></a></h2>
      <p class="name"><small>登録者：<a href="member.php?id=<?php echo h($record['id']); ?>" class="link"><?php echo h($record['name']); ?></a></small></p>
      <p class="img-wrap"><img src="<?php echo h($record['img']); ?>" alt="<?php h($record['title']); ?>の画像"></p>
      <p class="url">URL：<a href="<?php echo h($record['url']); ?>" class="url-link"><?php echo h($record['url']); ?></a></p>
      <p class="difficulty">難易度：<?php for($i=0; $i<$record['difficulty']; $i++){echo '★';} ?></p>
      <p class="advice">アドバイス：<?php echo nl2br(h($record['advice'])); ?></p>
      <?php if(isset($_SESSION['id'])): ?>
        <form action="?id=<?php echo h($_GET['id']); ?>" method="post">
          <input type="hidden" name="token" value="<?php echo $_SESSION['token']; ?>">
          <?php if(!$isfavored): ?>
            <input type="hidden" name="action" value="favor">
            <button type="submit" class="favorite">★お気に入りに追加する</button>
          <?php else: ?>
            <input type="hidden" name="action" value="unfavor">
            <button type="submit" class="favorite">★お気に入りから削除する</button>
          <?php endif; ?>
        </form>
      <?php endif; ?>
      <div class="sns">
        <a class="btn-twitter" href="https://twitter.com/share?url=<?php echo h($thisurl); ?>&text=<?php echo h($thistitle); ?>" rel="nofollow" target="_blank">Twitter</a>
        <a class="btn-facebook" href="https://www.facebook.com/share.php?u=<?php echo h($thisurl); ?>" rel="nofollow" target="_blank">Facebook</a>
      </div>
      <?php if(isset($_SESSION['id']) && $_SESSION['id'] === $record['member_id']): ?>
        <div class="action">
          <a href="edit/input.php?id=<?php echo h($_GET['id']); ?>" class="edit">編集</a>
          <a href="delete/confirm.php?id=<?php echo h($_GET['id']); ?>" class="delete">削除</a>
        </div>
      <?php endif; ?>
    </div>
  </div>
  <?php include('common/footer.php'); ?>
</body>
</html>