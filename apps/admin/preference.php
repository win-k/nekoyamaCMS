<?php
////////////////////////////////////////////////////////////////////////
// アクセス制限

  if(ereg("admin/config.php", $_SERVER['PHP_SELF']))
  {
    header("Location: ../admin.php");
    exit();
  }

////////////////////////////////////////////////////////////////////////
// 基本設定の変更

  if($input_type == "update")
  {
    if(strtoupper($REQUEST_METHOD) == "POST" &&
       ereg($PHP_SELF, $HTTP_REFERER))
    {
      $admin_query = "select name,content from wps_general order by id asc";
      $admin_name_data = mysql_query($admin_query);
      while($admin_name = mysql_fetch_row($admin_name_data))
      {
        $admin_query = "update wps_general set content='"
                     . $_POST[$admin_name[0]] . "'";
        if($_POST[$admin_name[0]] != $admin_name[1])
        {
          $admin_query .= ",date=now()";
        }
        $admin_query .= " where name='${admin_name[0]}'";
        mysql_query($admin_query, $db);
      }
    }
    header("Location: admin.php?mode=admin&action=preference");
  }

////////////////////////////////////////////////////////////////////////
// 設定変更画面の表示

  $admin_query = "select * from wps_general_category"
               . " order by id asc";
  $admin_cat_data = mysql_query($admin_query, $db);
  while($admin_cat = mysql_fetch_assoc($admin_cat_data))
  {
    $page_content .= <<<_EOT_
<table border="0" cellspacing="0" cellpadding="2" width="100%">
<form method="POST" action="admin.php">
<tr>
  <td bgcolor="#cccccc">
    ▼${admin_cat['category']}
  </td>
</tr>
</table>
<br>
<table border="0" cellspacing="0" cellpadding="2">
_EOT_;

    $admin_query = "select * from wps_general"
                 . " where cid=${admin_cat['id']}"
                 . " order by id asc";
    $admin_pref_data = mysql_query($admin_query, $db);
    $page_content .= '<table border="0" cellspacing="0" cellpadding="2">';
    while($admin_pref = mysql_fetch_assoc($admin_pref_data))
    {
      $admin_pref['content'] = htmlspecialchars($admin_pref['content']);
      $page_content .= <<<_EOT_
<tr>
  <td>
    ${admin_pref['comment']}
  </td>
  <td>
_EOT_;

      if($admin_pref['cid'] == 3)
      {
        $page_content .= '&nbsp;<input type="text" size="4"'
                       . ' name="' . $admin_pref['name']
                       . '" value="' . $admin_pref['content']
                       . '">';
      }
      else
      {
        $page_content .= '&nbsp;<input type="text" size="40"'
                       . ' name="' . $admin_pref['name']
                       . '" value="' . $admin_pref['content']
                       . '">';
      }

      $page_content .= <<<_EOT_
  </td>
</tr>
_EOT_;
    }

    $page_content .= '</table><br>';
  }
  $page_content .= <<<_EOT_
<table border="0" cellspacing="0" cellpadding="5" width="100%">
<tr>
  <td align="center">
    <input type="hidden" name="mode" value="admin">
    <input type="hidden" name="action" value="preference">
    <input type="hidden" name="type" value="update">
    <input type="submit" value="変更する">
    <input type="reset" value="やり直す">
  </td>
</tr>
</form>
</table>
<br>
_EOT_;
?>