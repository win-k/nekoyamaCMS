<?php
////////////////////////////////////////////////////////////////////////
// アクセス制限

  if(ereg("admin/submenu.php", $_SERVER['PHP_SELF']))
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
                   . " where internal_name='menu_functions'";
      mysql_query($admin_query, $db);
    }
    header("Location: admin.php?mode=admin&action=submenu");
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
      $admin_query = "update wps_menu_functions set weight=${admin_back}"
                   . " where weight=${admin_weight}";
      mysql_query($admin_query, $db);

      // 編集中のカテゴリを移動
      $admin_query = "update wps_menu_functions set weight=${admin_forward}"
                   . " where id=${input_id}";
      mysql_query($admin_query, $db);
    }
    header("Location: admin.php?mode=admin&action=submenu");
  }

////////////////////////////////////////////////////////////////////////
// 表示順の重複解消

  elseif($input_type == "conflict")
  {
    if(ereg($PHP_SELF, $HTTP_REFERER))
    {
      $admin_query = "select id from wps_menu_functions"
                   . " order by weight";
      $admin_menu_data = mysql_query($admin_query, $db);
      $admin_weight = 1;
      while($admin_menu = mysql_fetch_assoc($admin_menu_data))
      {
        $admin_query = "update wps_menu_functions"
                     . " set weight=${admin_weight}"
                     . " where id=${admin_menu['id']}";
        mysql_query($admin_query, $db);
        $admin_weight++;
      }
    }
    header("Location: admin.php?mode=admin&action=submenu");
  }

////////////////////////////////////////////////////////////////////////
// メニュー項目の有効化

  elseif($input_type == "activate")
  {
    if(ereg($PHP_SELF, $HTTP_REFERER))
    {
      $admin_query = "update wps_menu_functions set active='Y'"
                   . " where id=${input_id}";
      mysql_query($admin_query, $db);
    }
    header("Location: admin.php?mode=admin&action=submenu");
  }

////////////////////////////////////////////////////////////////////////
// メニュー項目の無効化

  elseif($input_type == "deactivate")
  {
    if(ereg($PHP_SELF, $HTTP_REFERER))
    {
      $admin_query = "update wps_menu_functions set active='N'"
                   . " where id=${input_id}";
      mysql_query($admin_query, $db);
    }
    header("Location: admin.php?mode=admin&action=submenu");
  }

////////////////////////////////////////////////////////////////////////
// ヘッダーの有効化

  elseif($input_type == "show")
  {
    if(ereg($PHP_SELF, $HTTP_REFERER))
    {
      $admin_query = "update wps_menu_functions set header='Y'"
                   . " where id=${input_id}";
      mysql_query($admin_query, $db);
    }
    header("Location: admin.php?mode=admin&action=submenu");
  }

////////////////////////////////////////////////////////////////////////
// ヘッダーの無効化

  elseif($input_type == "hide")
  {
    if(ereg($PHP_SELF, $HTTP_REFERER))
    {
      $admin_query = "update wps_menu_functions set header='N'"
                   . " where id=${input_id}";
      mysql_query($admin_query, $db);
    }
    header("Location: admin.php?mode=admin&action=submenu");
  }

////////////////////////////////////////////////////////////////////////
// メニューリスト

  $page_content .= <<<_EOT_
<table border="0" cellspacing="0" cellpadding="2" width="100%">
<tr>
  <td bgcolor="#cccccc">▼メニューアイテムの編集</td>
</tr>
</table>
<br>
<table border="1" cellspacing="1" cellpadding="2" width="100%">
<form method="POST" action="admin.php">
<tr>
  <td align="center">
<a href="admin.php?mode=admin&amp;action=submenu&amp;type=conflict">
順位
</a>
  </td>
  <td align="center">移動</td>
  <td align="center">名称</td>
  <td align="center">メニュー</td>
  <td align="center">ヘッダー</td>
</tr>
_EOT_;

  $admin_query = "select max(weight) from wps_menu_functions";
  $admin_result = mysql_query($admin_query, $db);
  $admin_weight_max = mysql_result($admin_result, 0, 0);

  $admin_query = "select * from wps_menu_functions order by weight";
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
<a href="admin.php?mode=admin&amp;action=submenu&amp;type=moveup&amp;id=${admin_menu['id']}&amp;weight=${admin_menu['weight']}">
▲
</a>
_EOT_;
    }

    if($admin_menu['weight'] < $admin_weight_max)
    {
      $page_content .= <<<_EOT_
<a href="admin.php?mode=admin&amp;action=submenu&amp;type=movedown&amp;id=${admin_menu['id']}&amp;weight=${admin_menu['weight']}">
▼
</a>
_EOT_;
    }

      $page_content .= <<<_EOT_
  </td>
  <td>${admin_menu['name']}</td>
  <td align="center">
    <a href="admin.php?mode=admin&amp;action=submenu&amp;type=${admin_menu_type}&amp;id=${admin_menu['id']}">
      ${admin_menu_status}
    </a>
  </td>
  <td align="center">
    <a href="admin.php?mode=admin&amp;action=submenu&amp;type=${admin_header_type}&amp;id=${admin_menu['id']}">
      ${admin_header_status}
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
// メニュー編集画面

  $admin_query = "select display_name from wps_menu"
               . " where internal_name='menu_functions'";
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
    <input type="hidden" name="action" value="submenu">
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