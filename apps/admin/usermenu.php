<?php
////////////////////////////////////////////////////////////////////////
// アクセス制限

  if(ereg("admin/usermenu.php", $_SERVER['PHP_SELF']))
  {
    header("Location: ../admin.php");
    exit();
  }

////////////////////////////////////////////////////////////////////////
// タイトルの変更

  if($input_type == "title")
  {
    if(strtoupper($REQUEST_METHOD) == "POST" &&
       ereg($PHP_SELF, $HTTP_REFERER))
    {
      $admin_query = "update wps_menu set display_name='${input_name}',"
                   . "active='${input_active}'"
                   . " where internal_name='menu_user'";
      mysql_query($admin_query, $db);
    }
    header("Location: admin.php?mode=admin&action=usermenu");
  }

////////////////////////////////////////////////////////////////////////
// メニューアイテムの追加

  elseif($input_type == "new")
  {
    if(strtoupper($REQUEST_METHOD) == "POST" &&
       ereg($PHP_SELF, $HTTP_REFERER))
    {
      $admin_item = '&nbsp;<a href="' . $input_url
                  . '">_item_content_</a><br>';

      $admin_query = "select max(weight) from wps_menu_user";
      $admin_result = mysql_query($admin_query, $db);
      $admin_weight = mysql_result($admin_result, 0, 0);
      $admin_weight++;

      $admin_query = "insert into wps_menu_user"
                   . "(name,item,active,weight,header)"
                   . " values('${input_name}','${admin_item}',"
                   . "'${input_active}',${admin_weight},'N')";
      $admin_result = mysql_query($admin_query, $db);
    }
    header("Location: admin.php?mode=admin&action=usermenu");
  }

////////////////////////////////////////////////////////////////////////
// 表示順の変更

  elseif(preg_match("/^move/", $input_type))
  {
    if(ereg($PHP_SELF, $HTTP_REFERER))
    {
      if($input_type == "moveup")
      {
        $admin_weight = $input_weight - 1;
        $admin_forward = 'weight-1';
        $admin_back = 'weight+1';
      }
      else
      {
        $admin_weight = $input_weight + 1;
        $admin_forward = 'weight+1';
        $admin_back = 'weight-1';
      }

      // 前後のカテゴリを移動
      $admin_query = "update wps_menu_user set weight=${admin_back}"
                   . " where weight=${admin_weight}";
      mysql_query($admin_query, $db);

      // 編集中のカテゴリを移動
      $admin_query = "update wps_menu_user set weight=${admin_forward}"
                   . " where id=${input_id}";
      mysql_query($admin_query, $db);
    }
    header("Location: admin.php?mode=admin&action=usermenu");
  }

////////////////////////////////////////////////////////////////////////
// 表示順の重複解消

  elseif($input_type == "conflict")
  {
    if(ereg($PHP_SELF, $HTTP_REFERER))
    {
      $admin_query = "select id from wps_menu_user"
                   . " order by weight";
      $admin_menu_data = mysql_query($admin_query, $db);
      $admin_weight = 1;
      while($admin_menu = mysql_fetch_assoc($admin_menu_data))
      {
        $admin_query = "update wps_menu_user"
                     . " set weight=${admin_weight}"
                     . " where id=${admin_menu['id']}";
        mysql_query($admin_query, $db);
        $admin_weight++;
      }
    }
    header("Location: admin.php?mode=admin&action=usermenu");
  }

////////////////////////////////////////////////////////////////////////
// メニュー項目の有効化

  elseif($input_type == "activate")
  {
    if(ereg($PHP_SELF, $HTTP_REFERER))
    {
      $admin_query = "update wps_menu_user set active='Y'"
                   . " where id=${input_id}";
      mysql_query($admin_query, $db);
    }
    header("Location: admin.php?mode=admin&action=usermenu");
  }

////////////////////////////////////////////////////////////////////////
// メニュー項目の無効化

  elseif($input_type == "deactivate")
  {
    if(ereg($PHP_SELF, $HTTP_REFERER))
    {
      $admin_query = "update wps_menu_user set active='N'"
                   . " where id=${input_id}";
      mysql_query($admin_query, $db);
    }
    header("Location: admin.php?mode=admin&action=usermenu");
  }

////////////////////////////////////////////////////////////////////////
// ヘッダーの有効化

  elseif($input_type == "show")
  {
    if(ereg($PHP_SELF, $HTTP_REFERER))
    {
      $admin_query = "update wps_menu_user set header='Y'"
                   . " where id=${input_id}";
      mysql_query($admin_query, $db);
    }
    header("Location: admin.php?mode=admin&action=usermenu");
  }

////////////////////////////////////////////////////////////////////////
// ヘッダーの無効化

  elseif($input_type == "hide")
  {
    if(ereg($PHP_SELF, $HTTP_REFERER))
    {
      $admin_query = "update wps_menu_user set header='N'"
                   . " where id=${input_id}";
      mysql_query($admin_query, $db);
    }
    header("Location: admin.php?mode=admin&action=usermenu");
  }

////////////////////////////////////////////////////////////////////////
// メニューアイテムの編集

  elseif($input_type == "edit")
  {
    if(ereg($PHP_SELF, $HTTP_REFERER))
    {
      $admin_item = '&nbsp;<a href="' . $input_url
                  . '">_item_content_</a><br>';

      $admin_query = "update wps_menu_user set name='${input_name}',"
                   . "item='${admin_item}' where id=${input_id}";
      mysql_query($admin_query, $db);
    }
    header("Location: admin.php?mode=admin&action=usermenu");
  }

////////////////////////////////////////////////////////////////////////
// メニューアイテムの削除

  elseif($input_type == "delete")
  {
    if(ereg($PHP_SELF, $HTTP_REFERER))
    {
      $admin_query = "delete from wps_menu_user where id=${input_id}";
      mysql_query($admin_query, $db);
    }
    header("Location: admin.php?mode=admin&action=usermenu");
  }

////////////////////////////////////////////////////////////////////////
// メニューアイテムの削除確認

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
<a href="admin.php?mode=admin&amp;action=usermenu&amp;type=delete&amp;id=${input_id}">
はい
</a>
&nbsp;/&nbsp;
<a href="admin.php?mode=admin&amp;action=usermenu">
いいえ
</a>
&nbsp;]
</center>
<br>
_EOT_;
    }
  }

////////////////////////////////////////////////////////////////////////
// メニューアイテム編集画面

  $page_content .= <<<_EOT_
<table border="0" cellspacing="0" cellpadding="2" width="100%">
<tr>
  <td bgcolor="#cccccc">▼メニューアイテムの編集</td>
</tr>
</table>
<br>
_EOT_;

  if($input_type == "editview")
  {
    $admin_query = "select * from wps_menu_user"
                 . " where id=${input_id}";
    $admin_item_data = mysql_query($admin_query, $db);
    $admin_item = mysql_fetch_assoc($admin_item_data);

    $admin_url = strip_tags($admin_item['item'], "<a>");
    $admin_url = preg_replace("/.*<a.+href=\"([^\"]+)\".+/", "$1", $admin_url);

    $page_content .= <<<_EOT_
<center>
<table border="0" cellspacing="0" cellpadding="2">
<form method="POST" action="admin.php">
<tr>
  <td>アイテム名</td>
</tr>
<tr>
  <td>
    <input type="text" name="name" size="20" maxlength="255" value="${admin_item['name']}">
  </td>
</tr>
<tr>
  <td>URL</td>
</tr>
<tr>
  <td>
    <input type="text" name="url" size="40" maxlength="255" value="${admin_url}">
  </td>
</tr>
<tr>
  <td align="center">
    <br>
    <input type="hidden" name="mode" value="admin">
    <input type="hidden" name="action" value="usermenu">
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
// メニューリスト

  $page_content .= <<<_EOT_
<table border="1" cellspacing="1" cellpadding="2" width="100%">
<form method="POST" action="admin.php">
<tr>
  <td align="center">
<a href="admin.php?mode=admin&amp;action=usermenu&amp;type=conflict">
順位
</a>
  </td>
  <td align="center">移動</td>
  <td align="center">名称</td>
  <td align="center">メニュー</td>
  <td align="center">ヘッダー</td>
  <td colspan="2" align="center">機能</td>
</tr>
_EOT_;

  $admin_query = "select max(weight) from wps_menu_user";
  $admin_result = mysql_query($admin_query, $db);
  $admin_weight_max = mysql_result($admin_result, 0, 0);

  $admin_query = "select * from wps_menu_user order by weight";
  $admin_menu_data = mysql_query($admin_query, $db);
  while($admin_menu = mysql_fetch_assoc($admin_menu_data))
  {
    if($admin_menu['active'] == 'N')
    {
      $admin_menu_type = "activate";
      $admin_menu_status = "×";
    }
    else
    {
      $admin_menu_type = "deactivate";
      $admin_menu_status = "○";
    }

    if($admin_menu['header'] == 'N')
    {
      $admin_header_type = "show";
      $admin_header_status = "×";
    }
    else
    {
      $admin_header_type = "hide";
      $admin_header_status = "○";
    }

    $page_content .= <<<_EOT_
<tr>
  <td align="center">${admin_menu['weight']}</td>
  <td align="center">
_EOT_;

    if($admin_menu['weight'] > 1)
    {
      $page_content .= <<<_EOT_
<a href="admin.php?mode=admin&amp;action=usermenu&amp;type=moveup&amp;id=${admin_menu['id']}&amp;weight=${admin_menu['weight']}">
▲
</a>
_EOT_;
    }

    if($admin_menu['weight'] < $admin_weight_max)
    {
      $page_content .= <<<_EOT_
<a href="admin.php?mode=admin&amp;action=usermenu&amp;type=movedown&amp;id=${admin_menu['id']}&amp;weight=${admin_menu['weight']}">
▼
</a>
_EOT_;
    }

      $page_content .= <<<_EOT_
  </td>
  <td>${admin_menu['name']}</td>
  <td align="center">
    <a href="admin.php?mode=admin&amp;action=usermenu&amp;type=${admin_menu_type}&amp;id=${admin_menu['id']}">
      ${admin_menu_status}
    </a>
  </td>
  <td align="center">
    <a href="admin.php?mode=admin&amp;action=usermenu&amp;type=${admin_header_type}&amp;id=${admin_menu['id']}">
      ${admin_header_status}
    </a>
  </td>
  <td align="center">
    <a href="admin.php?mode=admin&amp;action=usermenu&amp;type=editview&amp;id=${admin_menu['id']}">
      編集
    </a>
  </td>
  <td align="center">
    <a href="admin.php?mode=admin&amp;action=usermenu&amp;type=confirm&amp;id=${admin_menu['id']}">
      削除
    </a>
  </td>
</tr>
_EOT_;
  }

  $page_content .= <<<_EOT_
</form>
</table>
※「順位」をクリックすると表示順の重複を解消
<br><br>
_EOT_;

////////////////////////////////////////////////////////////////////////
// メニューアイテム追加画面

  $page_content .= <<<_EOT_
<table border="0" cellspacing="0" cellpadding="2" width="100%">
<tr>
  <td bgcolor="#cccccc">▼メニューアイテムの追加</td>
</tr>
</table>
<br>
<center>
<table border="0" cellspacing="0" cellpadding="2">
<form method="POST" action="admin.php">
<tr>
  <td>アイテム名</td>
</tr>
<tr>
  <td>
    <input type="text" name="name" size="20" maxlength="255">
  </td>
</tr>
<tr>
  <td>URL</td>
</tr>
<tr>
  <td>
    <input type="text" name="url" size="40" maxlength="255">
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
    <input type="hidden" name="action" value="usermenu">
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

////////////////////////////////////////////////////////////////////////
// メニュー編集画面

  $admin_query = "select display_name from wps_menu"
               . " where internal_name='menu_user'";
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
    <input type="hidden" name="action" value="usermenu">
    <input type="hidden" name="type" value="title">
    <input type="submit" value="編集する">
  </td>
</tr>
</form>
</table>
</center>
<br>
_EOT_;
?>