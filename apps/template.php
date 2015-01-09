<?php
////////////////////////////////////////////////////////////////////////
// アクセス制限

  if(ereg("template.php", $_SERVER['PHP_SELF']))
  {
    header("Location: index.php");
    exit();
  }

////////////////////////////////////////////////////////////////////////
// テンプレート

print <<<_EOT_
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html;charset=EUC-JP">
${page_myheader}
  <title>${page_title}</title>
${page_stylesheet}
${page_javascript}
</head>
<body bgcolor="#ffffff" text="#333333" link="#483d8b" alink="#ff8c00" vlink="#483d8b">
<center>
<!-- Logo -->
  <br>
  <table border="0" cellspacing="0" cellpadding="0" width="780">
  <form method="POST" action="${page_base}">
    <tr>
      <td>${page_logo}</td>
      <td align="right">
        Search ：<input type="image" src="images/bk.gif" width="1" height="1" border="0">
        <input type="text" name="keyword" size="12" maxlength="255">
        <input type="hidden" name="action" value="search">
      </td>
    </tr>
    <tr>
      <td colspan="2"><img src="images/bk.gif" width="780" height="5"></td>
    </tr>
  </form>
  </table>
<!-- Header -->
  <table border="0" cellspacing="0" cellpadding="0" width="780">
    <tr>
      <td bgcolor="#333333"><img src="images/bk.gif" width="1" height="1"></td>
      <td bgcolor="#333333"><img src="images/bk.gif" width="778" height="1"></td>
      <td bgcolor="#333333"><img src="images/bk.gif" width="1" height="1"></td>
    </tr>
    <tr>
      <td bgcolor="#333333"><img src="images/bk.gif" width="1" height="1"></td>
      <td bgcolor="#4682b4">
        <table border="0" cellspacing="0" cellpadding="2" width="778">
          <tr>
            <td>&nbsp;<font color="#ffffff">${page_access}</font></td>
            <td align="right"><font color="#ffffff">${page_update}</font></td>
          </tr>
        </table>
      </td>
      <td bgcolor="#333333"><img src="images/bk.gif" width="1" height="1"></td>
    </tr>
    <tr>
      <td colspan="3" bgcolor="#333333"><img src="images/bk.gif" width="780" height="1"></td>
    </tr>
    <tr>
      <td bgcolor="#333333"><img src="images/bk.gif" width="1" height="1"></td>
      <td bgcolor="#cccccc" align="center">
        <table border="0" cellspacing="0" cellpadding="2">
          <tr>
            <td><b>${page_header}</b></td>
          </tr>
        </table>
      </td>
      <td bgcolor="#333333"><img src="images/bk.gif" width="1" height="1"></td>
    </tr>
    <tr>
      <td colspan="3" bgcolor="#333333"><img src="images/bk.gif" width="780" height="1"></td>
    </tr>
    <tr>
      <td colspan="3"><img src="images/bk.gif" width="1" height="10"></td>
    </tr>
  </table>
<!-- Menu -->
  <table border="0" cellspacing="0" cellpadding="0" width="780">
    <tr>
      <td bgcolor="#333333"><img src="images/bk.gif" width="1" height="1"></td>
      <td bgcolor="#333333"><img src="images/bk.gif" width="170" height="1"></td>
      <td bgcolor="#333333"><img src="images/bk.gif" width="1" height="1"></td>
      <td><img src="images/bk.gif" width="10" height="1"></td>
      <td bgcolor="#333333"><img src="images/bk.gif" width="1" height="1"></td>
      <td bgcolor="#333333"><img src="images/bk.gif" width="596" height="1"></td>
      <td bgcolor="#333333"><img src="images/bk.gif" width="1" height="1"></td>
    </tr>
    <tr>
      <td bgcolor="#333333"><img src="images/bk.gif" width="1" height="1"></td>
      <td align="center" valign="top">
        <table border="0" cellspacing="0" cellpadding="0" width="150">
          <tr>
            <td><img src="images/bk.gif" width="1" height="10"></td>
          </tr>
        </table>
${page_menu}
      </td>
      <td bgcolor="#333333"><img src="images/bk.gif" width="1" height="1"></td>
      <td><img src="images/bk.gif" width="10" height="1"></td>
      <td bgcolor="#333333"><img src="images/bk.gif" width="1" height="1"></td>
      <td align="center" valign="top">
<!-- Display -->
        <table border="0" cellspacing="0" cellpadding="0" width="576">
          <tr>
            <td>${page_content}</td>
          </tr>
          <tr>
            <td><img src="images/bk.gif" width="1" height="10"></td>
          </tr>
        </table>
      </td>
      <td bgcolor="#333333"><img src="images/bk.gif" width="1" height="1"></td>
    </tr>
    <tr>
      <td bgcolor="#333333"><img src="images/bk.gif" width="1" height="1"></td>
      <td bgcolor="#333333"><img src="images/bk.gif" width="170" height="1"></td>
      <td bgcolor="#333333"><img src="images/bk.gif" width="1" height="1"></td>
      <td><img src="images/bk.gif" width="10" height="1"></td>
      <td bgcolor="#333333"><img src="images/bk.gif" width="1" height="1"></td>
      <td bgcolor="#333333"><img src="images/bk.gif" width="596" height="1"></td>
      <td bgcolor="#333333"><img src="images/bk.gif" width="1" height="1"></td>
    </tr>
  </table>
<!-- Footer -->
  <table border="0" cellspacing="0" cellpadding="0" width="780">
    <tr>
      <td><img src="images/bk.gif" width="1" height="10"></td>
    </tr>
    <tr>
      <td align="center">${page_footer}</td>
    </tr>
  </table>
<br><br>
</center>
</body>
</html>
_EOT_;
?>