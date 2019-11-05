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
?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>個人情報保護方針｜MOSHA</title>
  <link rel="stylesheet" href="css/stylesheet.css">
  <link rel="stylesheet" href="css/responsive.css">
</head>
<body>
  <?php include('common/header.php'); ?>
  <div class="privacypolicy">
    <div class="main-box">
      <h2>個人情報保護方針</h2>

      <h3>個人情報の利用目的</h3>

      <p>当サイトでは、メールでのお問い合わせ、会員登録などの際に、名前（ハンドルネーム）、メールアドレス等の個人情報をご登録いただく場合がございます。

      これらの個人情報は質問に対する回答や必要な情報を電子メールなどをでご連絡する場合に利用させていただくものであり、個人情報をご提供いただく際の目的以外では利用いたしません。</p>

      <h3>個人情報の第三者への開示</h3>

      <p>当サイトでは、個人情報は適切に管理し、以下に該当する場合を除いて第三者に開示することはありません。

      ・本人のご了解がある場合

      ・法令等への協力のため、開示が必要となる場合</p>

      <h3>個人情報の開示、訂正、追加、削除、利用停止</h3>

      <p>ご本人からの個人データの開示、訂正、追加、削除、利用停止のご希望の場合には、ご本人であることを確認させていただいた上、速やかに対応させていただきます。</p>


      <h3>免責事項</h3>

      <p>当サイトからリンクやバナーなどによって他のサイトに移動された場合、移動先サイトで提供される情報、サービス等について一切の責任を負いません。

      当サイトのコンテンツ・情報につきまして、可能な限り正確な情報を掲載するよう努めておりますが、誤情報が入り込んだり、情報が古くなっていることもございます。

      当サイトに掲載された内容によって生じた損害等の一切の責任を負いかねますのでご了承ください。</p>

      <h3>個人情報保護方針の変更について</h3>

      <p>当サイトは、個人情報に関して適用される日本の法令を遵守するとともに、本方針の内容を適宜見直しその改善に努めます。

      修正された最新の個人情報保護方針は常に本ページにて開示されます。</p>
    </div>
  </div>
  <?php include('common/footer.php'); ?>
</body>
</html>