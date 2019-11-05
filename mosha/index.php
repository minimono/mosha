<?php
session_start();

require_once('common/function.php');

require_once('common/db.php');

const COLUMN = 3;
const MAXNUM = 9;

if(isset($_SESSION['time'])){
  if($_SESSION['time'] < time() - 3600){
    header('Location: logout.php?action=timeout');
    exit();
  }
  $_SESSION['time'] = time();
}

$stmt = $db->query('SELECT COUNT(*) AS cnt FROM websites');
$record = $stmt->fetch(PDO::FETCH_ASSOC);

$maxpage = max(ceil($record['cnt'] / MAXNUM), 1);
if(isset($_GET['page'])){
  $page = intval($_GET['page']);
}else{
  $page = 1;
}
$page = max($page, 1);
$page = min($page, $maxpage);
$start = ($page - 1) * MAXNUM;

$stmt = $db->prepare('SELECT w.id, w.url, w.title, w.img, w.difficulty, w.advice, m.name FROM websites w, members m WHERE w.member_id = m.id ORDER BY date DESC LIMIT :start, :maxnum');
$stmt->bindParam(':start', $start, PDO::PARAM_INT);
$stmt->bindValue(':maxnum', MAXNUM, PDO::PARAM_INT);
$stmt->execute();
$records = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>MOSHA</title>
  <link rel="stylesheet" href="css/stylesheet.css">
  <link rel="stylesheet" href="css/responsive.css">
</head>
<body>
  <?php include('common/header.php'); ?>
  <div class="index">
    <div class="container">
      <div class="websites">
        <?php foreach($records as $record): ?>
          <div class="website">
            <a class="card" href="view.php?id=<?php echo h($record['id']); ?>">
            <p class="img-wrap"><img src="<?php echo h($record['img']); ?>" alt="<?php echo h($record['title']); ?>の画像;"></p>
              <p class="title"><?php echo h($record['title']); ?></p>
              <p class="difficulty"><small>難易度：<?php for($i=0; $i<$record['difficulty']; $i++){echo '★';} ?></small></p>
              <p class="name"><small>登録者：<?php echo h($record['name']); ?></small></p>
              <p class="advice"><?php echo getTrimString(h($record['advice']), 100); ?></p>
            </a>
          </div>
        <?php endforeach; ?>
        <?php
        $placeholder = 0;
        for($i=1; $i<COLUMN; $i++){
          if(count($records) % COLUMN === $i){
            $placeholder = COLUMN - $i; 
          }
        }
        for($i=0; $i<$placeholder; $i++){
          echo '<div class="dummy"></div>';
        }
        ?>
      </div>
      <div class="pagenav">
        <?php
        if($page > 1){
          echo '<a href="index.php?page='.($page - 1).'">&laquo; '.($page - 1).'ページ目へ</a>';
        }
        if($page < $maxpage){
          echo '<a href="index.php?page='.($page + 1).'">'.($page + 1).'ページ目へ &raquo;</a>';
        }
        ?>
      </div>
    </div>
  </div>
  <?php include('common/footer.php'); ?>
</body>
</html>