<?php
$root = (empty($_SERVER["HTTPS"]) ? "http://" : "https://").$_SERVER["HTTP_HOST"].'/mosha';
?>

<header>
  <div class="container">
    <div class="left">
      <h1><a href="<?php echo $root.'/index.php'; ?>">MOSHA</a></h1>
      <ul>
        <li><a href="<?php echo $root.'/index.php'; ?>">トップ</a></li>
        <li><a href="<?php echo $root.'/about.php' ?>">MOSHAとは</a></li>
        <?php
        if(isset($_SESSION['id'])){
          echo '<li><a href="'.$root.'/member.php?id='.$_SESSION['id'].'">マイページ</a></li>';
        }
        ?>
        <?php
        if(isset($_SESSION['id'])){
          echo '<li><a href="'.$root.'/post/input.php">投稿する</a></li>';
        }
        ?>
        <li><a href="https://docs.google.com/forms/d/e/1FAIpQLSdUrMYfdYH9aeUKp-qLkloigWVjIOwndBqegHipU8VtFhY35g/viewform?usp=sf_link">お問い合わせ</a><li>
      </ul>
    </div>
    <div class="right">
      <ul>
        <?php
        if(!isset($_SESSION['id'])){
          echo '<li><a href="'.$root.'/join/signup_mail.php">新規登録</a></li>';
          echo '<li><a href="'.$root.'/login.php">ログイン</a></li>';
        }else{
          echo '<li><a href="'.$root.'/logout.php">ログアウト</a></li>';
        }
        ?>
      </ul>
    </div>
  </div>
</header>