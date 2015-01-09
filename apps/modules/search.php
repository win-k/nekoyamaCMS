<?php
////////////////////////////////////////////////////////////////////////
// アクセス制限

  if(ereg("modules/search.php", $_SERVER['PHP_SELF']))
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
  <td><img src="images/search.gif" width="66" height="12"></td>
</tr>
<tr>
  <td><img src="images/bk.gif" width="1" height="15"></td>
</tr>
</table>
_EOT_;

////////////////////////////////////////////////////////////////////////
// 検索フォーム表示

  if(!$input_keyword)
  {
    $page_content .= <<<_EOT_
<table border="0" cellspacing="2" cellpadding="0">
<form method="POST" action="${page_base}">
<tr>
  <td colspan="2">
    ■キーワード
  </td>
</tr>
<tr>
  <td>
    <input type="text" name="keyword" size="40" maxlength="255">
  </td>
  <td>
    &nbsp;<input type="submit" value="検索">
    <input type="hidden" name="action" value="search">
  </td>
</tr>
<tr>
  <td colspan="2">
    <br>■検索方法
  </td>
</tr>
<tr>
  <td colspan="2">
    <select name="method">
      <option value="and">すべての語句を含む
      <option value="or">いずれかの語句を含む
      <option value="exact">フレーズを含む
    </select>
  </td>
</tr>
<tr>
  <td colspan="2">
    <br>■検索対象
  </td>
</tr>
<tr>
  <td colspan="2">
    <input type="radio" name="category" value="contents" checked>メインコンテンツ<br>
    <input type="radio" name="category" value="bbs">掲示板<br>
    <input type="radio" name="category" value="link">リンク集<br>
  </td>
</tr>
</form>
</table>
_EOT_;
  }

////////////////////////////////////////////////////////////////////////
// 検索実行

  else
  {
    // キーワードとオプションの処理
    if($input_method == "")
    {
      $input_method = "or";
    }
    if($input_category == "")
    {
      $input_category = "contents";
    }
    $input_keyword = preg_replace("/　/", " ", $input_keyword);

    // 検索用SQLクエリの生成
    $search_query = "select * from wps_${input_category}"
                  . " where active = 'Y' and";
    if($input_method != "exact")
    {
      $search_keywords = explode(" ", $input_keyword);
      $keywords_length = count($search_keywords);
      $search_query .= " ((";
      for($i = 0; $i < $keywords_length; $i++)
      {
        $search_query .= "title like '%${search_keywords[$i]}%'";
        if($i != $keywords_length - 1)
        {
          $search_query .= " ${input_method} ";
        }
      }
      $search_query .= ") or (";
      for($i = 0; $i < $keywords_length; $i++)
      {
        $search_query .= "content like '%${search_keywords[$i]}%'";
        if($i != $keywords_length - 1)
        {
          $search_query .= " ${input_method} ";
        }
      }
      $search_query .= "))";
    }
    else
    {
      $search_query .= " title like '%${input_keyword}%'"
                     . " or content like '%${input_keyword}%'";
    }
    $search_query .= " order by id desc";

    // 検索結果の表示
    $search_flag = 0;
    if(!isset($input_page))
    {
      $input_page = 1;
    }
    $search_start = $input_page * $page_search_show - $page_search_show + 1;
    $search_end = $search_start + $page_search_show - 1;
    $search_count = 1;

    $page_content .= <<<_EOT_
<table border="0" cellspacing="2" cellpadding="0" width="100%">
<tr>
  <td>キーワード [ $input_keyword ] の検索結果</td>
</tr>
<tr>
  <td bgcolor="#333333"><img src="images/bk.gif" width="1" height="1"></td>
</tr>
</table>
<br>
<center>
_EOT_;

    $search_result_data = mysql_query($search_query, $db);
    while($search_result = mysql_fetch_assoc($search_result_data))
    {
      $search_flag = 1;
      if($search_count >= $search_start && $search_count <= $search_end)
      {
        $search_content = strip_tags($search_result['content']);
        $search_content = mb_strcut($search_content, 0, 255, "EUC-JP");
        $page_content .= <<<_EOT_
<table border="0" cellspacing="0" cellpadding="5" width="95%">
<tr>
  <td bgcolor="#cccccc">
    ${search_result['title']}
  </td>
  <td align="right" bgcolor="#cccccc">
    <a href="${page_base}?action=${input_category}&amp;type=search&amp;id=${search_result['id']}">
      [ 詳細 ]
    </a>
  </td>
</tr>
<tr>
  <td colspan="2" bgcolor="#eeeeee">
    ${search_content} ...
  </td>
</tr>
</table>
<br>
_EOT_;
      }
      $search_count++;
    }

    // 次のページへのリンク
    if($search_flag == 1)
    {
      $search_rows = mysql_num_rows($search_result_data);
      $search_length = floor($search_rows / $page_search_show);
      if($search_rows % $page_search_show != 0)
      {
        $search_length++;
      }

      $page_content .= <<<_EOT_
</center>
<table border="0" cellspacing="2" cellpadding="0" width="100%">
<tr>
  <td bgcolor="#333333"><img src="images/bk.gif" width="1" height="1"></td>
</tr>
<tr>
  <td>
    次の検索結果&nbsp;-&nbsp;
_EOT_;

      for($i = 1; $i <= $search_length; $i++)
      {
        $page_content .= '<a href="' . $page_base . '?action=search'
                       . '&amp;keyword=' . urlencode($input_keyword)
                       . '&amp;method=' . $input_method
                       . '&amp;category=' . $input_category
                       . '&amp;page=' . $i . '">' . $i . '</a>&nbsp;';
      }

      $page_content .= <<<_EOT_
  </td>
</tr>
</table>
<br>
_EOT_;
    }

    // キーワードがマッチしなかったときのメッセージ
    else
    {
      $page_content .= <<<_EOT_
<p>キーワード [ ${input_keyword} ] を含むコンテンツは見つかりませんでした。</p>
<center>
<a href="javascript:history.back();">[ 戻る ]</a>
</center>
_EOT_;
    }
  }
?>