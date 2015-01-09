<?php
////////////////////////////////////////////////////////////////////////
// アクセス制限
  if(ereg("header.php", $_SERVER['PHP_SELF']))
  {
    header("Location: index.php");
    exit();
  }

////////////////////////////////////////////////////////////////////////
// METAタグの追加

  $page_myheader = <<<_EOT_
  <meta http-equiv="Cache-control" content="no-cache">
  <meta http-equiv="Pragma" content="no-cache">
_EOT_;

////////////////////////////////////////////////////////////////////////
// スタイルシートの追加

  $page_stylesheet = <<<_EOT_
  <style type="text/css">
  <!--
    *       { font-family:Verdana; font-size:10pt; line-height:1.2;}
    a:hover { color:#ff8c00; }
  -->
  </style>
_EOT_;

////////////////////////////////////////////////////////////////////////
// ロゴイメージの設定

  $logo_image_size = @getimagesize($page_logo_image);
  $page_logo = "<img src=\"${page_logo_image}\" ${logo_image_size[3]}"
             . " alt=\"${page_logo_alt}\">";

////////////////////////////////////////////////////////////////////////
// 最終更新日を取得

  $index_query = "select max(date)"
               . " from wps_${input_action}";
  $update_result = @mysql_query($index_query, $db);
  if(mysql_errno($db) == 0)
  {
    $update_date = @mysql_result($update_result, 0, 0);
  }
  if(mysql_errno($db) != 0 || !$update_date)
  {
    $page_date = "";
  }
  else
  {
    $page_update = $page_update_prepend
                 . preg_replace("/(\d+\-\d+\-\d+) [\d\:]+/", "$1", $update_date)
                 . $page_update_append;
  }

////////////////////////////////////////////////////////////////////////
// アクセスログをとる

  if($page_base != "admin.php")
  {
    if(!ereg($PHP_SELF, $HTTP_REFERER))
    {
      if(!$HTTP_REFERER)
      {
        $HTTP_REFERER = "Bookmark";
      }
      $referer = preg_replace("/([^\?]+)\?.+/i", "$1", $HTTP_REFERER);
      $index_query = "insert into wps_access"
                   . "(date,wday,time,user_agent,host,remote_addr,"
                   . "client_ip,via,coming_from,forwarded,"
                   . "forwarded_for,x_coming_from,x_forwarded,"
                   . "x_forwarded_for,referer)"
                   . " values(current_date(),dayofweek(current_date()),"
                   . "current_time(),'$HTTP_USER_AGENT','$HTTP_HOST',"
                   . "'$REMOTE_ADDR','$HTTP_CLIENT_IP','$HTTP_VIA',"
                   . "'$HTTP_COMING_FROM','$HTTP_FORWARDED',"
                   . "'$HTTP_FORWARDED_FOR','$HTTP_X_COMING_FROM',"
                   . "'$HTTP_X_FORWARDED','$HTTP_X_FORWARDED_FOR',"
                   . "'$referer')";
      mysql_query($index_query, $db);
    }
  }

////////////////////////////////////////////////////////////////////////
// アクセス数を取得

  $access_result = mysql_query("select max(id) from wps_access");
  $access_count = mysql_result($access_result, 0, 0);
  $page_access = $page_access_prepend
               . $access_count
               . $page_access_append;
?>