<?php
////////////////////////////////////////////////////////////////////////
// アクセス制限

  if(ereg("admin/poll.php", $_SERVER['PHP_SELF']))
  {
    header("Location: ../admin.php");
    exit();
  }

////////////////////////////////////////////////////////////////////////
// メニューの編集

  if($input_type == "title")
  {
    if(strtoupper($REQUEST_METHOD) == "POST" &&
       ereg($PHP_SELF, $HTTP_REFERER))
    {
      $admin_query = "update wps_menu set display_name='${input_name}',"
                   . "active='${input_active}'"
                   . " where internal_name='menu_poll'";
      mysql_query($admin_query, $db);
    }
    header("Location: admin.php?mode=admin&action=poll");
  }

////////////////////////////////////////////////////////////////////////
// アンケート項目の編集

  elseif($input_type == "edit")
  {
    if(strtoupper($REQUEST_METHOD) == "POST" &&
       ereg($PHP_SELF, $HTTP_REFERER))
    {
      // タイトルの編集
      $admin_query = "update wps_menu_poll"
                   . " set item='${input_title}'"
                   . " where name='タイトル'";
      mysql_query($admin_query, $db);

      // アンケート項目の編集
      if(is_array($input_name))
      {
        foreach ($input_name as $name_key => $name_value)
        {
          $admin_query = "update wps_menu_poll"
                       . " set name='${name_value}'"
                       . " where iid=${name_key}";
          mysql_query($admin_query, $db);
        }
      }
    }
    header("Location: admin.php?mode=admin&action=poll");
  }

////////////////////////////////////////////////////////////////////////
// 投票データの削除

  elseif($input_type == "delete")
  {
    if(ereg($PHP_SELF, $HTTP_REFERER))
    {
      $admin_query = "delete from wps_poll";
      mysql_query($admin_query, $db);
    }
  }

////////////////////////////////////////////////////////////////////////
// 削除の確認

  elseif($input_type == "confirm")
  {
    if(ereg($PHP_SELF, $HTTP_REFERER))
    {
      $page_content .= <<<_EOT_
<center>
<font color="#ff0000">
本当に削除してもよろしいですか？
</font>
&nbsp;&nbsp;[&nbsp;
<a href="admin.php?mode=admin&amp;action=poll&amp;type=delete">
はい
</a>
&nbsp;/&nbsp;
<a href="admin.php?mode=admin&amp;action=poll">
いいえ
</a>
&nbsp;]
</center>
<br>
_EOT_;
    }
  }

////////////////////////////////////////////////////////////////////////
// 編集項目の表示

  $admin_query = "select item from wps_menu_poll"
               . " where name='タイトル'";
  $admin_result = mysql_query($admin_query, $db);
  $admin_title = mysql_result($admin_result, 0, 0);
  $admin_title = str_replace("&", "&amp;", $admin_title);

  $page_content .= <<<_EOT_
<center>
<table border="0" cellspacing="0" cellpadding="2" width="100%">
<tr>
  <td bgcolor="#cccccc">▼アンケート項目の編集</td>
</tr>
</table>
<br>
<table border="0" cellspacing="0" cellpadding="2">
<form method="POST" action="admin.php">
<tr>
  <td>タイトル</td>
</tr>
<tr>
  <td>
    <input type="text" name="title" size="40" maxlength="255" value="${admin_title}">
  </td>
</tr>
</table>
<br>
<table border="0" cellspacing="0" cellpadding="2">
<tr>
  <td>投票項目</td>
</tr>
_EOT_;

  $admin_query = "select iid,name from wps_menu_poll"
               . " where iid <> 0 order by iid asc";
  $admin_poll_data = mysql_query($admin_query, $db);
  while($admin_poll = mysql_fetch_assoc($admin_poll_data))
  {
    $page_content .= <<<_EOT_
<tr>
  <td>
    <input type="text" name="name[${admin_poll['iid']}]" size="40" maxlength="255" value="${admin_poll['name']}">
  </td>
</tr>
_EOT_;
  }

  $page_content .= <<<_EOT_
<tr>
  <td align="center">
    <br>
    <input type="hidden" name="mode" value="admin">
    <input type="hidden" name="action" value="poll">
    <input type="hidden" name="type" value="edit">
    <input type="submit" value="編集する">
    <input type="reset" value="やり直す">
  </td>
</tr>
</form>
</table>
</center>
<br>
_EOT_;

////////////////////////////////////////////////////////////////////////
// メニュー編集画面

  $admin_query = "select display_name from wps_menu"
               . " where internal_name='menu_poll'";
  $admin_result = mysql_query($admin_query, $db);
  $admin_title = mysql_result($admin_result, 0, 0);

  $page_content .= <<<_EOT_
<table border="0" cellspacing="0" cellpadding="2" width="100%">
<tr>
  <td bgcolor="#cccccc">▼メニューの編集</td>
</tr>
</table>
<br>
<center>
<table border="0" cellspacing="0" cellpadding="2">
<form method="POST" action="admin.php">
<tr>
  <td>タイトル</td>
</tr>
<tr>
  <td>
    <input type="text" name="name" size="20" maxlength="255" value="${admin_title}">
  </td>
</tr>
<tr>
  <td>表示</td>
</tr>
<tr>
  <td>
    <select name="active">
      <option value="Y" selected>有効
      <option value="N">無効
    </select>
  </td>
</tr>
<tr>
  <td align="center">
    <br>
    <input type="hidden" name="mode" value="admin">
    <input type="hidden" name="action" value="poll">
    <input type="hidden" name="type" value="title">
    <input type="submit" value="編集する">
  </td>
</tr>
</form>
</table>
</center>
<br>
_EOT_;

////////////////////////////////////////////////////////////////////////
// 投票データ編集画面

  $page_content .= <<<_EOT_
<table border="0" cellspacing="0" cellpadding="2" width="100%">
<tr>
  <td bgcolor="#cccccc">▼投票データの編集</td>
</tr>
</table>
<p>
&nbsp;
<a href="admin.php?mode=admin&amp;action=poll&amp;type=confirm">
●投票データを削除する
</a>
</p>
<br>
_EOT_;
?>