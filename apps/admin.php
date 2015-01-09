<?php
////////////////////////////////////////////////////////////////////////
// セッション開始

  session_start();

////////////////////////////////////////////////////////////////////////
// 準備

  // DBへの接続設定を読み込む
  require("config.php");

  // ユーザー関数を読み込む
  require("function.php");

////////////////////////////////////////////////////////////////////////
// 外部からのデータを処理する

  // 受信データを変数に展開
  extract($_POST, EXTR_PREFIX_ALL, "input");
  extract($_GET, EXTR_PREFIX_ALL, "input");

  // 変数を補完
  if(!$input_action)
  {
    $input_action = "preference";
    if(!$input_mode)
    {
      $input_mode = "admin";
    }
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

  // ヘッダを生成
  include("header.php");

  // メニューを生成
  include("menu.php");

  // ユーザー認証
  $user_auth = 0;
  if($input_name && $input_pass)
  {
    if(strtoupper($REQUEST_METHOD) == "POST" &&
       ereg($PHP_SELF, $HTTP_REFERER))
    {
      $admin_query = "select * from wps_admin"
                   . " where name = '${input_name}'"
                   . " and pass = password('${input_pass}')";
      $admin_auth_data = mysql_query($admin_query, $db);
      if(mysql_errno($db) == 0)
      {
        $admin_auth = mysql_fetch_assoc($admin_auth_data);
      }
      if(is_array($admin_auth))
      {
        if(!isset($_SESSION['name']))
        {
          $_SESSION['name'] = md5($admin_auth['name']);
        }

        if(!isset($_SESSION['pass']))
        {
          $_SESSION['pass'] = md5($admin_auth['pass']);
        }

        $user_auth = 1;
      }
    }
  }
  else
  {
    $admin_query = "select * from wps_admin";
    $admin_auth_data = mysql_query($admin_query, $db);
    if(mysql_errno($db) == 0)
    {
      while($admin_auth = mysql_fetch_assoc($admin_auth_data))
      {
        if($_SESSION['name'] == md5($admin_auth['name']) &&
           $_SESSION['pass'] == md5($admin_auth['pass']))
        {
          $user_auth = 1;
        }
      }
    }
  }

  // タイトル
    $page_content = <<<_EOT_
<table border="0" cellspacing="0" cellpadding="0">
<tr>
  <td><img src="images/bk.gif" width="1" height="15"></td>
</tr>
<tr>
  <td><img src="images/admin.gif" width="120" height="12"></td>
</tr>
<tr>
  <td><img src="images/bk.gif" width="1" height="15"></td>
</tr>
</table>
_EOT_;

  // 未認証の場合は認証フォームの表示
  if(!$user_auth)
  {
    $page_content .= <<<_EOT_
<table border="0" cellspacing="2" cellpadding="0">
<form method="POST" action="admin.php">
<tr>
  <td>Name</td>
  <td>&nbsp;<input type="text" name="name"></td>
</tr>
<tr>
  <td>Pass</td>
  <td>&nbsp;<input type="password" name="pass"></td>
</tr>
<tr>
  <td colspan="2">
    <br>
    <input type="submit" value="ログイン">
  </td>
</tr>
</form>
</table>
_EOT_;
  }

  // 認証済みの場合はメインコンテンツの取得
  else
  {
    // 管理メニュー
    $page_content .= <<<_EOT_
<table border="0" cellspacing="0" cellpadding="5" width="100%" bgcolor="#cccccc">
<tr>
  <td>
    <a href="admin.php?mode=admin&amp;action=preference">
      ■基本設定の変更
    </a>
  </td>
  <td>
    <a href="admin.php?mode=admin&amp;action=module">
      ■機能の管理
    </a>
  </td>
  <td>
    <a href="admin.php?mode=admin&amp;action=database">
      ■データベースの管理
    </a>
  </td>
</tr>
<tr>
  <td>
    <a href="admin.php?mode=admin&amp;action=top">
      ■トップページの管理
    </a>
  </td>
  <td>
    <a href="admin.php?mode=admin&amp;action=contents">
      ■コンテンツの管理
    </a>
  </td>
  <td>
    <a href="admin.php?mode=admin&amp;action=access">
      ■アクセスログの管理
    </a>
  </td>
</tr>
<tr>
  <td>
    <a href="admin.php?mode=admin&amp;action=bbs">
      ■掲示板の管理
    </a>
  </td>
  <td>
    <a href="admin.php?mode=admin&amp;action=category">
      ■リンクカテゴリの管理
    </a>
  </td>
  <td>
    <a href="admin.php?mode=admin&amp;action=link">
      ■リンク集の管理
    </a>
  </td>
</tr>
<tr>
  <td>
    <a href="admin.php?mode=admin&amp;action=mainmenu">
      ■メインメニューの管理
    </a>
  </td>
  <td>
    <a href="admin.php?mode=admin&amp;action=submenu">
      ■サブメニューの管理
    </a>
  </td>

  <td>
    <a href="admin.php?mode=admin&amp;action=poll">
      ■アンケートの管理
    </a>
  </td>
</tr>
<tr>
  <td>
    <a href="admin.php?mode=admin&amp;action=banner">
      ■リンクバナーの管理
    </a>
  </td>
  <td>
    <a href="admin.php?mode=admin&amp;action=usermenu">
      ■ユーザーメニューの管理
    </a>
  </td>
  <td>
    <a href="admin.php?mode=admin&amp;action=logout">
      ■ログアウト
    </a>
  </td>
</tr>
</table>
<br>
_EOT_;

    // モジュール
    if($input_mode == "admin")
    {
      include("admin/${input_action}.php");
    }
    else
    {
      include("modules/${input_action}.php");
    }
  }

  // DBとの接続を切断
  mysql_close($db);

////////////////////////////////////////////////////////////////////////
// ページを表示する

  // テンプレートを読み込む
  include("template.php");
?>