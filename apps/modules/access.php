<?php
////////////////////////////////////////////////////////////////////////
// アクセス制限

  if(ereg("modules/access.php", $_SERVER['PHP_SELF']))
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
  <td><img src="images/access.gif" width="66" height="12"></td>
</tr>
<tr>
  <td><img src="images/bk.gif" width="1" height="15"></td>
</tr>
</table>
_EOT_;

////////////////////////////////////////////////////////////////////////
// 解析結果を表示するリンク

  $page_content .= <<<_EOT_
<a href="${page_base}?action=access&amp;type=thismonth">
  ■今月のアクセス数
</a><br>
<a href="${page_base}?action=access&amp;type=month">
  ■月別アクセス数
</a><br>
<a href="${page_base}?action=access&amp;type=weekday">
  ■曜日別アクセス数
</a><br>
<a href="${page_base}?action=access&amp;type=time">
  ■時間帯別アクセス数
</a><br>
<a href="${page_base}?action=access&amp;type=agent">
  ■ユーザーエージェント一覧
</a><br>
<a href="${page_base}?action=access&amp;type=host">
  ■ホスト一覧
</a><br>
<a href="${page_base}?action=access&amp;type=referer">
  ■リンク元一覧
</a><br>
<br>
_EOT_;

////////////////////////////////////////////////////////////////////////
// 今月のアクセス数

  if($input_type == "thismonth")
  {
    $this_year = date("Y");
    $this_month = date("n");
    $days_of_month = date("t");

    $page_content .= <<<_EOT_
<table border="0" cellspacing="1" cellpadding="5" width="100%">
<tr>
  <td bgcolor="#cccccc"><nobr>日付</nobr></td>
  <td bgcolor="#cccccc"><nobr>カウント</nobr></td>
  <td bgcolor="#cccccc">
    <img src="images/bk.gif" width="400" height="10">
  </td>
</tr>
_EOT_;

    for($i = 1; $i <= $days_of_month; $i++)
    {
      $access_query = "select count(date) from wps_access"
                    . " where year(date) = ${this_year}"
                    . " and month(date) = ${this_month}"
                    . " and dayofmonth(date) = ${i}";
      $access_result = mysql_query($access_query, $db);
      $access_thismonth = mysql_result($access_result, 0, 0);
      if($access_count > 0)
      {
        $thismonth_length = floor($access_thismonth / $access_count * 400);
      }

      $page_content .= '<tr><td bgcolor="#eeeeee"><nobr>'
                     . sprintf("%02d/%02d", $this_month, $i)
                     . '</nobr></td>'
                     . '<td align="right" bgcolor="#eeeeee"><nobr>'
                     . $access_thismonth
                     . '</nobr></td><td bgcolor="#eeeeee">';
      if($thismonth_length > 0)
      {
        $page_content .= <<<_EOT_
<table border="0" cellspacing="0" cellpadding="0">
<tr>
  <td bgcolor="#ff0000">
    <img src="images/bk.gif" width="${thismonth_length}" height="10">
  </td>
</tr>
</table>
_EOT_;
      }
      else
      {
        $page_content .= '<img src="images/bk.gif" width="1" height="1">';
      }
      $page_content .= '</td></tr>';
    }
    $page_content .= "</table>";
  }

////////////////////////////////////////////////////////////////////////
// 月別アクセス数

  elseif($input_type == "month")
  {
    $page_content .= <<<_EOT_
<table border="0" cellspacing="1" cellpadding="5" width="100%">
<tr>
  <td bgcolor="#cccccc"><nobr>月</nobr></td>
  <td bgcolor="#cccccc"><nobr>カウント</nobr></td>
  <td bgcolor="#cccccc">
    <img src="images/bk.gif" width="400" height="10">
  </td>
</tr>
_EOT_;
    for($i = 1; $i <= 12; $i++)
    {
      $access_query = "select count(date) from wps_access"
                    . " where month(date) = ${i}";
      $access_result = mysql_query($access_query, $db);
      $access_month = mysql_result($access_result, 0, 0);
      if($access_count > 0)
      {
        $month_length = floor($access_month / $access_count * 400);
      }

      $page_content .= '<tr><td bgcolor="#eeeeee"><nobr>'
                     . sprintf("%02d", $i)
                     . '</nobr></td>'
                     . '<td align="right" bgcolor="#eeeeee"><nobr>'
                     . $access_month
                     . '</nobr></td><td bgcolor="#eeeeee">';
      if($month_length > 0)
      {
        $page_content .= <<<_EOT_
<table border="0" cellspacing="0" cellpadding="0">
<tr>
  <td bgcolor="#ff0000">
    <img src="images/bk.gif" width="${month_length}" height="10">
  </td>
</tr>
</table>
_EOT_;
      }
      else
      {
        $page_content .= '<img src="images/bk.gif" width="1" height="1">';
      }
      $page_content .= '</td></tr>';
    }
    $page_content .= "</table>";
  }

////////////////////////////////////////////////////////////////////////
// 曜日別アクセス数

  elseif($input_type == "weekday")
  {
    $weekday_string = array(NULL, '日', '月', '火', '水', '木', '金', '土');

    $page_content .= <<<_EOT_
<table border="0" cellspacing="1" cellpadding="5" width="100%">
<tr>
  <td bgcolor="#cccccc"><nobr>曜日</nobr></td>
  <td bgcolor="#cccccc"><nobr>カウント</nobr></td>
  <td bgcolor="#cccccc">
    <img src="images/bk.gif" width="400" height="10">
  </td>
</tr>
_EOT_;
    for($i = 1; $i <= 7; $i++)
    {
      $access_query = "select count(wday) from wps_access"
                    . " where wday = ${i}";
      $access_result = mysql_query($access_query, $db);
      $access_weekday = mysql_result($access_result, 0, 0);
      if($access_count > 0)
      {
        $weekday_length = floor($access_weekday / $access_count * 400);
      }

      $page_content .= '<tr><td bgcolor="#eeeeee"><nobr>'
                     . $weekday_string[$i]
                     . '</nobr></td>'
                     . '<td align="right" bgcolor="#eeeeee"><nobr>'
                     . $access_weekday
                     . '</nobr></td><td bgcolor="#eeeeee">';
      if($weekday_length > 0)
      {
        $page_content .= <<<_EOT_
<table border="0" cellspacing="0" cellpadding="0">
<tr>
  <td bgcolor="#ff0000">
    <img src="images/bk.gif" width="${weekday_length}" height="10">
  </td>
</tr>
</table>
_EOT_;
      }
      else
      {
        $page_content .= '<img src="images/bk.gif" width="1" height="1">';
      }
      $page_content .= '</td></tr>';
    }
    $page_content .= "</table>";
  }

////////////////////////////////////////////////////////////////////////
// 時間帯別アクセス数

  elseif($input_type == "time")
  {
    $page_content .= <<<_EOT_
<table border="0" cellspacing="1" cellpadding="5" width="100%">
<tr>
  <td bgcolor="#cccccc"><nobr>時間帯</nobr></td>
  <td bgcolor="#cccccc"><nobr>カウント</nobr></td>
  <td bgcolor="#cccccc">
    <img src="images/bk.gif" width="400" height="10">
  </td>
</tr>
_EOT_;
    for($i = 0; $i < 24; $i++)
    {
      $access_query = "select count(time) from wps_access"
                    . " where hour(time) = ${i}";
      $access_result = mysql_query($access_query, $db);
      $access_time = mysql_result($access_result, 0, 0);
      if($access_count > 0)
      {
        $time_length = floor($access_time / $access_count * 400);
      }

      $page_content .= '<tr><td bgcolor="#eeeeee"><nobr>'
                     . sprintf("%02d:00～%02d:59", $i, $i)
                     . '</nobr></td>'
                     . '<td align="right" bgcolor="#eeeeee"><nobr>'
                     . $access_time
                     . '</nobr></td><td bgcolor="#eeeeee">';
      if($time_length > 0)
      {
        $page_content .= <<<_EOT_
<table border="0" cellspacing="0" cellpadding="0">
<tr>
  <td bgcolor="#ff0000">
    <img src="images/bk.gif" width="${time_length}" height="10">
  </td>
</tr>
</table>
_EOT_;
      }
      else
      {
        $page_content .= '<img src="images/bk.gif" width="1" height="1">';
      }
      $page_content .= '</td></tr>';
    }
    $page_content .= "</table>";
  }

////////////////////////////////////////////////////////////////////////
// ユーザーエージェント一覧

  elseif($input_type == "agent")
  {
    $page_content .= <<<_EOT_
<table border="0" cellspacing="1" cellpadding="5" width="100%">
<tr>
  <td bgcolor="#cccccc">
    <nobr>ユーザーエージェント</nobr>
  </td>
  <td bgcolor="#cccccc" width="10%">
    <nobr>カウント</nobr>
  </td>
</tr>
_EOT_;
    $access_query = "select user_agent,count(user_agent)"
                  . " from wps_access group by user_agent"
                  . " order by user_agent desc";
    $access_agent_data = mysql_query($access_query, $db);
    while($access_agent = mysql_fetch_row($access_agent_data))
    {
      $page_content .= '<tr><td bgcolor="#eeeeee">'
                     . $access_agent[0]
                     . '</td>'
                     . '<td align="right" bgcolor="#eeeeee"><nobr>'
                     . $access_agent[1]
                     . '</nobr></td></tr>';
    }
    $page_content .= "</table>";
  }

////////////////////////////////////////////////////////////////////////
// ホスト一覧

  elseif($input_type == "host")
  {
    $page_content .= <<<_EOT_
<table border="0" cellspacing="1" cellpadding="5" width="100%">
<tr>
  <td bgcolor="#cccccc">
    <nobr>ホスト</nobr>
  </td>
  <td bgcolor="#cccccc" width="10%">
    <nobr>カウント</nobr>
  </td>
</tr>
_EOT_;
    $access_query = "select host,count(host) from wps_access"
                  . " group by host order by host asc";
    $access_host_data = mysql_query($access_query);
    while($access_host = mysql_fetch_row($access_host_data))
    {
      $page_content .= '<tr><td bgcolor="#eeeeee">'
                     . $access_host[0]
                     . '</td>'
                     . '<td align="right" bgcolor="#eeeeee"><nobr>'
                     . $access_host[1]
                     . '</nobr></td></tr>';
    }
    $page_content .= "</table>";
  }

////////////////////////////////////////////////////////////////////////
// リンク元一覧

  elseif($input_type == "referer")
  {
    $page_content .= <<<_EOT_
<table border="0" cellspacing="1" cellpadding="5" width="100%">
<tr>
  <td bgcolor="#cccccc">
    <nobr>リンク元</nobr>
  </td>
  <td bgcolor="#cccccc" width="10%">
    <nobr>カウント</nobr>
  </td>
</tr>
_EOT_;
    $access_query = "select referer,count(referer) from wps_access"
                  . " group by referer order by referer desc";
    $access_referer_data = mysql_query($access_query);
    while($access_referer = mysql_fetch_row($access_referer_data))
    {
      $page_content .= '<tr><td bgcolor="#eeeeee">'
                     . $access_referer[0]
                     . '</td>'
                     . '<td align="right" bgcolor="#eeeeee"><nobr>'
                     . $access_referer[1]
                     . '</nobr></td></tr>';
    }
    $page_content .= "</table>";
  }
?>