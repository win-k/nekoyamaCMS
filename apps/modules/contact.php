<?php
////////////////////////////////////////////////////////////////////////
// アクセス制限

  if(ereg("modules/contact.php", $_SERVER['PHP_SELF']))
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
  <td><img src="images/contact.gif" width="73" height="12"></td>
</tr>
<tr>
  <td><img src="images/bk.gif" width="1" height="15"></td>
</tr>
</table>
_EOT_;

////////////////////////////////////////////////////////////////////////
// フォームの表示

  if(!isset($input_type))
  {
    $page_content .= <<<_EOT_
<center>
<table border="0" cellspacing="2" cellpadding="0">
<form method="POST" action="${page_base}">
<tr>
  <td>タイトル</td>
</tr>
<tr>
  <td><input type="text" name="subject" size="70" maxlength="70"></td>
</tr>
<tr>
  <td>メールアドレス</td>
</tr>
<tr>
  <td><input type="text" name="from" size="70" maxlength="70"></td>
</tr>
<tr>
  <td>メッセージ</td>
</tr>
<tr>
  <td>
    <textarea name="message" cols="70" rows="12" wrap="hard"></textarea>
  </td>
</tr>
<tr>
  <td colspan="2" align="center">
    <br>
    <input type="submit" value="送信する">
    <input type="reset" value="書き直す">
    <input type="hidden" name="action" value="contact">
    <input type="hidden" name="type" value="send">
  </td>
</tr>
</form>
</table>
</center>
_EOT_;
  }

////////////////////////////////////////////////////////////////////////
// メールを送信

  elseif($input_type == "send" &&
         strtoupper($REQUEST_METHOD) == "POST")
  {
    $mail_flag = 1;
    if($input_subject == "")
    {
      $mail_flag = 0;
      error_message("タイトルが入力されていません。");
    }
    elseif($input_from == "")
    {
      $mail_flag = 0;
      error_message("メールアドレスが入力されていません。");
    }
    elseif(!preg_match("/[\w\d\-\.]+\@[\w\d\-\.]+/i", $input_from))
    {
      $mail_flag = 0;
      error_message("メールアドレスの形式が不正です。");
    }
    elseif($input_message == "")
    {
      $mail_flag = 0;
      error_message("メッセージが入力されていません。");
    }

    if($mail_flag == 1)
    {
      $mail_body = "\n"
                 . "From : ${input_from}\n"
                 . "Date : " . date("Y/m/d h:i:s") . "\n"
                 . "Host : " . $HTTP_HOST . "\n\n"
                 . $input_message;
      mb_language("ja");
      $mail_result = mb_send_mail($page_contact_to,
                                   $input_subject,
                                   $mail_body
                                  );
      if($mail_result)
      {
        $page_content .= "メールを送信しました。";
      }
      else
      {
        error_message("メールを送信できませんでした。");
      }
    }
  }
?>