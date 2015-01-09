<?php
///////////////////////////////////////////////////////////////////////
// アクセス制限

  if(ereg("config.php", $_SERVER['PHP_SELF']))
  {
    header("Location: index.php");
    exit();
  }

///////////////////////////////////////////////////////////////////////
// データベースの設定
// $db_host : ホスト名
// $db_user : ユーザー名
// $db_pass : パスワード
// $db_name : データベース名

  $db_host = "localhost";
  $db_user = "your_name";
  $db_pass = "your_pass";
  $db_name = "wps";
?>