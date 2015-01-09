<?php
////////////////////////////////////////////////////////////////////////
// アクセス制限

  if(ereg("admin/bbs.php", $_SERVER['PHP_SELF']))
  {
    header("Location: ../admin.php");
    exit();
  }

////////////////////////////////////////////////////////////////////////
// 有効化

  if($input_type == "activate")
  {
    if(ereg($PHP_SELF, $HTTP_REFERER))
    {
      if($input_cid == 0)
      {
        $admin_query = "update wps_bbs set active='Y'"
                     . " where pid=${input_pid}";
      }
      else
      {
        $admin_query = "update wps_bbs set active='Y'"
                     . " where id=${input_id} or"
                     . " (pid=${input_pid} and cid=0)";
      }
      mysql_query($admin_query, $db);
    }
  }

////////////////////////////////////////////////////////////////////////
// 無効化

  elseif($input_type == "deactivate")
  {
    if(ereg($PHP_SELF, $HTTP_REFERER))
    {
      if($input_cid == 0)
      {
        $admin_query = "update wps_bbs set active='N'"
                     . " where pid=${input_pid}";
      }
      else
      {
        $admin_query = "update wps_bbs set active='N'"
                     . " where id=${input_id}";
      }
      mysql_query($admin_query, $db);
    }
  }

////////////////////////////////////////////////////////////////////////
// 編集

  elseif($input_type == "edit")
  {
    if(strtoupper($REQUEST_METHOD) == "POST" &&
       ereg($PHP_SELF, $HTTP_REFERER))
    {
      $admin_query = "update wps_bbs set title='${input_title}',"
                   . "name='${input_name}',homepage='${input_homepage}',"
                   . "email='${input_email}',date='${input_date}',"
                   . "content='${input_content}' where id=${input_id}";
      mysql_query($admin_query, $db);
    }
  }

////////////////////////////////////////////////////////////////////////
// 削除

  elseif($input_type == "delete")
  {
    if(ereg($PHP_SELF, $HTTP_REFERER))
    {
      if($input_cid == 0)
      {
        $admin_query = "delete from wps_bbs where pid=${input_pid}";
      }
      else
      {
        $admin_query = "delete from wps_bbs where id=${input_id}";
      }
        mysql_query($admin_query, $db);
    }
  }

////////////////////////////////////////////////////////////////////////
// 削除の確認

  elseif($input_type == "confirm")
  {
    $page_content .= <<<_EOT_
<center>
<font color="#ff0000">
本当に削除してもよろしいですか？
</font>
&nbsp;&nbsp;[&nbsp;
<a href="admin.php?mode=admin&amp;action=bbs&amp;type=delete&amp;id=${input_id}&amp;pid=${input_pid}&amp;cid=${input_cid}">
はい
</a>
&nbsp;/&nbsp;
<a href="admin.php?mode=admin&amp;action=bbs">
いいえ
</a>
&nbsp;]
</center>
<br>
_EOT_;
  }

////////////////////////////////////////////////////////////////////////
// 編集画面タイトル

  $page_content .= <<<_EOT_
<table border="0" cellspacing="0" cellpadding="2" width="100%">
<tr>
  <td bgcolor="#cccccc">▼メッセージの編集</td>
</tr>
</table>
<br>
_EOT_;

////////////////////////////////////////////////////////////////////////
// 編集画面

  if($input_type == "editview")
  {
    $admin_query = "select * from wps_bbs where id=${input_id}";
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
    <input type="text" name="title" size="40" maxlength="255" value="${admin_text['title']}">
  </td>
</tr>
<tr>
  <td>名前</td>
</tr>
<tr>
  <td>
    <input type="text" name="name" size="40" maxlength="255" value="${admin_text['name']}">
  </td>
</tr>
<tr>
  <td>ホームページ</td>
</tr>
<tr>
  <td>
    <input type="text" name="homepage" size="40" maxlength="255" value="${admin_text['homepage']}">
  </td>
</tr>
<tr>
  <td>メールアドレス</td>
</tr>
<tr>
  <td>
    <input type="text" name="email" size="40" maxlength="255" value="${admin_text['email']}">
  </td>
</tr>
<tr>
  <td>日付</td>
</tr>
<tr>
  <td>
    <input type="text" name="date" size="40" maxlength="19" value="${admin_text['date']}">
  </td>
</tr>
<tr>
  <td>メッセージ</td>
</tr>
<tr>
  <td>
    <textarea name="content" cols="40" rows="6" wrap="no">${admin_text['content']}</textarea>
  </td>
</tr>
<tr>
  <td align="center">
    <br>
    <input type="hidden" name="mode" value="admin">
    <input type="hidden" name="action" value="bbs">
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
// メッセージリスト

  $admin_query = "select count(id) from wps_bbs"
               . " where cid = 0";
  $admin_result = mysql_query($admin_query, $db);
  $admin_rows = mysql_result($admin_result, 0, 0);
  if(!isset($input_page))
  {
    $input_page = 1;
  }
  $admin_max = floor($admin_rows / $page_bbs_show);
  if($admin_rows % $page_bbs_show != 0)
  {
    $admin_max++;
  }
  $admin_start = $input_page * $page_bbs_show - $page_bbs_show;
  $admin_end = $admin_start + $page_bbs_show - 1;

  $page_content .= '<div align="right">Page ： ';
  for($i = 1; $i <= $admin_max; $i++)
  {
    if($i == $input_page)
    {
      $page_content .= '<font color="#ff0000">' . $i . '</font>&nbsp;';
    }
    else
    {
      $page_content .= '<a href="admin.php?mode=admin'
                     . '&amp;action=bbs&amp;page=' . $i . '">'
                     . $i . '</a>&nbsp;';
    }
  }
  $page_content .= '</div>';

  $page_content .= <<<_EOT_
<table border="1" cellspacing="1" cellpadding="2" width="100%">
<tr>
  <td align="center">PID</td>
  <td align="center">CID</td>
  <td align="center">タイトル</td>
  <td align="center">日付</td>
  <td align="center">表示</td>
  <td colspan="2" align="center">機能</td>
</tr>
_EOT_;

  $admin_query = "select * from wps_bbs where pid"
               . " between ${admin_start} and ${admin_end}"
               . " order by pid asc";
  $admin_bbs_data = mysql_query($admin_query, $db);
  while($admin_bbs = mysql_fetch_assoc($admin_bbs_data))
  {
    if($admin_bbs['active'] == "N")
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
  <td align="center">${admin_bbs['pid']}</td>
  <td align="center">${admin_bbs['cid']}</td>
  <td>${admin_bbs['title']}</td>
  <td>${admin_bbs['date']}</td>
  <td align="center">
    <a href="admin.php?mode=admin&amp;action=bbs&amp;type=${admin_type}&amp;id=${admin_bbs['id']}&amp;pid=${admin_bbs['pid']}&amp;cid=${admin_bbs['cid']}">
      ${admin_status}
    </a>
  </td>
  <td align="center">
    <a href="admin.php?mode=admin&amp;action=bbs&amp;type=editview&amp;id=${admin_bbs['id']}">
      編集
    </a>
  </td>
  <td align="center">
    <a href="admin.php?mode=admin&amp;action=bbs&amp;type=confirm&amp;id=${admin_bbs['id']}&amp;pid=${admin_bbs['pid']}&amp;cid=${admin_bbs['cid']}">
      削除
    </a>
  </td>
</tr>
_EOT_;
  }
  $page_content .= "</table>";

  $page_content .= '<div align="right">Page ： ';
  for($i = 1; $i <= $admin_max; $i++)
  {
    if($i == $input_page)
    {
      $page_content .= '<font color="#ff0000">' . $i . '</font>&nbsp;';
    }
    else
    {
      $page_content .= '<a href="admin.php?mode=admin'
                     . '&amp;action=bbs&amp;page=' . $i . '">'
                     . $i . '</a>&nbsp;';
    }
  }
  $page_content .= '</div><br>';
?>