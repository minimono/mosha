<?php
session_start();

require_once('common/function.php');

require_once('common/db.php');

const COLUMN = 3;

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

$stmt = $db->prepare('SELECT name FROM members WHERE id = ?');
$stmt->execute(array($_GET['id']));
$record = $stmt->fetch(PDO::FETCH_ASSOC);

$name = $record['name'];

if(!$name){
  header('Location: index.php');
  exit();
}

$stmt = $db->prepare('SELECT w.title, w.img, w.difficulty, w.advice, w.id, m.name FROM websites w, members m, favorites f WHERE f.member_id = ? AND f.website_id = w.id AND w.member_id = m.id ORDER BY f.date DESC');
$stmt->execute(array($_GET['id']));
$favorites = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $db->prepare('SELECT title, img, difficulty, advice, id FROM websites WHERE member_id = ? ORDER BY date DESC');
$stmt->execute(array($_GET['id']));
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo h($name); ?>｜MOSHA</title>
  <link rel="stylesheet" href="css/stylesheet.css">
  <link rel="stylesheet" href="css/responsive.css">
</head>
<body>
  <?php include('common/header.php'); ?>
  <div class="member">
    <div class="container">
      <h2><?php echo h($name); ?></h2>
      <h3>お気に入り一覧</h3>
      <?php if(!$favorites): ?>
        <p class="none">お気に入りはまだありません。<br>気に入った投稿を見つけたら、お気に入りに保存しましょう！</p>
      <?php else: ?>
        <div class="websites">
          <?php foreach($favorites as $favorite): ?>
            <div class="website">
              <a class="card" href="view.php?id=<?php echo h($favorite['id']); ?>">
                <p class="img-wrap"><img src="<?php echo h($favorite['img']); ?>" alt="<?php echo h($favorite['title']); ?>の画像;"></p>
                <p class="title"><?php echo h($favorite['title']); ?></p>
                <p class="difficulty"><small>難易度：<?php for($i=0; $i<$favorite['difficulty']; $i++){echo '★';} ?></small></p>
                <p class="name"><small>登録者：<?php echo h($favorite['name']); ?></small></p>
                <p class="advice"><?php echo getTrimString(h($favorite['advice']), 100); ?></p>
              </a>
            </div>
          <?php endforeach; ?>
          <?php
          $placeholder = 0;
          for($i=1; $i<COLUMN; $i++){
            if(count($favorites) % COLUMN === $i){
              $placeholder = COLUMN - $i; 
            }
          }
          for($i=0; $i<$placeholder; $i++){
            echo '<div class="dummy"></div>';
          }
          ?>
        </div>
      <?php endif; ?>
      <h3>投稿一覧</h3>
      <?php if(!$posts): ?>
        <p class="none">投稿はまだありません。<br>模写コーディングにおすすめのサイトをぜひ<a href="post/input.php" class="link">こちら</a>から投稿しましょう！</p>
      <?php else: ?>
        <div class="websites">
          <?php foreach($posts as $post): ?>
            <div class="website">
              <a class="card" href="view.php?id=<?php echo h($post['id']); ?>">
                <p class="img-wrap"><img src="<?php echo h($post['img']); ?>" alt="<?php echo h($post['title']); ?>の画像;"></p>
                <p class="title"><?php echo h($post['title']); ?></p>
                <p class="difficulty"><small>難易度：<?php for($i=0; $i<$post['difficulty']; $i++){echo '★';} ?></small></p>
                <p class="advice"><?php echo getTrimString(h($post['advice']), 100); ?></p>
              </a>
              <?php if(isset($_SESSION['id']) && $_SESSION['id'] === $_GET['id']): ?>
                <div class="action">
                  <a href="edit/input.php?id=<?php echo h($post['id']); ?>" class="edit">編集</a>
                  <a href="delete/confirm.php?id=<?php echo h($post['id']); ?>" class="delete">削除</a>
                </div>
              <?php endif; ?>
            </div>
          <?php endforeach; ?>
          <?php
          $placeholder = 0;
          for($i=1; $i<COLUMN; $i++){
            if(count($posts) % COLUMN === $i){
              $placeholder = COLUMN - $i; 
            }
          }
          for($i=0; $i<$placeholder; $i++){
            echo '<div class="dummy"></div>';
          }
          ?>
        </div>
        <?php endif; ?>
      <?php if(isset($_SESSION['id']) && $_SESSION['id'] === $_GET['id']): ?>
      <div class="unsubscribe-btn">
        <button class="danger" onclick="location.href='unsubscribe.php'">退会する</button>
      </div>
      <?php endif; ?>
    </div>
  </div>
  <?php include('common/footer.php'); ?>
</body>
</html>