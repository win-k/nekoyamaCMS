﻿<?php
////////////////////////////////////////////////////////////////////////
// アクセス制限

  if(ereg("admin/logout.php", $_SERVER['PHP_SELF']))
  {
    header("Location: ../admin.php");
    exit();
  }

////////////////////////////////////////////////////////////////////////
// セッションの破棄

  unset($_SESSION['name']);
  unset($_SESSION['pass']);
  session_destroy();

////////////////////////////////////////////////////////////////////////
// トップページに移動

  header("Location: index.php");
?>