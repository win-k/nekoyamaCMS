<?php
////////////////////////////////////////////////////////////////////////
// アクセス制限

  if(ereg("admin/link.php", $_SERVER['PHP_SELF']))
  {
    header("Location: ../admin.php");
    exit();
  }

////////////////////////////////////////////////////////////////////////
// リンク有効化

  if($input_type == "activate")
  {
    if(ereg($PHP_SELF, $HTTP_REFERER))
    {
      // 有効化
      $admin_query = "update wps_link set active='Y'"
                   . " where id=${input_id}";
      mysql_query($admin_query, $db);

      // 登録数増加
      $admin_query = "update wps_link_category"
                   . " set count=count+1 where id=${input_cid}";
      mysql_query($admin_query, $db);
    }
  }

////////////////////////////////////////////////////////////////////////
// リンク無効化

  elseif($input_type == "deactivate")
  {
    if(ereg($PHP_SELF, $HTTP_REFERER))
    {
      // 無効化
      $admin_query = "update wps_link set active='N'"
                   . " where id=${input_id}";
      mysql_query($admin_query, $db);

      // 登録数減少
      $admin_query = "update wps_link_category"
                   . " set count=count-1 where id=${input_cid}";
      mysql_query($admin_query, $db);
    }
  }

////////////////////////////////////////////////////////////////////////
// リンク編集

  elseif($input_type == "edit")
  {
    if(strtoupper($REQUEST_METHOD) == "POST" &&
       ereg($PHP_SELF, $HTTP_REFERER))
    {
      // 登録数増減
      if($input_cid != $input_cat)
      {
        $admin_query = "update wps_link_category"
                     . " set count=count-1 where id=${input_cid}";
        mysql_query($admin_query, $db);

        $admin_query = "update wps_link_category"
                     . " set count=count+1 where id=${input_cat}";
        mysql_query($admin_query, $db);
      }

      // リンク編集
      $admin_query = "update wps_link set cid='${input_cat}',"
                   . "title='${input_title}',url='${input_url}',"
                   . "date='${input_date}',content='${input_content}',"
                   . "username='${input_username}',count=${input_count}"
                   . " where id=${input_id}";
      mysql_query($admin_query, $db);
    }
  }

////////////////////////////////////////////////////////////////////////
// リンク削除

  elseif($input_type == "delete")
  {
    if(ereg($PHP_SELF, $HTTP_REFERER))
    {
      // リンクの削除
      $admin_query = "delete from wps_link where id=${input_id}";
      mysql_query($admin_query, $db);

      // 登録数減少
      $admin_query = "update wps_link_category"
                   . " set count=count-1 where id=${input_cid}";
      mysql_query($admin_query, $db);
    }
  }

////////////////////////////////////////////////////////////////////////
// リンク削除の確認

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
<a href="admin.php?mode=admin&amp;action=link&amp;type=delete&amp;id=${input_id}&amp;cid=${input_cid}">
はい
</a>
&nbsp;/&nbsp;
<a href="admin.php?mode=admin&amp;action=link">
いいえ
</a>
&nbsp;]
</center>
<br>
_EOT_;
    }
  }

////////////////////////////////////////////////////////////////////////
// リンク編集画面

  $page_content .= <<<_EOT_
<table border="0" cellspacing="0" cellpadding="2" width="100%">
<tr>
  <td bgcolor="#cccccc">▼リンクの編集</td>
</tr>
</table>
<br>
_EOT_;

  if($input_type == "editview")
  {
    $admin_query = "select * from wps_link where id=${input_id}";
    $admin_text_data = mysql_query($admin_query, $db);
    $admin_text = mysql_fetch_assoc($admin_text_data);
    $page_content .= <<<_EOT_
<center>
<table border="0" cellspacing="0" cellpadding="2">
<form method="POST" action="admin.php">
<tr>
  <td>ヒット数</td>
</tr>
<tr>
  <td>
    <input type="text" name="count" size="10" maxlength="10" value="${admin_text['count']}">
  </td>
</tr>
<tr>
  <td>名前</td>
</tr>
<tr>
  <td>
    <input type="text" name="username" size="20" maxlength="255" value="${admin_text['username']}">
  </td>
</tr>
<tr>
  <td>カテゴリ</td>
</tr>
<tr>
  <td>
    <select name="cat">
_EOT_;

    $admin_query = "select id,category from wps_link_category"
                 . " order by weight asc";
    $admin_cat_data = mysql_query($admin_query, $db);
    while($admin_cat = mysql_fetch_assoc($admin_cat_data))
    {
      if($admin_cat['id'] == $admin_text['cid'])
      {
        $page_content .= '<option value="'
                       . $admin_cat['id'] . '" selected>'
                       . $admin_cat['category'];
      }
      else
      {
        $page_content .= '<option value="'
                       . $admin_cat['id'] . '">'
                       . $admin_cat['category'];
      }
    }

    $page_content .= <<<_EOT_
    </select>
  </td>
</tr>
<tr>
  <td>タイトル</td>
</tr>
<tr>
  <td>
    <input type="text" name="title" size="40" maxlength="255" value="${admin_text['title']}">
  </td>
</tr>
<tr>
  <td>URL</td>
</tr>
<tr>
  <td>
    <input type="text" name="url" size="40" maxlength="255" value="${admin_text['url']}">
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
  <td>コメント</td>
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
    <input type="hidden" name="action" value="link">
    <input type="hidden" name="type" value="edit">
    <input type="hidden" name="id" value="${input_id}">
    <input type="hidden" name="cid" value="${input_cid}">
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
// リンクリスト

  $admin_query = "select count(id) from wps_link";
  $admin_result = mysql_query($admin_query, $db);
  $admin_rows = mysql_result($admin_result, 0, 0);
  if(!isset($input_page))
  {
    $input_page = 1;
  }
  $admin_max = floor($admin_rows / $page_link_show);
  if($admin_rows % $page_bbs_show != 0)
  {
    $admin_max++;
  }
  $admin_start = $input_page * $page_link_show - $page_link_show;
  $admin_end = $admin_start + $page_link_show - 1;

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
                     . '&amp;action=link&amp;page=' . $i . '">'
                     . $i . '</a>&nbsp;';
    }
  }
  $page_content .= '</div>';

  $page_content .= <<<_EOT_
<table border="1" cellspacing="1" cellpadding="2" width="100%">
<tr>
  <td align="center">カテゴリ</td>
  <td align="center">タイトル</td>
  <td align="center">日付</td>
  <td align="center">表示</td>
  <td colspan="2" align="center">機能</td>
</tr>
_EOT_;

  $admin_query = "select * from wps_link"
               . " order by cid asc"
               . " limit ${admin_start},${admin_end}";
  $admin_link_data = mysql_query($admin_query, $db);
  while($admin_link = mysql_fetch_assoc($admin_link_data))
  {
    $admin_query = "select category from wps_link_category"
                 . " where id=${admin_link['cid']}";
    $admin_result = mysql_query($admin_query, $db);
    $admin_link_cat = mysql_result($admin_result, 0, 0);

    if($admin_link['active'] == 'N')
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
  <td>${admin_link_cat}</td>
  <td>${admin_link['title']}</td>
  <td>${admin_link['date']}</td>
  <td align="center">
    <a href="admin.php?mode=admin&amp;action=link&amp;type=${admin_type}&amp;id=${admin_link['id']}&amp;cid=${admin_link['cid']}">
      ${admin_status}
    </a>
  </td>
  <td align="center">
    <a href="admin.php?mode=admin&amp;action=link&amp;type=editview&amp;id=${admin_link['id']}&amp;cid=${admin_link['cid']}">
      編集
    </a>
  </td>
  <td align="center">
    <a href="admin.php?mode=admin&amp;action=link&amp;type=confirm&amp;id=${admin_link['id']}&amp;cid=${admin_link['cid']}">
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
                     . '&amp;action=link&amp;page=' . $i . '">'
                     . $i . '</a>&nbsp;';
    }
  }
  $page_content .= '</div><br>';
?>