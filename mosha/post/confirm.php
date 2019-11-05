<?php
session_start();

require_once('../common/function.php');

require_once('../common/db.php');

if(empty($_SESSION['id']) || empty($_SESSION['post'])){
  header('Location: ../index.php');
  exit();
}

if(isset($_SESSION['time'])){
  if($_SESSION['time'] < time() - 3600){
    header('Location: ../logout.php?action=timeout');
    exit();
  }
  $_SESSION['time'] = time();
}

if(isset($_SESSION['token']) && isset($_POST['token']) && $_SESSION['token'] === $_POST['token']){
  unset($_SESSION['token']);

  $stmt = $db->prepare('INSERT INTO websites (member_id, url, title, img, difficulty, advice, date) VALUES (?, ?, ?, ?, ?, ?, NOW())');
  $stmt->execute(array($_SESSION['id'], $_SESSION['post']['url'], $_SESSION['post']['title'], $_SESSION['post']['img'], $_SESSION['post']['difficulty'], $_SESSION['post']['advice'] ));

  unset($_SESSION['post']);

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
  <title>投稿確認｜MOSHA</title>
  <link rel="stylesheet" href="../css/stylesheet.css">
  <link rel="stylesheet" href="../css/responsive.css">
</head>
<body>
  <?php include('../common/header.php'); ?>
  <div class="post-confirm">
    <div class="main-box">
      <h2>よろしければ「投稿する」ボタンを押してください</h2>
      <table>
        <tr>
          <th>サイトタイトル</th>
          <td><?php echo h($_SESSION['post']['title']); ?></td>
        </tr>
        <tr>
          <th>サイトURL</th>
          <td><a href="<?php echo h($_SESSION['post']['url']); ?>" class="url-link"><?php echo h($_SESSION['post']['url']); ?></a></td>
        </tr>
        <tr>
          <th>サイト画像</th>
          <td><img src="<?php echo h($_SESSION['post']['img']); ?>" alt="<?php echo h($_SESSION['post']['title']); ?>の画像"></td>
        </tr>
        <tr>
          <th>難易度</th>
          <td><?php for($i=0; $i<$_SESSION['post']['difficulty']; $i++){echo '★';} ?></td>
        </tr>
        <tr>
          <th>アドバイス</th>
          <td><?php echo nl2br(h($_SESSION['post']['advice'])); ?></td>
        </tr>
      </table>
      <p class="notice"><small>※もしサイト画像が正しく表示されなければ、10秒くらい待ってから再読み込みしてください。<br>　それでも不具合があれば、別の画像をアップロードしてください。</small></p>
      <p class="notice"><small>※難易度とアドバイスは投稿した後でもマイページから編集できます。</small></p>
      <form action="" method="post">
        <input type="hidden" name="token" value="<?php echo $_SESSION['token']; ?>">
        <button type="submit">投稿する</button>
      </form>
      <button onclick="location.href='input.php?action=rewrite'">戻る</button>
    </div>
  </div>
  <?php include('../common/footer.php'); ?>
</body>
</html>