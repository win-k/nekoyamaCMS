<?php
////////////////////////////////////////////////////////////////////////
// アクセス制限

  if(ereg("modules/link.php", $_SERVER['PHP_SELF']))
  {
    header("Location: ../index.php");
    exit();
  }

////////////////////////////////////////////////////////////////////////
// タイトル

  $page_content .= <<<_EOT_
<table border="0" cellspacing="0" cellpadding="0">
<tr>
  <td><img src="images/bk.gif" width="1" height="15"></td>
</tr>
<tr>
  <td><img src="images/link.gif" width="45" height="12"></td>
</tr>
<tr>
  <td><img src="images/bk.gif" width="1" height="15"></td>
</tr>
</table>
_EOT_;

////////////////////////////////////////////////////////////////////////
// 初期画面

  if(!isset($input_type))
  {
    // 最新のリンク
    $page_content .= "■最新の5件<br>"
                  . '<table border="0" cellspacing="2" cellpadding="0">';

    $link_query = "select id,title,date from wps_link"
                . " where active = 'Y' order by date desc limit 0,5";
    $link_new_data = mysql_query($link_query, $db);
    while($link_new = mysql_fetch_assoc($link_new_data))
    {
      $page_content .= <<<_EOT_
<tr>
  <td>
    <a href="${page_base}?action=link&amp;type=jump&amp;id=${link_new['id']}">
      ${link_new['title']}
    </a>
  </td>
  <td>
    &nbsp;&nbsp;${link_new['date']} 登録
  </td>
</tr>
_EOT_;
    }
    $page_content .= '</table><br>';

    // 人気のリンク
    $page_content .= "■人気の5件<br>"
                   . '<table border="0" cellspacing="2" cellpadding="0">';

    $link_show = 1;
    $link_query = "select id,title,count from wps_link"
                . " where active = 'Y'"
                . " order by count desc,date desc limit 0,5";
    $link_rank_data = mysql_query($link_query, $db);
    while($link_rank = mysql_fetch_assoc($link_rank_data))
    {
      $page_content .= <<<_EOT_
<tr>
  <td>
    <a href="${page_base}?action=link&amp;type=jump&amp;id=${link_rank['id']}">
      ${link_rank['title']}
    </a>
  </td>
  <td>
    &nbsp;&nbsp;${link_rank['count']} Hits
  </td>
</tr>
_EOT_;
    }
    $page_content .= '</table><br>';

    // カテゴリ表示
    $select_option = "";
    $page_content .= "■カテゴリ別<br>"
                   . '<table border="0" cellspacing="2" cellpadding="0">';

    $link_query = "select id,category,count from wps_link_category"
                . " where active='Y' order by weight asc";
    $link_cat_data = mysql_query($link_query, $db);
    while($link_cat = mysql_fetch_assoc($link_cat_data))
    {
      $page_content .= <<<_EOT_
<tr>
  <td>
    <a href="${page_base}?action=link&amp;type=disp&amp;id=${link_cat['id']}">
      ${link_cat['category']}
    </a>
  </td>
  <td>&nbsp;( ${link_cat['count']} )</td>
</tr>
_EOT_;
      $select_option .= '<option value="' . $link_cat['id'] . '">'
                      . $link_cat['category'];
    }
    $page_content .= '</table><br>';

    // 登録フォーム表示
    $page_content .= <<<_EOT_
■新規登録<br>
<table border="0" cellspacing="2" cellpadding="0">
<form method="POST" action="${page_base}">
<tr>
  <td>名前</td>
</tr>
<tr>
  <td><input type="text" name="name" size="22" maxlength="64"></td>
</tr>
<tr>
  <td>カテゴリ</td>
</tr>
<tr>
  <td>
    <select name="cat">
      ${select_option}
    </select>
  </td>
</tr>
<tr>
  <td>タイトル</td>
</tr>
<tr>
  <td><input type="text" name="title" size="50" maxlength="255"></td>
</tr>
<tr>
  <td>URL</td>
</tr>
<tr>
  <td><input type="text" name="url" size="50" maxlength="255"></td>
</tr>
<tr>
  <td>コメント</td>
</tr>
<tr>
  <td><textarea name="comment" cols="50" rows="6"></textarea></td>
</tr>
<tr>
  <td>
    <br>
    <input type="submit" value="登録する">
    <input type="reset" value="書き直す">
    <input type="hidden" name="action" value="link">
    <input type="hidden" name="type" value="regist">
  </td>
</tr>
</form>
</table>
<br>
_EOT_;
  }

////////////////////////////////////////////////////////////////////////
// リンク表示

  elseif($input_type == "disp")
  {
    $page_content .= <<<_EOT_
<center>
[&nbsp;
<a href="${page_base}?action=link&amp;type=disp&amp;id=${input_id}&amp;order=count">
人気でソート
</a>
&nbsp;|&nbsp;
<a href="${page_base}?action=link&amp;type=disp&amp;id=${input_id}&amp;order=date">
日付でソート
</a>
&nbsp;]
<br><br>
_EOT_;

    if(!isset($input_order))
    {
      $input_order = "date";
    }
    if(!isset($input_page))
    {
      $input_page = 1;
    }
    $link_start = $input_page * $page_link_show - $page_link_show;

    $link_query = "select * from wps_link"
                . " where cid = ${input_id} and active = 'Y'"
                . " order by ${input_order} desc";
    if($input_order == "count")
    {
      $link_query .= ",date desc";
    }
    $link_query .= " limit ${link_start},${page_link_show}";
    $link_disp_data = mysql_query($link_query, $db);
    while($link_disp = mysql_fetch_assoc($link_disp_data))
    {
        $page_content .= <<<_EOT_
<table border="0" cellspacing=0" cellpadding="5" width="95%">
<tr>
  <td bgcolor="#cccccc">
    <a href="${page_base}?action=link&amp;type=jump&amp;id=${link_disp['id']}">
      [ ${link_disp['count']} ] ${link_disp['title']}
    </a>
  </td>
  <td bgcolor="#cccccc" align="right">
    ${link_disp['username']} / ${link_disp['date']}&nbsp;
  </td>
</tr>
<tr>
  <td colspan="2" bgcolor="#eeeeee">
    ${link_disp['content']}
  </td>
</tr>
</table>
<br>
_EOT_;
    }

    $link_query = "select count(id) from wps_link"
                . " where cid = ${input_id} and active = 'Y'";
    $link_result = mysql_query($link_query, $db);
    $link_rows = mysql_result($link_result, 0, 0);
    $link_max = floor($link_rows / $page_link_show);
    if($link_rows % $page_link_show != 0)
    {
      $link_max++;
    }
    $link_next = $input_page + 1;
    $link_prev = $input_page - 1;
    if($link_max >= $link_next && $link_prev > 0)
    {
      $page_content .= <<<_EOT_
[&nbsp;
<a href="${page_base}?action=link&amp;type=disp&amp;id=${input_id}&amp;order=${input_order}&amp;page=${link_prev}">
  前の ${page_link_show} 件
</a>
&nbsp;|&nbsp;
<a href="${page_base}?action=link&amp;type=disp&amp;id=${input_id}&amp;order=${input_order}&amp;page=${link_next}">
  次の ${page_link_show} 件
</a>
&nbsp;]
_EOT_;
    }
    elseif($link_max >= $link_next && $link_prev == 0)
    {
      $page_content .= <<<_EOT_
[&nbsp;
<a href="${page_base}?action=link&amp;type=disp&amp;id=${input_id}&amp;order=${input_order}&amp;page=${link_next}">
  次の ${page_link_show} 件
</a>
&nbsp;]
_EOT_;
    }
    elseif($link_max < $link_next && $link_prev > 0)
    {
      $page_content .= <<<_EOT_
[&nbsp;
<a href="${page_base}?action=link&amp;type=disp&amp;id=${input_id}&amp;order=${input_order}&amp;page=${link_prev}">
  前の ${page_link_show} 件
</a>
&nbsp;]
_EOT_;
    }

    $page_content .= '</center><br>';
  }

////////////////////////////////////////////////////////////////////////
// クリック数のカウント

  elseif($input_type == "jump")
  {
    $link_query = "update wps_link"
                . " set count = count+1"
                . " where id = ${input_id}";
    mysql_query($link_query, $db);

    $link_query = "select url from wps_link"
                . " where id = ${input_id}";
    $link_result = mysql_query($link_query);
    $link_url = mysql_result($link_result, 0, 0);

    header("Location: ${link_url}");
  }

////////////////////////////////////////////////////////////////////////
// 検索結果の表示

  elseif($input_type == "search")
  {
    $page_content .= "<center>";

    $link_query = "select * from wps_link"
                . " where id = ${input_id}";
    $link_search_data = mysql_query($link_query, $db);
    $link_search = mysql_fetch_assoc($link_search_data);

    $page_content .= <<<_EOT_
<table border="0" cellspacing=0" cellpadding="5" width="95%">
<tr>
  <td bgcolor="#cccccc">
    <a href="${page_base}?action=link&amp;type=jump&amp;id=${link_search['id']}">
      [ ${link_search['count']} ] ${link_search['title']}
    </a>
  </td>
  <td bgcolor="#cccccc" align="right">
    ${link_search['username']} / ${link_search['date']}&nbsp;
  </td>
</tr>
<tr>
  <td colspan="2" bgcolor="#eeeeee">
    ${link_search['content']}
  </td>
</tr>
</table>
<br>
<a href="javascript:history.back();">[ 戻る ]</a>
<br>
_EOT_;
    $page_content .= "</center>";
  }

////////////////////////////////////////////////////////////////////////
// リンク登録

  elseif($input_type == "regist" && strtoupper($REQUEST_METHOD) == "POST")
  {
    if(!$input_name)
    {
      error_message("名前が入力されていません。");
    }
    elseif(!$input_title)
    {
      error_message("タイトルが入力されていません。");
    }
    elseif(!$input_url)
    {
      error_message("URLが入力されていません。");
    }
    elseif(!preg_match("/^http\:\/\/[\w\d\:\/\~\?\&\-\.]+/i", $input_url))
    {
      error_message("URLの形式が不正です。");
    }
    elseif(!$input_comment)
    {
      error_message("コメントが入力されていません。");
    }
    $input_name = htmlspecialchars($input_name);
    $input_title = htmlspecialchars($input_title);
    $input_url = htmlspecialchars($input_url);
    $input_comment = htmlspecialchars($input_comment);
    $input_comment = preg_replace("/\r\n/", "<br>", $input_comment);
    $input_comment = preg_replace("/(\r|\n)/", "<br>", $input_comment);

    $link_query = "insert into wps_link"
                . "(cid,title,url,date,content,host,username,active)"
                . " values(${input_cat},'${input_title}',"
                . "'${input_url}',now(),'${input_comment}',"
                . "'${HTTP_HOST}','${input_name}','Y')";

    $link_result = mysql_query($link_query, $db);
    if(mysql_errno($db) != 0)
    {
      error_message("システムエラーです。管理者に連絡してください。");
    }
    else
    {
      $link_query = "update wps_link_category"
                  . " set count=count+1"
                  . " where id=${input_cat}";
      $link_result = mysql_query($link_query, $db);
      if(mysql_errno($db) != 0)
      {
        error_message("システムエラーです。管理者に連絡してください。");
      }
      else
      {
        header("Location: ${page_base}?action=link");
      }
    }
  }
?>