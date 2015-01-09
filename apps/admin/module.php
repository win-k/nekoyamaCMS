<?php
////////////////////////////////////////////////////////////////////////
// アクセス制限

  if(ereg("admin/module.php", $_SERVER['PHP_SELF']))
  {
    header("Location: ../admin.php");
    exit();
  }

////////////////////////////////////////////////////////////////////////
// 機能の追加

  if($input_type == "new")
  {
    if(strtoupper($REQUEST_METHOD) == "POST" &&
       ereg($PHP_SELF, $HTTP_REFERER))
    {
      $admin_item = '&nbsp;<a href="_page_base_?action='
                  . $input_module . '">_item_content_</a><br>';

      $admin_query = "select max(weight) from wps_menu_functions";
      $admin_result = mysql_query($admin_query, $db);
      $admin_weight = mysql_result($admin_result, 0, 0);
      $admin_weight++;

      $admin_query = "insert into wps_menu_functions"
                   . "(module,name,item,active,weight,header)"
                   . " values('${input_module}','${input_name}',"
                   . "'${admin_item}','${input_active}',"
                   . "${admin_weight},'${input_header}')";
      mysql_query($admin_query, $db);

      $file_name = "modules/${input_module}.php";
      if(file_exists("${file_name}_"))
      {
        rename("${file_name}_", $file_name);
      }
    }
    header("Location: admin.php?mode=admin&action=module");
  }

////////////////////////////////////////////////////////////////////////
// 機能の削除

  elseif($input_type == "delete")
  {
    if(ereg($PHP_SELF, $HTTP_REFERER))
    {
      $admin_query = "select module from wps_menu_functions"
                   . " where id=${input_id}";
      $admin_result = mysql_query($admin_query, $db);
      $admin_item = mysql_result($admin_result, 0, 0);
      $file_name = "modules/${admin_item}.php";
      if(file_exists($file_name))
      {
        rename($file_name, "${file_name}_");
      }

      $admin_query = "delete from wps_menu_functions"
                   . " where id=${input_id}";
      mysql_query($admin_query, $db);
    }
    header("Location: admin.php?mode=admin&action=module");
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
<a href="admin.php?mode=admin&amp;action=module&amp;type=delete&amp;id=${input_id}">
はい
</a>
&nbsp;/&nbsp;
<a href="admin.php?mode=admin&amp;action=module">
いいえ
</a>
&nbsp;]
</center>
<br>
_EOT_;
    }
  }

////////////////////////////////////////////////////////////////////////
// 機能追加画面

  $page_content .= <<<_EOT_
<center>
<table border="0" cellspacing="0" cellpadding="2" width="100%">
<tr>
  <td bgcolor="#cccccc">▼機能の追加</td>
</tr>
</table>
<br>
<table border="0" cellspacing="0" cellpadding="2">
<form method="POST" action="admin.php">
<tr>
  <td>メニュー表示名</td>
</tr>
<tr>
  <td>
    <input type="text" name="name" size="30" maxlength="255">
  </td>
</tr>
<tr>
  <td>モジュール</td>
</tr>
<tr>
  <td>
    <select name="module">
_EOT_;

  $admin_files = array();
  $admin_dir = dir("modules");
  while($entry = $admin_dir->read())
  {
    if(preg_match("/\.php_?/", $entry))
    {
      array_push($admin_files, $entry);
    }
  }
  $admin_dir->close();

  natsort($admin_files);
  foreach ($admin_files as $file)
  {
    $page_content .= '<option value="'
                   . preg_replace("/.php_?/", "", $file) . '"'
                   . '>' . rtrim($file, "_");
  }

  $page_content .= <<<_EOT_
    </select>
  </td>
</tr>
<tr>
  <td>メニュー表示</td>
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
  <td>ヘッダー表示</td>
</tr>
<tr>
  <td>
    <select name="header">
      <option value="Y" selected>有効
      <option value="N">無効
    </select>
  </td>
</tr>
<tr>
  <td align="center">
    <br>
    <input type="hidden" name="mode" value="admin">
    <input type="hidden" name="action" value="module">
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
// 機能削除画面

  $page_content .= <<<_EOT_
<center>
<table border="0" cellspacing="0" cellpadding="2" width="100%">
<tr>
  <td bgcolor="#cccccc">▼機能の削除</td>
</tr>
</table>
<br>
<table border="0" cellspacing="0" cellpadding="2">
<form method="POST" action="admin.php">
<tr>
  <td align="center">
    <select name="id">
_EOT_;

  $admin_query = "select id,name from wps_menu_functions"
               . " order by weight asc";
  $admin_func_data = mysql_query($admin_query, $db);
  while($admin_func = mysql_fetch_assoc($admin_func_data))
  {
    $page_content .= '<option value="' . $admin_func['id']
                   . '">' . $admin_func['name'];
  }

  $page_content .= <<<_EOT_
    </select>
  </td>
</tr>
<tr>
  <td align="center">
    <br>
    <input type="hidden" name="mode" value="admin">
    <input type="hidden" name="action" value="module">
    <input type="hidden" name="type" value="confirm">
    <input type="submit" value="削除する">
  </td>
</tr>
</form>
</table>
</center>
<br>
_EOT_;
?>