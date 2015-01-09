<?php
////////////////////////////////////////////////////////////////////////
// 準備

  // DBへの接続設定を読み込む
  require("config.php");

  // ユーザー関数を読み込む
  require("function.php");

////////////////////////////////////////////////////////////////////////
// 外部からのデータを処理する

  // データを変数に展開
  extract($_POST, EXTR_PREFIX_ALL, "input");
  extract($_GET, EXTR_PREFIX_ALL, "input");

  // 変数を補完
  if(!isset($input_action))
  {
    $input_action = "top";
  }

  // サーバー変数を展開
  extract($_SERVER);

////////////////////////////////////////////////////////////////////////
// 基本となるファイル名を設定

  $page_base = basename($PHP_SELF);

////////////////////////////////////////////////////////////////////////
// 表示するデータを処理する

  // DBに接続
  $db = mysql_connect($db_host, $db_user, $db_pass);
  mysql_select_db($db_name, $db);

  // 初期設定の読み込み
  $general_data = mysql_query("select name,content from wps_general", $db);
  while($setup_param = mysql_fetch_assoc($general_data))
  {
    extract($setup_param, EXTR_PREFIX_ALL, "setup");
    $page_data[$setup_name] = $setup_content;
  }

  // データを変数に展開
  extract($page_data, EXTR_PREFIX_ALL, "page");

  // ヘッダを取得
  include("header.php");

  // メニューを取得
  include("menu.php");

  // メインコンテンツを取得
  $module_path = "modules/${input_action}.php";

  if(file_exists($module_path))
  {
    include($module_path);
  }
  else
  {
    error_message("そのような機能はありません。");
  }

  // DBとの接続を切断
  mysql_close($db);

////////////////////////////////////////////////////////////////////////
// ページを表示する

  // テンプレートを読み込む
  include("template.php");
?>