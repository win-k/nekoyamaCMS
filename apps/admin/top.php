<?php
////////////////////////////////////////////////////////////////////////
// アクセス制限

  if(ereg("admin/top.php", $_SERVER['PHP_SELF']))
  {
    header("Location: ../admin.php");
    exit();
  }

////////////////////////////////////////////////////////////////////////
// 新規追加

  if($input_type == "new")
  {
    if(strtoupper($REQUEST_METHOD) == "POST" &&
       ereg($PHP_SELF, $HTTP_REFERER))
    {
      $admin_query = "insert into wps_top(title,content,date)"
                   . " values('${input_title}','${input_content}',now())";
      mysql_query($admin_query, $db);
    }
    header("Location: admin.php?mode=admin&action=top");
  }

////////////////////////////////////////////////////////////////////////
// 有効化

  elseif($input_type == "activate")
  {
    if(ereg($PHP_SELF, $HTTP_REFERER))
    {
      $admin_query = "update wps_top set active='Y'"
                   . " where id=${input_id}";
      mysql_query($admin_query, $db);
    }
    header("Location: admin.php?mode=admin&action=top");
  }

////////////////////////////////////////////////////////////////////////
// 無効化

  elseif($input_type == "deactivate")
  {
    if(ereg($PHP_SELF, $HTTP_REFERER))
    {
      $admin_query = "update wps_top set active='N'"
                   . " where id=${input_id}";
      mysql_query($admin_query, $db);
    }
    header("Location: admin.php?mode=admin&action=top");
  }

////////////////////////////////////////////////////////////////////////
// 編集

  elseif($input_type == "edit")
  {
    if(strtoupper($REQUEST_METHOD) == "POST" &&
       ereg($PHP_SELF, $HTTP_REFERER))
    {
      $admin_query = "update wps_top set title='${input_title}',"
                   . "content='${input_content}',date='${input_date}'"
                   . " where id=${input_id}";
      mysql_query($admin_query, $db);
    }
    header("Location: admin.php?mode=admin&action=top");
  }

////////////////////////////////////////////////////////////////////////
// 削除

  elseif($input_type == "delete")
  {
    if(ereg($PHP_SELF, $HTTP_REFERER))
    {
      $admin_query = "delete from wps_top where id=${input_id}";
      mysql_query($admin_query, $db);
    }
    header("Location: admin.php?mode=admin&action=top");
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
<a href="admin.php?mode=admin&amp;action=top&amp;type=delete&amp;id=${input_id}">
はい
</a>
&nbsp;/&nbsp;
<a href="admin.php?mode=admin&amp;action=top">
いいえ
</a>
&nbsp;]
</center>
<br>
_EOT_;
    }
  }

////////////////////////////////////////////////////////////////////////
// 編集画面

  $page_content .= <<<_EOT_
<table border="0" cellspacing="0" cellpadding="2" width="100%">
<tr>
  <td bgcolor="#cccccc">▼記事の編集</td>
</tr>
</table>
<br>
_EOT_;

  if($input_type == "editview")
  {
    $admin_query = "select * from wps_top where id=${input_id}";
    $admin_text_data = mysql_query($admin_query, $db);
    $admin_text = mysql_fetch_assoc($admin_text_data);
    $page_content .= <<<_EOT_
<center>
<table border="0" cellspacing="0" cellpadding="2">
<form method="POST" action="admin.php">
<tr>
  <td>タイトル</td>
</tr>
<tr>
  <td>
    <input type="text" name="title" size="70" maxlength="255" value="${admin_text['title']}">
  </td>
</tr>
<tr>
  <td>日付</td>
</tr>
<tr>
  <td>
    <input type="text" name="date" size="70" maxlength="19" value="${admin_text['date']}">
  </td>
</tr>
<tr>
  <td>テキスト</td>
</tr>
<tr>
  <td>
    <textarea name="content" cols="70" rows="8" wrap="no">${admin_text['content']}</textarea>
  </td>
</tr>
<tr>
  <td align="center">
    <br>
    <input type="hidden" name="mode" value="admin">
    <input type="hidden" name="action" value="top">
    <input type="hidden" name="type" value="edit">
    <input type="hidden" name="id" value="${input_id}">
    <input type="submit" value="編集する">
    <input type="reset" value="やり直す">
  </td>
</tr>
</form>
</table>
</center>
<br>
_EOT_;
  }

////////////////////////////////////////////////////////////////////////
// 記事リスト

  $page_content .= <<<_EOT_
<table border="1" cellspacing="1" cellpadding="2" width="100%">
<tr>
  <td align="center">タイトル</td>
  <td align="center">日付</td>
  <td align="center">表示</td>
  <td colspan="2" align="center">機能</td>
</tr>
_EOT_;

  $admin_query = "select * from wps_top order by id asc";
  $admin_top_data = mysql_query($admin_query, $db);
  while($admin_top = mysql_fetch_assoc($admin_top_data))
  {
    if($admin_top['active'] == "N")
    {
      $admin_type = "activate";
      $admin_status = "×";
    }
    else
    {
      $admin_type = "deactivate";
      $admin_status = "○";
    }

    $page_content .= <<<_EOT_
<tr>
  <td>${admin_top['title']}</td>
  <td>${admin_top['date']}</td>
  <td align="center">
    <a href="admin.php?mode=admin&amp;action=top&amp;type=${admin_type}&amp;id=${admin_top['id']}">
      ${admin_status}
    </a>
  </td>
  <td align="center">
    <a href="admin.php?mode=admin&amp;action=top&amp;type=editview&amp;id=${admin_top['id']}">
      編集
    </a>
  </td>
  <td align="center">
    <a href="admin.php?mode=admin&amp;action=top&amp;type=confirm&amp;id=${admin_top['id']}">
      削除
    </a>
  </td>
</tr>
_EOT_;
  }
  $page_content .= "</table><br>";

////////////////////////////////////////////////////////////////////////
// 新規追加画面

  $page_content .= <<<_EOT_
<table border="0" cellspacing="0" cellpadding="2" width="100%">
<tr>
  <td bgcolor="#cccccc">▼記事の追加</td>
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
    <input type="text" name="title" size="70" maxlength="255">
  </td>
</tr>
<tr>
  <td>テキスト</td>
</tr>
<tr>
  <td>
    <textarea name="content" cols="70" rows="8" wrap="no"></textarea>
  </td>
</tr>
<tr>
  <td align="center">
    <br>
    <input type="hidden" name="mode" value="admin">
    <input type="hidden" name="action" value="top">
    <input type="hidden" name="type" value="new">
    <input type="submit" value="追加する">
    <input type="reset" value="やり直す">
  </td>
</tr>
</form>
</table>
</center>
<br>
_EOT_;
?>