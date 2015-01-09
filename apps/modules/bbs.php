<?php
////////////////////////////////////////////////////////////////////////
// アクセス制限

  if(ereg("modules/bbs.php", $_SERVER['PHP_SELF']))
  {
    header("Location: ../index.php");
    exit();
  }

////////////////////////////////////////////////////////////////////////
// ページング用データ

  $bbs_query = "select count(id) from wps_bbs"
             . " where cid = 0 and active='Y'";
  $bbs_result = mysql_query($bbs_query, $db);
  $bbs_rows = mysql_result($bbs_result, 0, 0);
  if(!isset($input_page))
  {
    $input_page = 1;
  }
  $bbs_max = floor($bbs_rows / $page_bbs_show);
  if($bbs_rows % $page_bbs_show != 0)
  {
    $bbs_max++;
  }
  $bbs_start = $input_page * $page_bbs_show - $page_bbs_show;
  $bbs_prev = $input_page - 1;
  $bbs_next = $input_page + 1;

////////////////////////////////////////////////////////////////////////
// タイトル

  $page_content .= <<<_EOT_
<table border="0" cellspacing="0" cellpadding="0">
<tr>
  <td><img src="images/bk.gif" width="1" height="15"></td>
</tr>
<tr>
  <td><img src="images/bbs.gif" width="43" height="12"></td>
</tr>
<tr>
  <td><img src="images/bk.gif" width="1" height="15"></td>
</tr>
</table>
_EOT_;

////////////////////////////////////////////////////////////////////////
// メッセージの表示

  if(!isset($input_type))
  {
    // ヘッダ部分の表示
    $page_content .= '<table border="0" cellspacing="2" cellpadding="0" width="100%">'
                  . '<tr>';

    if($bbs_prev < 1)
    {
      $page_content .= <<<_EOT_
<td width="1">
  <nobr>■ ページ終了</nobr>
</td>
_EOT_;
    }
    else
    {
      $page_content .= <<<_EOT_
<td width="1">
  <nobr>
    <a href="${page_base}?action=bbs&amp;page=${bbs_prev}">
      ▲ 前のページ
    </a>
  </nobr>
</td>
_EOT_;
    }

    $page_content .= <<<_EOT_
<td align="center">
  <a href="${page_base}?action=bbs&amp;type=new">
    [ 新しい記事を投稿する ]
  </a>
</td>
_EOT_;

    if($bbs_next > $bbs_max)
    {
      $page_content .= <<<_EOT_
<td width="1">
  <nobr>ページ終了 ■</nobr>
</td>
_EOT_;
    }
    else
    {
      $page_content .= <<<_EOT_
<td width="1">
  <nobr>
    <a href="${page_base}?action=bbs&amp;page=${bbs_next}">
      次のページ ▼
    </a>
  </nobr>
</td>
_EOT_;
    }

    $page_content .= <<<_EOT_
</tr>
<tr>
  <td colspan="3" bgcolor="#333333"><img src="images/bk.gif" width="1" height="1"></td>
</tr>
</table>
<br>
_EOT_;

    // 親記事の表示
    $bbs_query = "select * from wps_bbs"
               . " where cid = 0 and active = 'Y'"
               . " order by pid desc"
               . " limit ${bbs_start},${page_bbs_show}";
    $bbs_parent_data = mysql_query($bbs_query, $db);
    while($bbs_parent = mysql_fetch_assoc($bbs_parent_data))
    {
      $page_content .= <<<_EOT_
<center>
<table border="0" cellspacing="0" cellpadding="4" width="95%">
<tr>
  <td bgcolor="#cccccc">
    <b>${bbs_parent['id']}.${bbs_parent['title']}</b>&nbsp;
_EOT_;

      if($bbs_parent['email'])
      {
        $page_content .= '- <a href="mailto:'
                       . $bbs_parent['email']
                       . '">'
                       . $bbs_parent['name']
                       . '</a>';
      }
      else
      {
        $page_content .= "- ${bbs_parent['name']}";
      }

      $page_content .= <<<_EOT_
    &nbsp;[ ${bbs_parent['date']} ]
  </td>
  <td align="right" bgcolor="#cccccc">
    <a href="${page_base}?action=bbs&amp;type=res&amp;pid=${bbs_parent['pid']}">
      [ 返信 ]
    </a>
  </td>
</tr>
<tr>
  <td colspan="2" bgcolor="#eeeeee">${bbs_parent['content']}</td>
</tr>
_EOT_;

      if($bbs_parent['homepage'])
      {
        $page_content .= <<<_EOT_
<tr>
  <td colspan="2" bgcolor="#eeeeee">
    <a href="${bbs_parent['homepage']}" target="_blank">
      ${bbs_parent['homepage']}
    </a>
  </td>
</tr>
_EOT_;
      }
      $page_content .= <<<_EOT_
<tr>
  <td colspan="2" bgcolor="#eeeeee"><img src="images/bk.gif" width="1" height="5"></td>
</tr>
_EOT_;

      // 子記事の表示
      $bbs_query = "select * from wps_bbs"
                 . " where pid = ${bbs_parent['pid']}"
                 . " and cid > 0 and active = 'Y'"
                 . " order by cid asc";
      $bbs_child_data = mysql_query($bbs_query, $db);
      while($bbs_child = mysql_fetch_assoc($bbs_child_data))
      {
        $page_content .= <<<_EOT_
<tr>
  <td colspan="2" bgcolor="#eeeeee" align="center">
    <table border="0" cellspacing="0" cellpadding="4" width="90%">
    <tr>
      <td bgcolor="#dddddd">
          <b>${bbs_child['id']}.${bbs_child['title']}</b>&nbsp;
_EOT_;

        if($bbs_child['email'])
        {
          $page_content .= '- <a href="mailto:'
                         . $bbs_child['email']
                         . '">'
                         . $bbs_child['name']
                         . '</a>';
        }
        else
        {
          $page_content .= "- ${bbs_child['name']}";
        }

        $page_content .= <<<_EOT_
          &nbsp;[ ${bbs_child['date']} ]
      </td>
      <td align="right" bgcolor="#dddddd">
        <a href="${page_base}?action=bbs&amp;type=res&amp;pid=${bbs_child['pid']}&amp;cid=${bbs_child['cid']}">
          [ 返信 ]
        </a>
      </td>
    </tr>
    <tr>
      <td colspan="2" bgcolor="#eeeeee">${bbs_child['content']}</td>
    </tr>
_EOT_;

        if($bbs_parent['homepage'])
        {
          $page_content .= <<<_EOT_
<tr>
  <td colspan="2" bgcolor="#eeeeee">
    <a href="${bbs_child['homepage']}" target="_blank">
      ${bbs_child['homepage']}
    </a>
  </td>
</tr>
_EOT_;
        }
        $page_content .= <<<_EOT_
<tr>
  <td colspan="2" bgcolor="#eeeeee"><img src="images/bk.gif" width="1" height="5"></td>
</tr>
</table>
  </td>
</tr>
_EOT_;
      }
      $page_content .= '</table></center><br>';
    }
  }

////////////////////////////////////////////////////////////////////////
// 検索結果の表示

  elseif($input_type == "search")
  {
    $bbs_query = "select * from wps_bbs"
               . " where id = ${input_id}";
    $bbs_story_data = mysql_query($bbs_query, $db);

    $bbs_story = mysql_fetch_assoc($bbs_story_data);
    $page_content .= <<<_EOT_
<center>
<table border="0" cellspacing="0" cellpadding="4" width="95%">
<tr>
  <td bgcolor="#cccccc">
    <b>${bbs_story['id']}.${bbs_story['title']}</b>&nbsp;
_EOT_;

    if($bbs_story['email'])
    {
      $page_content .= '- <a href="mailto:'
                     . $bbs_story['email']
                     . '">'
                     . $bbs_story['name']
                     . '</a>';
    }
    else
    {
      $page_content .= "- ${bbs_story['name']}";
    }

    $page_content .= <<<_EOT_
    &nbsp;[ ${bbs_story['date']} ]
  </td>
  <td align="right" bgcolor="#cccccc">
    <a href="${page_base}?action=bbs&amp;type=res&amp;pid=${bbs_story['pid']}&amp;cid=${bbs_story['cid']}">
      [ 返信 ]
    </a>
  </td>
</tr>
<tr>
  <td colspan="2" bgcolor="#eeeeee">${bbs_story['content']}</td>
</tr>
_EOT_;

    if($bbs_story['homepage'])
    {
      $page_content .= <<<_EOT_
<tr>
  <td colspan="2" bgcolor="#eeeeee">
    <a href="${bbs_story['homepage']}" target="_blank">
      ${bbs_story['homepage']}
    </a>
  </td>
</tr>
_EOT_;
    }
    $page_content .= <<<_EOT_
<tr>
  <td colspan="2" bgcolor="#eeeeee"><img src="images/bk.gif" width="1" height="5"></td>
</tr>
</table>
<br>
<a href="javascript:history.back();">[ 戻る ]</a><br>
<br>
_EOT_;
  }

////////////////////////////////////////////////////////////////////////
// 投稿画面

  elseif($input_type == "new" || $input_type == "res")
  {
    $page_content .= "<center>";

    if($input_type == "new")
    {
      $bbs_query = "select max(pid) from wps_bbs";
      $bbs_result = mysql_query($bbs_query, $db);
      $bbs_pid_max = mysql_result($bbs_result, 0, 0);
      $bbs_pid_max++;
      $input_pid = $bbs_pid_max;
      $page_content .= "<p>[ 新しい記事の投稿 ]</p>";
    }
    else
    {
      $bbs_query = "select * from wps_bbs"
                 . " where pid = ${input_pid}";
      if(isset($input_cid))
      {
        $bbs_query .= " and cid = ${input_cid}";
      }
      $bbs_article_data = mysql_query($bbs_query, $db);
      $bbs_article = mysql_fetch_assoc($bbs_article_data);
      $page_content .= <<<_EOT_
<table border="0" cellspacing="0" cellpadding="4" width="95%">
<tr>
  <td bgcolor="#cccccc">
    <b>${bbs_article['id']}.${bbs_article['title']}</b>&nbsp;-&nbsp;
    ${bbs_article['name']}&nbsp;
    [ ${bbs_article['date']} ]
  </td>
</tr>
<tr>
  <td bgcolor="#eeeeee">
    ${bbs_article['content']}
  </td>
</tr>
<tr>
  <td bgcolor="#eeeeee"><img src="images/bk.gif" width="1" height="5"></td>
</tr>
</table>
<p>[ 返信記事の投稿 ]</p>
_EOT_;
    }

    $page_content .= <<<_EOT_
<table border="0" cellspacing="2" cellpadding="0">
<form method="POST" action="${page_base}">
<tr>
  <td>タイトル</td>
</tr>
<tr>
  <td><input type="text" name="title" size="40" maxlength="255"></td>
</tr>
<tr>
  <td>名前</td>
</tr>
<tr>
  <td><input type="text" name="name" size="40" maxlength="255"></td>
</tr>
<tr>
  <td>ホームページ</td>
</tr>
<tr>
  <td><input type="text" name="homepage" size="40" maxlength="255"></td>
</tr>
<tr>
  <td>メールアドレス</td>
</tr>
<tr>
  <td><input type="text" name="email" size="40" maxlength="255"></td>
</tr>
<tr>
  <td>メッセージ</td>
</tr>
<tr>
  <td><textarea name="message" cols="40" rows="8"></textarea></td>
</tr>
<tr>
  <td align="center">
    <br>
    <input type="hidden" name="action" value="bbs">
    <input type="hidden" name="type" value="post${input_type}">
    <input type="hidden" name="pid" value="${input_pid}">
    <input type="submit" value="投稿する">
    <input type="reset" value="修正する">
  </td>
</tr>
</form>
</table>
_EOT_;
  }

////////////////////////////////////////////////////////////////////////
// 記録処理

  elseif($input_type == "postnew" || $input_type == "postres")
  {
    if($input_message == "")
    {
      error_message("メッセージを入力してください");
    }
    else
    {
      if(!$input_title)
      {
        $input_title = "無題";
      }
      if(!$input_name)
      {
        $input_name = "匿名";
      }
      if(!preg_match("/^http\:\/\/[\w\d\:\~\/\-\.\#\?\&]+/", $input_homepage))
      {
        $input_homepage = "";
      }
      if(!preg_match("/[\w\d\-\.]+\@[\w\d\-\.]+/i", $input_email))
      {
        $input_email = "";
      }
      $input_title = htmlspecialchars($input_title);
      $input_name = htmlspecialchars($input_name);
      $input_homepae = htmlspecialchars($input_homepage);
      $input_email = htmlspecialchars($input_email);
      $input_message = htmlspecialchars($input_message);
      $input_message = preg_replace("/\t/",
                                    " ",
                                    $input_message);
      $input_message = preg_replace("/\r\n/",
                                    "<br>",
                                    $input_message);
      $input_message = preg_replace("/(\r|\n)/",
                                    "<br>",
                                    $input_message);

      $bbs_query = "insert into wps_bbs"
                 . " values(null,${input_pid},0,"
                 . "'${input_title}','${input_name}',"
                 . "'${input_homepage}','${input_email}',"
                 . "now(),'${input_message}','${HTTP_HOST}','Y')";
      if($input_type == "postres")
      {
        $bbs_query = "select max(cid) from wps_bbs"
                   . " where pid = ${input_pid}";
        $bbs_result = mysql_query($bbs_query, $db);
        $bbs_cid_max = mysql_result($bbs_result, 0, 0);
        $bbs_cid_max++;
        $bbs_query = "insert into wps_bbs"
                   . " values(null,${input_pid},${bbs_cid_max},"
                   . "'${input_title}','${input_name}',"
                   . "'${input_homepage}','${input_email}',now(),"
                   . "'${input_message}','${HTTP_HOST}','Y')";
      }
      mysql_query($bbs_query);
      header("Location: ${page_base}?action=bbs");
    }
  }

////////////////////////////////////////////////////////////////////////
// フッタ部分の表示

  if(!isset($input_type))
  {
    $page_content .= <<<_EOT_
<table border="0" cellspacing="2" cellpadding="0" width="100%">
<tr>
  <td colspan="3" bgcolor="#333333"><img src="images/bk.gif" width="1" height="1"></td>
</tr>
<tr>
_EOT_;

    if($bbs_prev < 1)
    {
      $page_content .= <<<_EOT_
<td width="1">
  <nobr>■ ページ終了</nobr>
</td>
_EOT_;
    }
    else
    {
      $page_content .= <<<_EOT_
<td width="1">
  <nobr>
    <a href="${page_base}?action=bbs&amp;page=${bbs_prev}">
      ▲ 前のページ
    </a>
  </nobr>
</td>
_EOT_;
    }

    $page_content .= <<<_EOT_
<td align="center">
  <a href="${page_base}?action=bbs&amp;type=new">
    [ 新しい記事を投稿する ]
  </a>
</td>
_EOT_;

    if($bbs_next > $bbs_max)
    {
      $page_content .= <<<_EOT_
<td width="1">
  <nobr>ページ終了 ■</nobr>
</td>
_EOT_;
    }
    else
    {
      $page_content .= <<<_EOT_
<td width="1">
  <nobr>
    <a href="${page_base}?action=bbs&amp;page=${bbs_next}">
      次のページ ▼
    </a>
  </nobr>
</td>
_EOT_;
    }

    $page_content .= '</tr></table><br>';
  }
?>