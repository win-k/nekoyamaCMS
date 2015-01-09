<?php
////////////////////////////////////////////////////////////////////////
// アクセス制限

  if(ereg("admin/contents.php", $_SERVER['PHP_SELF']))
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
      // コンテンツの追加
      $admin_query = "insert into wps_contents"
                   . "(name,topic,title,content,date,active)"
                   . " values('${input_name}','${input_topic}',"
                   . "'${input_title}','${input_content}',"
                   . "now(),'${input_active}')";
      mysql_query($admin_query, $db);

      // メニューの追加
      if($input_menu == 'Y')
      {
        $admin_query = "select id from wps_contents where name='${input_name}'";
        $admin_result = mysql_query($admin_query, $db);
        $admin_contents_id = mysql_result($admin_result, 0, 0);
        $admin_menu_item = '&nbsp;<a href="_page_base_?action=contents'
                         . '&amp;id=' . $admin_contents_id . '">'
                         . '_item_content_</a><br>';

        $admin_query = "select max(weight) from wps_menu_contents";
        $admin_result = mysql_query($admin_query, $db);
        $admin_menu_weight = mysql_result($admin_result, 0, 0);
        $admin_menu_weight++;

        if($input_active == 'N')
        {
          $input_menu = 'N';
        }

        $admin_query = "insert into wps_menu_contents"
                     . "(cid,name,item,active,weight,header)"
                     . " values(${admin_contents_id},'${input_name}',"
                     . "'${admin_menu_item}','${input_menu}',"
                     . "${admin_menu_weight},'${input_header}')";
        mysql_query($admin_query, $db);
      }
    }
    header("Location: admin.php?mode=admin&action=contents");
  }

////////////////////////////////////////////////////////////////////////
// 有効化

  elseif($input_type == "activate")
  {
    if(ereg($PHP_SELF, $HTTP_REFERER))
    {
      // コンテンツの有効化
      $admin_query = "update wps_contents set active='Y'"
                   . " where id=${input_id}";
      mysql_query($admin_query, $db);

      // メニューの有効化
      $admin_query = "update wps_menu_contents set active='Y'"
                   . " where cid=${input_id}";
      mysql_query($admin_query, $db);
    }
    header("Location: admin.php?mode=admin&action=contents");
  }

////////////////////////////////////////////////////////////////////////
// 無効化

  elseif($input_type == "deactivate")
  {
    if(ereg($PHP_SELF, $HTTP_REFERER))
    {
      // コンテンツの無効化
      $admin_query = "update wps_contents set active='N'"
                   . " where id=${input_id}";
      mysql_query($admin_query, $db);

      // メニューの無効化
      $admin_query = "update wps_menu_contents set active='N'"
                   . " where cid=${input_id}";
      mysql_query($admin_query, $db);
    }
    header("Location: admin.php?mode=admin&action=contents");
  }

////////////////////////////////////////////////////////////////////////
// 編集

  elseif($input_type == "edit")
  {
    if(strtoupper($REQUEST_METHOD) == "POST" &&
       ereg($PHP_SELF, $HTTP_REFERER))
    {
      // コンテンツの編集
      $admin_query = "update wps_contents set name='${input_name}',"
                   . "topic='${input_topic}',title='${input_title}',"
                   . "content='${input_content}',date='${input_date}'"
                   . " where id=${input_id}";
      mysql_query($admin_query, $db);

      // メニューの編集
      $admin_query = "update wps_menu_contents set name='${input_name}'"
                   . " where cid=${input_id}";
      mysql_query($admin_query, $db);
    }
    header("Location: admin.php?mode=admin&action=contents");
  }

////////////////////////////////////////////////////////////////////////
// 削除

  elseif($input_type == "delete")
  {
    if(ereg($PHP_SELF, $HTTP_REFERER))
    {
      // コンテンツの削除
      $admin_query = "delete from wps_contents where id=${input_id}";
      mysql_query($admin_query, $db);

      // メニューの削除
      $admin_query = "delete from wps_menu_contents"
                   . " where cid=${input_id}";
      mysql_query($admin_query, $db);
    }
    header("Location: admin.php?mode=admin&action=contents");
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
<a href="admin.php?mode=admin&amp;action=contents&amp;type=delete&amp;id=${input_id}">
はい
</a>
&nbsp;/&nbsp;
<a href="admin.php?mode=admin&amp;action=contents">
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
  <td bgcolor="#cccccc">▼コンテンツの編集</td>
</tr>
</table>
<br>
_EOT_;

  if($input_type == "editview")
  {
    $admin_query = "select * from wps_contents where id=${input_id}";
    $admin_text_data = mysql_query($admin_query, $db);
    $admin_text = mysql_fetch_assoc($admin_text_data);
    $page_content .= <<<_EOT_
<center>
<table border="0" cellspacing="0" cellpadding="2">
<form method="POST" action="admin.php">
<tr>
  <td>コンテンツ名</td>
</tr>
<tr>
  <td>
    <input type="text" name="name" maxlength="255" value="${admin_text['name']}">
  </td>
</tr>
<tr>
  <td>トピック用画像ファイル名</td>
</tr>
<tr>
  <td>
    <select name="topic">
_EOT_;

    $admin_images = array();
    $admin_dir = dir("images");
    while($entry = $admin_dir->read())
    {
      if(preg_match("/(\.gif$|\.png$|\.jpe?g$)/", $entry))
      {
        array_push($admin_images, $entry);
      }
    }
    $admin_dir->close();

    natsort($admin_images);
    foreach ($admin_images as $image)
    {
      $page_content .= '<option value="' . $image . '"'
                       . '>' . $image;
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
    <textarea name="content" cols="70" rows="20" wrap="no">${admin_text['content']}</textarea>
  </td>
</tr>
<tr>
  <td align="center">
    <br>
    <input type="hidden" name="mode" value="admin">
    <input type="hidden" name="action" value="contents">
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
  <td align="center">名称</td>
  <td align="center">タイトル</td>
  <td align="center">日付</td>
  <td align="center">表示</td>
  <td colspan="2" align="center">機能</td>
</tr>
_EOT_;

  $admin_query = "select * from wps_contents order by id asc";
  $admin_contents_data = mysql_query($admin_query, $db);
  while($admin_contents = mysql_fetch_assoc($admin_contents_data))
  {
    if($admin_contents['active'] == 'N')
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
  <td>${admin_contents['name']}</td>
  <td>${admin_contents['title']}</td>
  <td>${admin_contents['date']}</td>
  <td align="center">
    <a href="admin.php?mode=admin&amp;action=contents&amp;type=${admin_type}&amp;id=${admin_contents['id']}">
      ${admin_status}
    </a>
  </td>
  <td align="center">
    <a href="admin.php?mode=admin&amp;action=contents&amp;type=editview&amp;id=${admin_contents['id']}">
      編集
    </a>
  </td>
  <td align="center">
    <a href="admin.php?mode=admin&amp;action=contents&amp;type=confirm&amp;id=${admin_contents['id']}">
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
  <td bgcolor="#cccccc">▼コンテンツの追加</td>
</tr>
</table>
<br>
<center>
<table border="0" cellspacing="0" cellpadding="2">
<form method="POST" action="admin.php">
<tr>
  <td>コンテンツ名</td>
</tr>
<tr>
  <td>
    <input type="text" name="name" maxlength="255">
  </td>
</tr>
<tr>
  <td>トピック用画像ファイル名</td>
</tr>
<tr>
  <td>
    <select name="topic">
_EOT_;

  $admin_images = array();
  $admin_dir = dir("images");
  while($entry = $admin_dir->read())
  {
    if(preg_match("/(\.gif$|\.png$|\.jpe?g$)/", $entry))
    {
      array_push($admin_images, $entry);
    }
  }
  $admin_dir->close();

  natsort($admin_images);
  foreach ($admin_images as $image)
  {
    $page_content .= '<option value="' . $image . '"'
                   . '>' . $image;
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
    <input type="text" name="title" size="70" maxlength="255">
  </td>
</tr>
<tr>
  <td>テキスト</td>
</tr>
<tr>
  <td>
    <textarea name="content" cols="70" rows="16" wrap="no"></textarea>
  </td>
</tr>
<tr>
  <td>
    <table border="0" cellspacing="0" cellpadding="2">
    <tr>
      <td>新しいコンテンツを</td>
      <td>
        <select name="active">
          <option value="Y" selected>公開する
          <option value="N">公開しない
        </select>
      </td>
    </tr>
    <tr>
      <td>メニューにリンクを</td>
      <td>
        <select name="menu">
          <option value="Y" selected>追加する
          <option value="N">追加しない
        </select>
      </td>
    </tr>
    <tr>
      <td>ヘッダーにリンクを</td>
      <td>
        <select name="header">
          <option value="Y">追加する
          <option value="N" selected>追加しない
        </select>
      </td>
    </tr>
    </table>
  </td>
</tr>
<tr>
  <td align="center">
    <br>
    <input type="hidden" name="mode" value="admin">
    <input type="hidden" name="action" value="contents">
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