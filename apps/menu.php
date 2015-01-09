<?php
////////////////////////////////////////////////////////////////////////
// アクセス制限

  if(ereg("menu.php", $_SERVER['PHP_SELF']))
  {
    header("Location: index.php");
    exit();
  }

////////////////////////////////////////////////////////////////////////
// ページヘッダのリンクとメニューの取得

  $page_header = "|";
  $index_query = "select internal_name,display_name"
               . " from wps_menu where active='Y' order by weight";
  $menu_data = mysql_query($index_query, $db);
  while($active_menu = mysql_fetch_assoc($menu_data))
  {
    $menu_item = $active_menu['internal_name'];

    // ページヘッダのリンク
    $index_query = "select name,item,header from wps_${menu_item}"
                 . " where header='Y' order by weight";
    $header_data = mysql_query($index_query, $db);

    while($active_header = mysql_fetch_assoc($header_data))
    {
      $active_header['item'] = str_replace("_page_base_",
                                           $page_base,
                                           $active_header['item']);
      $active_header['item'] = str_replace("_item_content_",
                                           $active_header['name'],
                                           $active_header['item']);
      $page_header .= strip_tags($active_header['item'], "<a><img>");
      $page_header .= "&nbsp;|";
    }

    $page_menu .= <<<_EOT_
<table border="0" cellspacing="0" cellpadding="0" width="150">
<tr>
  <td bgcolor="#333333"><img src="images/bk.gif" width="1" height="1"></td>
  <td bgcolor="#333333"><img src="images/bk.gif" width="148" height="1"></td>
  <td bgcolor="#333333"><img src="images/bk.gif" width="1" height="1"></td>
</tr>
<tr>
  <td bgcolor="#333333"><img src="images/bk.gif" width="1" height="1"></td>
  <td bgcolor="#4682b4">
    <table border="0" cellspacing="0" cellpadding="2" width="148">
    <tr>
      <td>
        &nbsp;<font color="#ffffff">
        <b>${active_menu['display_name']}</b>
        </font>
      </td>
    </tr>
    </table>
  </td>
  <td bgcolor="#333333"><img src="images/bk.gif" width="1" height="1"></td>
</tr>
<tr>
  <td colspan="3" bgcolor="#333333"><img src="images/bk.gif" width="1" height="1"></td>
</tr>
<tr>
  <td bgcolor="#333333"><img src="images/bk.gif" width="1" height="1"></td>
  <td>
    <table border="0" cellspacing="0" cellpadding="5" width="148">
    <tr>
      <td>
_EOT_;

    // メニュー
    $index_query = "select name,item,header from wps_${menu_item}"
                 . " where active='Y' order by weight";
    $item_data = mysql_query($index_query, $db);
    while($active_item = mysql_fetch_assoc($item_data))
    {
      $active_item['item'] = str_replace("_page_base_",
                                         $page_base,
                                         $active_item['item']);
      $active_item['item'] = str_replace("_item_content_",
                                         $active_item['name'],
                                         $active_item['item']);
      $page_menu .= $active_item['item'];
    }

    $page_menu .= <<<_EOT_
      </td>
    </tr>
    </table>
  </td>
  <td bgcolor="#333333"><img src="images/bk.gif" width="1" height="1"></td>
</tr>
<tr>
  <td colspan="3" bgcolor="#333333"><img src="images/bk.gif" width="1" height="1"></td>
</tr>
<tr>
  <td colspan="3"><img src="images/bk.gif" width="1" height="10"></td>
</tr>
</table>
_EOT_;
  }
?>