<?php
////////////////////////////////////////////////////////////////////////
// アクセス制限

  if(ereg("modules/poll.php", $_SERVER['PHP_SELF']))
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
  <td><img src="images/poll.gif" width="41" height="12"></td>
</tr>
<tr>
  <td><img src="images/bk.gif" width="1" height="15"></td>
</tr>
</table>
_EOT_;

////////////////////////////////////////////////////////////////////////
// 記録

  if($input_type == "vote")
  {
    if(isset($input_poll))
    {
      $poll_query = "insert into wps_poll(vote,date,host)"
                  . " values('${input_poll}',now(),'${HTTP_HOST}')";
      mysql_query($poll_query, $db);
    }
    else
    {
      $page_content .= '<font color="#ff0000">'
                    . 'エラー ： 項目を選択してから投票してください。'
                    . '</font><br><br>';
    }
  }

////////////////////////////////////////////////////////////////////////
// 結果表示

  $poll_query = "select iid,name from wps_menu_poll"
              . " where iid <> 0 order by iid asc";
  $poll_item_data = mysql_query($poll_query);

  $poll_query = "select item from wps_menu_poll"
              . " where name='タイトル'";
  $poll_result = mysql_query($poll_query, $db);
  $vote_theme = mysql_result($poll_result, 0, 0);
  $vote_theme = preg_replace("/<br>/i", "", $vote_theme);

  $poll_query = "select max(id) from wps_poll";
  $poll_result = mysql_query($poll_query, $db);
  $vote_count = mysql_result($poll_result, 0, 0);
  $page_content .= <<<_EOT_
■テーマ ： ${vote_theme} [ 総投票数 ： ${vote_count} ]
<table border="0" cellspacing="1" cellpadding="5" width="100%">
<tr>
  <td bgcolor="#cccccc">投票項目</td>
  <td bgcolor="#cccccc">投票数</td>
  <td bgcolor="#cccccc">
    <img src="images/bk.gif" width="400" height="1">
  </td>
</tr>
_EOT_;
  while($poll_item = mysql_fetch_assoc($poll_item_data))
  {
    $poll_query = "select count(vote) from wps_poll"
                . " where vote = ${poll_item['iid']}";
    $poll_result = mysql_query($poll_query, $db);
    $vote_data = mysql_result($poll_result, 0, 0);
    if($vote_count > 0)
    {
      $vote_length = floor($vote_data / $vote_count * 400);
    }
    $page_content .= '<tr><td bgcolor="#eeeeee"><nobr>'
                   . $poll_item['name']
                   . '</nobr></td>'
                   . '<td bgcolor="#eeeeee" align="right">'
                   . $vote_data
                   . '</td><td bgcolor="#eeeeee">';
    if($vote_length > 0)
    {
      $page_content .= <<<_EOT_
<table border="0" cellspacing="0" cellpadding="0">
<tr>
  <td bgcolor="#ff0000">
    <img src="images/bk.gif" width="${vote_length}" height="10">
  </td>
</tr>
</table>
_EOT_;
    }
    $page_content .= '</td></tr>';
  }
  $page_content .= '</table>';
?>