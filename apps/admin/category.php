<?php
////////////////////////////////////////////////////////////////////////
// アクセス制限

  if(ereg("admin/category.php", $_SERVER['PHP_SELF']))
  {
    header("Location: ../admin.php");
    exit();
  }

////////////////////////////////////////////////////////////////////////
// カテゴリ表示順移動

  if(preg_match("/^move/", $input_type))
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
      $admin_query = "update wps_link_category set weight=${admin_back}"
                   . " where weight=${admin_weight}";
      mysql_query($admin_query, $db);

      // 編集中のカテゴリを移動
      $admin_query = "update wps_link_category set weight=${admin_forward}"
                   . " where id=${input_id}";
      mysql_query($admin_query, $db);
    }
  }

////////////////////////////////////////////////////////////////////////
// カテゴリ表示順の重複解消

  elseif($input_type == "conflict")
  {
    if(ereg($PHP_SELF, $HTTP_REFERER))
    {
      $admin_query = "select id from wps_link_category"
                   . " order by weight";
      $admin_cat_data = mysql_query($admin_query, $db);
      $admin_weight = 1;
      while($admin_cat = mysql_fetch_assoc($admin_cat_data))
      {
        $admin_query = "update wps_link_category"
                     . " set weight=${admin_weight}"
                     . " where id=${admin_cat['id']}";
        mysql_query($admin_query, $db);
        $admin_weight++;
      }
    }
  }

////////////////////////////////////////////////////////////////////////
// カテゴリ有効化

  elseif($input_type == "activate")
  {
    if(ereg($PHP_SELF, $HTTP_REFERER))
    {
      $admin_query = "update wps_link_category set active='Y'"
                   . " where id=${input_id}";
      mysql_query($admin_query, $db);
    }
  }

////////////////////////////////////////////////////////////////////////
// カテゴリ無効化

  elseif($input_type == "deactivate")
  {
    if(ereg($PHP_SELF, $HTTP_REFERER))
    {
      $admin_query = "update wps_link_category set active='N'"
                   . " where id=${input_id}";
      mysql_query($admin_query, $db);
    }
  }


////////////////////////////////////////////////////////////////////////
// カテゴリ追加

  elseif($input_type == "new")
  {
    if(strtoupper($REQUEST_METHOD) == "POST" &&
       ereg($PHP_SELF, $HTTP_REFERER))
    {
      $admin_query = "select max(weight) from wps_link_category";
      $admin_result = mysql_query($admin_query, $db);
      $admin_cat_weight = mysql_result($admin_result, 0, 0);
      $admin_cat_weight++;

      $admin_query = "insert into wps_link_category"
                   . "(category,weight,count,active)"
                   . " values('${input_category}',${admin_cat_weight},"
                   . "0,'${input_active}')";
      mysql_query($admin_query, $db);
    }
  }

////////////////////////////////////////////////////////////////////////
// カテゴリ編集

  elseif($input_type == "edit")
  {
    if(strtoupper($REQUEST_METHOD) == "POST" &&
       ereg($PHP_SELF, $HTTP_REFERER))
    {
      $admin_query = "update wps_link_category"
                   . " set category='${input_category}'"
                   . " where id=${input_id}";
      mysql_query($admin_query, $db);
    }
  }

////////////////////////////////////////////////////////////////////////
// カテゴリ削除

  elseif($input_type == "delete")
  {
    if(ereg($PHP_SELF, $HTTP_REFERER))
    {
      // カテゴリの削除
      $admin_query = "delete from wps_link_category"
                   . " where id=${input_id}";
      mysql_query($admin_query, $db);

      // リンクの削除
      $admin_query = "delete from wps_link where cid=${input_id}";
      mysql_query($admin_query, $db);
    }
    header("Location: admin.php?mode=admin&action=category&type=conflict");
  }

////////////////////////////////////////////////////////////////////////
// カテゴリ削除の確認

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
<a href="admin.php?mode=admin&amp;action=category&amp;type=delete&amp;id=${input_id}">
はい
</a>
&nbsp;/&nbsp;
<a href="admin.php?mode=admin&amp;action=category">
いいえ
</a>
&nbsp;]
</center>
<br>
_EOT_;
    }
  }

////////////////////////////////////////////////////////////////////////
// カテゴリ編集画面

  $page_content .= <<<_EOT_
<table border="0" cellspacing="0" cellpadding="2" width="100%">
<tr>
  <td bgcolor="#cccccc">▼カテゴリの編集</td>
</tr>
</table>
<br>
_EOT_;

  if($input_type == "editview")
  {
    $admin_query = "select category from wps_link_category"
                 . " where id=${input_id}";
    $admin_result = mysql_query($admin_query, $db);
    $admin_cat = mysql_result($admin_result, 0, 0);

    $page_content .= <<<_EOT_
<center>
<table border="0" cellspacing="0" cellpadding="2">
<form method="POST" action="admin.php">
<tr>
  <td>
    <input type="text" name="category" size="40" maxlength="255" value="${admin_cat}">
  </td>
</tr>
<tr>
  <td align="center">
    <br>
    <input type="hidden" name="mode" value="admin">
    <input type="hidden" name="action" value="category">
    <input type="hidden" name="type" value="edit">
    <input type="hidden" name="id" value="${input_id}">
    <input type="submit" value="編集する">
  </td>
</tr>
</form>
</table>
</center>
<br>
_EOT_;
  }

////////////////////////////////////////////////////////////////////////
// カテゴリリスト

  $page_content .= <<<_EOT_
<table border="1" cellspacing="1" cellpadding="2" width="100%">
<tr>
  <td align="center">
<a href="admin.php?mode=admin&amp;action=category&amp;type=conflict">
順位
</a>
  </td>
  <td align="center">移動</td>
  <td align="center">カテゴリ</td>
  <td align="center">リンク数</td>
  <td align="center">表示</td>
  <td colspan="2" align="center">機能</td>
</tr>
_EOT_;

  $admin_query = "select max(weight) from wps_link_category";
  $admin_result = mysql_query($admin_query, $db);
  $admin_weight_max = mysql_result($admin_result, 0, 0);

  $admin_query = "select * from wps_link_category"
               . " order by weight";
  $admin_cat_data = mysql_query($admin_query, $db);
  while($admin_cat = mysql_fetch_assoc($admin_cat_data))
  {
    if($admin_cat['active'] == 'N')
    {
      $admin_cat_type = "activate";
      $admin_cat_status = "×";
    }
    else
    {
      $admin_cat_type = "deactivate";
      $admin_cat_status = "○";
    }

    $page_content .= <<<_EOT_
<tr>
  <td align="center">${admin_cat['weight']}</td>
  <td align="center">
_EOT_;

    if($admin_cat['weight'] > 1)
    {
      $page_content .= <<<_EOT_
<a href="admin.php?mode=admin&amp;action=category&amp;type=moveup&amp;id=${admin_cat['id']}&amp;weight=${admin_cat['weight']}">
▲
</a>
_EOT_;
    }

    if($admin_cat['weight'] < $admin_weight_max)
    {
      $page_content .= <<<_EOT_
<a href="admin.php?mode=admin&amp;action=category&amp;type=movedown&amp;id=${admin_cat['id']}&amp;weight=${admin_cat['weight']}">
▼
</a>
_EOT_;
    }

    $page_content .= <<<_EOT_
  </td>
  <td align="center">${admin_cat['category']}</td>
  <td align="center">${admin_cat['count']}</td>
  <td align="center">
    <a href="admin.php?mode=admin&amp;action=category&amp;type=${admin_cat_type}&amp;id=${admin_cat['id']}">
      ${admin_cat_status}
    </a>
  </td>
  <td align="center">
    <a href="admin.php?mode=admin&amp;action=category&amp;type=editview&amp;id=${admin_cat['id']}">
      編集
    </a>
  </td>
  <td align="center">
    <a href="admin.php?mode=admin&amp;action=category&amp;type=confirm&amp;id=${admin_cat['id']}">
      削除
    </a>
  </td>
</tr>
_EOT_;
  }

  $page_content .= <<<_EOT_
</table>
※「順位」をクリックすると表示順の重複を解消
<br><br>
_EOT_;

////////////////////////////////////////////////////////////////////////
// カテゴリ追加画面

  $page_content .= <<<_EOT_
<table border="0" cellspacing="0" cellpadding="2" width="100%">
<tr>
  <td bgcolor="#cccccc">▼カテゴリの追加</td>
</tr>
</table>
<br>
<center>
<table border="0" cellspacing="0" cellpadding="2">
<form method="POST" action="admin.php">
<tr>
  <td>カテゴリ</td>
</tr>
<tr>
  <td>
    <input type="text" name="category" size="40" maxlength="255">
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
    <input type="hidden" name="action" value="category">
    <input type="hidden" name="type" value="new">
    <input type="submit" value="追加する">
  </td>
</tr>
</form>
</table>
</center>
<br>
_EOT_;
?>