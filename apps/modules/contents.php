<?php
////////////////////////////////////////////////////////////////////////
// アクセス制限

  if(ereg("modules/contents.php", $_SERVER['PHP_SELF']))
  {
    header("Location: ../index.php");
    exit();
  }

////////////////////////////////////////////////////////////////////////
// コンテンツの取得

  $contents_query = "select * from wps_contents"
                  . " where id=${input_id} and active='Y'";
  $contents_item_data = mysql_query($contents_query, $db);
  $contents_item = mysql_fetch_assoc($contents_item_data);

////////////////////////////////////////////////////////////////////////
// 表示

  if(is_array($contents_item))
  {
    // タイトル
    $contents_topic_size = getimagesize ("images/${contents_item['topic']}");
    $page_content .= <<<_EOT_
<table border="0" cellspacing="0" cellpadding="0">
<tr>
  <td><img src="images/bk.gif" width="1" height="15"></td>
</tr>
<tr>
  <td><img src="images/${contents_item['topic']}" ${contents_topic_size[3]}></td>
</tr>
<tr>
  <td><img src="images/bk.gif" width="1" height="15"></td>
</tr>
</table>
_EOT_;

    // 内容
    $page_content .= "${contents_item['title']}"
                   . " [ ${contents_item['date']} ]<br><br>"
                   . $contents_item['content'];

    $page_content = str_replace("_page_base_",
                                $page_base,
                                $page_content);
  }
?>