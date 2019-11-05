<?php
$root = (empty($_SERVER["HTTPS"]) ? "http://" : "https://").$_SERVER["HTTP_HOST"].'/mosha';
?>

<footer>
  <div class="nav">
    <p><a href="<?php echo $root.'/index.php'; ?>" class="link">トップへ戻る</a>｜<a href="<?php echo $root.'/privacypolicy.php'; ?>" class="link">個人情報保護方針</a></p>
  </div>
  <div class="copy">
    <p>©︎2019 Minimono ALL rights reserved</p>
  </div>
</footer>