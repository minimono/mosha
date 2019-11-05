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

if(isset($_SESSION['token']) && isset($_POST['token']) && $_SESSION['token'] === $_POST['token']){
  unset($_SESSION['token']);

  if(empty($_POST['url'])){
    $errors['url'] = 'サイトURLを入力してください。';
  }else if(!@file_get_contents($_POST['url'])){
    $errors['url'] = 'URLが不適切です。';
  }

  if(!empty($_FILES['img']['name'])){
    $ext = mb_substr($_FILES['img']['name'], -3);
    if($ext !== 'jpg' && $ext !== 'png'){
      $errors['img'] = '「.jpg」または「.png」の画像を指定してください。';
    }else{
      $img = 'screenshot/'.date('YmdHis').$_FILES['img']['name'];
      $stmt = $db->prepare('SELECT COUNT(*) AS cnt FROM websites WHERE img = ?');
      $stmt->execute(array($img));
      $record = $stmt->fetch(PDO::FETCH_ASSOC);
      if($record['cnt'] > 0){
        $errors['img'] = '内部的なエラーが発生しました。恐れ入りますが画像を改めて指定してください。';
      }
    }
  }

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
    if(empty($img)){
      $img = 'https://s.wordpress.com/mshots/v1/'.$_POST['url'].'?w=800&h=450';
    }else{
      move_uploaded_file($_FILES['img']['tmp_name'], dirname(__FILE__).'/../'.$img);
      $imagick = new Imagick(dirname(__FILE__).'/../'.$img);
      $size = $imagick->getImageGeometry();
      $w = $size['width'];
      $h = $size['height'];
      if($w * 9 / 16 > $h){
        $width = round($h / (9 / 16));
        $x = round(($w - $width) / 2);
        $imagick->cropImage($width, $h, $x, 0);
      }else{
        $height = round($w * 9 / 16);
        $imagick->cropImage($w, $height, 0, 0);
      }
      $imagick->writeImage(dirname(__FILE__).'/../'.$img);
      $imagick->clear();
      $img = (empty($_SERVER["HTTPS"]) ? "http://" : "https://").$_SERVER["HTTP_HOST"].'/mosha/'.$img;
    }

    $_SESSION['post'] = $_POST;
    $html = file_get_contents($_POST['url']);
    $html = mb_convert_encoding($html, 'UTF-8', 'auto');
    if(preg_match( "/<title>(.*?)<\/title>/i", $html, $matches)){
      $_SESSION['post']['title'] = $matches[1];
    }else{
      $_SESSION['post']['title'] = 'タイトルなし';
    }
    $_SESSION['post']['img'] = $img;

    header('Location: confirm.php');
    exit();
  }
}

if(isset($_GET['action']) && $_GET['action'] === 'rewrite'){
  $_POST = $_SESSION['post'];
  $selected[$_POST['difficulty']] = 'selected';
}

$_SESSION['token'] = base64_encode(openssl_random_pseudo_bytes(32));
?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>投稿｜MOSHA</title>
  <link rel="stylesheet" href="../css/stylesheet.css">
  <link rel="stylesheet" href="../css/responsive.css">
</head>
<body>
  <?php include('../common/header.php'); ?>
  <div class="post">
    <div class="main-box">
      <h2>新規投稿を追加</h2>
      <?php if(!empty($errors)){
      echo '<ul class="errors">';
      foreach($errors as $error){
        echo '<li>'.$error.'</li>';
      }
      echo '</ul>';
      }
      ?>
      <form action="" method="post" enctype="multipart/form-data">
        <label for="url">サイトURL<span class="required">必須</span></label>
        <input type="text" name="url" id="url" value="<?php if(isset($_POST['url'])){echo h($_POST['url']);} ?>">
        <label for="img">サイト画像<span class="free">任意</span></label>
        <input type="file" name="img" id="img">
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
        <p class="notice"><small>※画像をアップロードしない場合は、自動的にスクリーンショットがアップロードされます。</small></p>
        <button type="submit">確認画面へ</button>
      </form>
    </div>
  </div>
  <?php include('../common/footer.php'); ?>
</body>
</html>