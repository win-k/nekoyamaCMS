<?php
////////////////////////////////////////////////////////////////////////
// アクセス制限

  if(ereg("function.php", $_SERVER['PHP_SELF']))
  {
    header("Location: index.php");
    exit();
  }

////////////////////////////////////////////////////////////////////////
// エラーメッセージ用関数

  function error_message($msg)
  {
    global $page_content;
    $page_content .= <<<_EOT_
<br>
<p>
<font color="#ff0000">
エラー ： ${msg}
</font>
</p>
<center>
<a href="javascript:history.back();">[ 戻る ]</a>
</center>
_EOT_;
  }
?>