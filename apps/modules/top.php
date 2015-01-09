<?php
////////////////////////////////////////////////////////////////////////
// アクセス制限

  if(ereg("modules/top.php", $_SERVER['PHP_SELF']))
  {
    header("Location: ../index.php");
    exit();
  }

////////////////////////////////////////////////////////////////////////
// タイトル

  $page_content .= <<<_EOT_
<table border="0" cellspacing="0" cellpadding="0">
<tr>
  <td><img src="images/bk.gif" width="1" height="15"></td>
</tr>
<tr>
  <td><img src="images/top.gif" width="115" height="12"></td>
</tr>
<tr>
  <td><img src="images/bk.gif" width="1" height="15"></td>
</tr>
</table>
_EOT_;

////////////////////////////////////////////////////////////////////////
// トップページのコンテンツを取得

  $top_query = "select title,date,content"
             . " from wps_top where active = 'Y'"
             . " order by date desc";
  $top_data = mysql_query($top_query, $db);
  while($top_content = mysql_fetch_assoc($top_data))
  {
    $page_content .= <<<_EOT_
<table border="0" cellspacing="0" cellpadding="5" width="100%">
<tr>
  <td bgcolor="#cccccc">${top_content['title']}</td>
  <td align="right" bgcolor="#cccccc">[ ${top_content['date']} ]</td>
</tr>
<tr>
  <td colspan="2" bgcolor="#eeeeee">${top_content['content']}</td>
</tr>
</table>
<br>
_EOT_;
  }
?>