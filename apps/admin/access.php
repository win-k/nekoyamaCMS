<?php
////////////////////////////////////////////////////////////////////////
// アクセス制限

  if(ereg("admin/access.php", $_SERVER['PHP_SELF']))
  {
    header("Location: ../admin.php");
    exit();
  }

////////////////////////////////////////////////////////////////////////
// CSVで保存

  if($input_type == "csv")
  {
    if(ereg($PHP_SELF, $HTTP_REFERER))
    {
      $admin_wday = array("dammy", "日", "月", "火", "水", "木", "金", "土");
      $admin_type = array();
      $admin_log = array();
      $admin_csv_file = "wps_access_" . date("Y-m-d") . ".csv";

      $admin_query = "select * from wps_access order by id asc";
      $admin_dump_data = mysql_query($admin_query, $db);

      for($i = 0; $i < mysql_num_fields($admin_dump_data); $i++)
      {
        $field_name = mysql_field_name($admin_dump_data, $i);
        $field_type = mysql_field_type($admin_dump_data, $i);
        $admin_type[$field_name] = $field_type;
        array_push($admin_log, '"' . strtoupper($field_name) . '"');
      }
      array_push($admin_log, "\r\n");

      while($admin_dump = mysql_fetch_assoc($admin_dump_data))
      {
        foreach($admin_type as $col_name => $col_type)
        {
          if($admin_dump[$col_name] != '')
          {
            if($col_name == "id")
            {
              array_push($admin_log, $admin_dump[$col_name]);
            }
            else
            {
              if($col_name == "wday")
              {
                $admin_dump[$col_name] = $admin_wday[$admin_dump[$col_name]];
              }
              array_push($admin_log,
                         '"' . addslashes($admin_dump[$col_name]) . '"');
            }
          }
          else
          {
            array_push($admin_log, '""');
          }
        }
        array_push($admin_log, "\r\n");
      }

      $admin_csv = implode(",", $admin_log);
      $admin_csv = preg_replace("/,\r\n,?/", "\r\n", $admin_csv);

      header("Content-type: application/octet-stream");
      header("Content-disposition: attachment; filename=${admin_csv_file}");

      print mb_convert_encoding($admin_csv, "SJIS", "EUC-JP");
      exit();
    }
  }

////////////////////////////////////////////////////////////////////////
// HTMLで保存

  elseif($input_type == "html")
  {
    if(ereg($PHP_SELF, $HTTP_REFERER))
    {
      $admin_date = date("Y-m-d");
      $admin_wday = array("dammy", "日", "月", "火", "水", "木", "金", "土");
      $admin_type = array();
      $admin_html_file = "wps_access_${admin_date}.html";

      $admin_html = <<<_EOT_
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html;charset=EUC-JP">
  <meta name="Create Date" content="${admin_date}">
  <meta name="Datebase Name" content="${db_name}">
  <meta name="Table Name" content="wps_access">
  <title>アクセスログ</title>
  <style type="text/css">
  <!--
    * { font-family:Verdana; font-size:10pt; }
  -->
  </style>
</head>
<body bgcolor="#ffffff">
  <table border="1" cellspacing="1" cellpadding="2">
_EOT_;
      $admin_html .= "\n";

      $admin_query = "select * from wps_access order by id asc";
      $admin_dump_data = mysql_query($admin_query);

      $admin_html .= "    <tr>\n";
      for($i = 0; $i < mysql_num_fields($admin_dump_data); $i++)
      {
        $field_name = mysql_field_name($admin_dump_data, $i);
        $field_type = mysql_field_type($admin_dump_data, $i);
        $admin_type[$field_name] = $field_type;
        $admin_html .= "      <td>" . strtoupper($field_name) . "</td>\n";
      }
      $admin_html .= "    </tr>\n";

      while($admin_dump = mysql_fetch_assoc($admin_dump_data))
      {
        $admin_html .= "    <tr>\n";
        foreach($admin_type as $col_name => $col_type)
        {
          if($admin_dump[$col_name] != '')
          {
            if($col_name == "wday")
            {
              $admin_dump[$col_name] = $admin_wday[$admin_dump[$col_name]];
            }
            $admin_html .= "      <td><nobr>" . $admin_dump[$col_name] . "</nobr></td>\n";
          }
          else
          {
            $admin_html .= "      <td>&nbsp;</td>\n";
          }
        }
        $admin_html .= "    </tr>\n";
      }
      $admin_html .= <<<_EOT_
  </table>
</body>
</html>
_EOT_;

      header("Content-type: application/octet-stream");
      header("Content-disposition: attachment; filename=${admin_html_file}");

      print $admin_html;
      exit();
    }
  }

////////////////////////////////////////////////////////////////////////
// XMLで保存

  elseif($input_type == "xml")
  {
    if(ereg($PHP_SELF, $HTTP_REFERER))
    {
      $admin_date = date("Y-m-d");
      $admin_wday = array("dammy", "日", "月", "火", "水", "木", "金", "土");
      $admin_type = array();
      $admin_xml_file = "wps_access_${admin_date}.xml";

      $admin_xml = <<<_EOT_
<?xml version="1.0" encoding="UTF-8"?>
<access date="${admin_date}" database="${db_name}" table="wps_access">
_EOT_;
      $admin_xml .= "\n";

      $admin_query = "select * from wps_access order by id asc";
      $admin_dump_data = mysql_query($admin_query, $db);

      $admin_xml .= "  <header>\n    <row>\n";
      for($i = 0; $i < mysql_num_fields($admin_dump_data); $i++)
      {
        $field_name = mysql_field_name($admin_dump_data, $i);
        $field_type = mysql_field_type($admin_dump_data, $i);
        $admin_type[$field_name] = $field_type;
        $admin_xml .= "      <col>" . strtoupper($field_name) . "</col>\n";
      }
      $admin_xml .= "    </row>\n  </header>\n  <data>\n";
      while($admin_dump = mysql_fetch_assoc($admin_dump_data))
      {
        $admin_xml .= "    <row>\n";
        foreach($admin_type as $col_name => $col_type)
        {
          if($admin_dump[$col_name] != '')
          {
            if($col_name == "wday")
            {
              $admin_dump[$col_name] = $admin_wday[$admin_dump[$col_name]];
            }
            $admin_xml .= "      <col>" . $admin_dump[$col_name] . "</col>\n";
          }
          else
          {
            $admin_xml .= "      <col/>\n";
          }
        }
        $admin_xml .= "    </row>\n";
      }
      $admin_xml .= "  </data>\n</access>\n";

      header("Content-type: application/octet-stream");
      header("Content-disposition: attachment; filename=${admin_xml_file}");

      print mb_convert_encoding($admin_xml, "UTF-8", "EUC-JP");
      exit();
    }
  }

////////////////////////////////////////////////////////////////////////
// 削除

  elseif($input_type == "delete")
  {
    if(ereg($PHP_SELF, $HTTP_REFERER))
    {
      $admin_query = "delete from wps_access";
      mysql_query($admin_query, $db);
    }
  }

////////////////////////////////////////////////////////////////////////
// 削除の確認

  elseif($input_type == "confirm")
  {
    if(ereg($PHP_SELF, $HTTP_REFERER))
    {
      $page_content .= <<<_EOT_
<center>
<font color="#ff0000">
本当に削除してもよろしいですか？
</font>
&nbsp;&nbsp;[&nbsp;
<a href="admin.php?mode=admin&amp;action=access&amp;type=delete">
はい
</a>
&nbsp;/&nbsp;
<a href="admin.php?mode=admin&amp;action=access">
いいえ
</a>
&nbsp;]
</center>
<br>
_EOT_;
    }
  }

////////////////////////////////////////////////////////////////////////
// リンクの表示

  $page_content .= <<<_EOT_
<table border="0" cellspacing="0" cellpadding="2" width="100%">
<tr>
  <td bgcolor="#cccccc">▼アクセスログの保存</td>
</tr>
</table>
<br>
<p>
  &nbsp;
  <a href="admin.php?mode=admin&amp;action=access&amp;type=csv">
    ●CSV形式で保存する
  </a><br>
  &nbsp;
  <a href="admin.php?mode=admin&amp;action=access&amp;type=html">
    ●HTML形式で保存する
  </a><br>
  &nbsp;
  <a href="admin.php?mode=admin&amp;action=access&amp;type=xml">
    ●XML形式で保存する
  </a><br>
</p>
<table border="0" cellspacing="0" cellpadding="2" width="100%">
<tr>
  <td bgcolor="#cccccc">▼アクセスログの編集</td>
</tr>
</table>
<p>
  &nbsp;
  <a href="admin.php?mode=admin&amp;action=access&amp;type=confirm">
    ●アクセスログを削除する
  </a>
</p>
_EOT_;
?>