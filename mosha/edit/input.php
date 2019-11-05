<?php
session_start();

require_once('../common/function.php');

require_once('../common/db.php');

$errors = array();

$selected = [
  '3' => '',
  '4' => '',
  '5' => '',
  '6' => ''
];

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

$stmt = $db->prepare('SELECT url, title, img, difficulty, advice FROM websites WHERE id = ? AND member_id = ?');
$stmt->execute(array($_GET['id'], $_SESSION['id']));
if($stmt->rowCount() === 1){
  $record = $stmt->fetch(PDO::FETCH_ASSOC);
}else{
  header('Location: ../index.php');
  exit();
}

if(isset($_SESSION['token']) && isset($_POST['token']) && $_SESSION['token'] === $_POST['token']){
  unset($_SESSION['token']);

  if(!in_array($_POST['difficulty'], ['3', '4', '5', '6'])){
    $errors['difficulty'] = '難易度を選択してください。';
  }else{
    $selected[$_POST['difficulty']] = 'selected';
  }

  if(empty($_POST['advice'])){
    $errors['advice'] = 'アドバイスを入力してください。';
  }else if(mb_strlen($_POST['advice']) < 40 || strlen($_POST['advice']) > 1000){
    $errors['advice'] = 'アドバイスは40文字以下1000文字以下で入力してください。';
  }

  if(empty($errors)){
    $stmt = $db->prepare('UPDATE websites SET difficulty = ?, advice = ? WHERE id = ?');
    $stmt->execute(array($_POST['difficulty'], $_POST['advice'], $_GET['id']));

    header('Location: complete.php');
    exit();
  }
}

if(empty($errors)){
  $_POST['difficulty'] = $record['difficulty'];
  $_POST['advice'] = $record['advice'];
  $selected[$_POST['difficulty']] = 'selected';
}

$_SESSION['token'] = base64_encode(openssl_random_pseudo_bytes(32));
?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>編集｜MOSHA</title>
  <link rel="stylesheet" href="../css/stylesheet.css">
  <link rel="stylesheet" href="../css/responsive.css">
</head>
<body>
  <?php include('../common/header.php'); ?>
  <div class="edit-input">
    <div class="main-box">
      <h2>投稿を編集</h2>
      <p class="title">サイトタイトル：<?php echo h($record['title']); ?></p>
      <p class="url">サイトURL：<a href="<?php echo h($record['url']); ?>" class="url-link"><?php echo h($record['url']); ?></a></p>
      <p class="img-wrap"><img src="<?php echo h($record['img']); ?>" alt="<?php echo h($record['title']); ?>の画像"></p>
      <?php if(!empty($errors)){
      echo '<ul class="errors">';
      foreach($errors as $error){
        echo '<li>'.$error.'</li>';
      }
      echo '</ul>';
      }
      ?>
      <form action="" method="post">
        <label for="difficulty">難易度<span class="required">必須</span></label>
        <select name="difficulty" id="difficulty">
          <option value="0">選択してください</option>
          <option value="3" <?php echo $selected['3']; ?>>★★★(初めて模写する人向け)</option>
          <option value="4" <?php echo $selected['4']; ?>>★★★★(いくつか模写してみた人向け)</option>
          <option value="5" <?php echo $selected['5']; ?>>★★★★★(どんなサイトでも模写できるようになりたい人向け)</option>
          <option value="6" <?php echo $selected['6']; ?>>★★★★★★(誰にも向かない)</option>
        </select>
        <label for="advice">アドバイス<span class="required">必須</label>
        <textarea name="advice" id="advice" rows="10" placeholder="40文字以上1000文字以下"><?php if(isset($_POST['advice'])){echo h($_POST['advice']);} ?></textarea>
        <input type="hidden" name="token" value="<?php echo $_SESSION['token']; ?>">
        <button type="submit">変更を保存する</button>
      </form>
    </div>
  </div>
  <?php include('../common/footer.php'); ?>
</body>
</html>