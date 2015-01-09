<?php
////////////////////////////////////////////////////////////////////////
// アクセス制限

  if(ereg("admin/database.php", $_SERVER['PHP_SELF']))
  {
    header("Location: ../admin.php");
    exit();
  }

////////////////////////////////////////////////////////////////////////
// データベースの最適化

  $page_content .= <<<_EOT_
<table border="0" cellspacing="0" cellpadding="2" width="100%">
<tr>
  <td bgcolor="#cccccc">▼データベースの管理</td>
</tr>
</table>
<br>
<p>
  &nbsp;
  <a href="admin.php?mode=admin&amp;action=database&amp;type=optimize">
    ●データベースを最適化する
  </a><br>
  &nbsp;
  <a href="admin.php?mode=admin&amp;action=database&amp;type=backup">
    ●データベースをバックアップする
  </a>
</p>
_EOT_;

  if($input_type == "optimize")
  {
    if(ereg($PHP_SELF, $HTTP_REFERER))
    {
      $page_content .= <<<_EOT_
<center>
<table border="1" cellspacing="1" cellpadding="2" width="100%">
<tr>
  <td align="center">
    テーブル名
  </td>
  <td align="center">
    サイズ
  </td>
  <td align="center">
    最適化
  </td>
  <td align="center">
    ステータス
  </td>
</tr>
_EOT_;

      $admin_table_data = mysql_query("show table status from ${db_name}", $db);
      while($admin_table = mysql_fetch_assoc($admin_table_data))
      {
        // テーブル情報の収集
        $admin_table_total = $admin_table['Data_length']
                             + $admin_table['Index_length'];
        $admin_table_total = round($admin_table_total / 1024, 0);
        $admin_table_gain = $admin_table['Data_free'];
        $admin_table_gain = round($admin_table_gain / 1024, 0);

        // 最適化実行
        $admin_query = "optimize table ${admin_table['Name']}";
        mysql_query($admin_query, $db);

        // 結果表示
        $page_content .= '<tr><td>'
                       . $admin_table['Name']
                       . '</td>'
                       . '<td align="right">'
                       . $admin_table_total
                       . ' kb</td>'
                       . '<td align="right">'
                       . $admin_table_gain
                       . ' kb</td>';
        if($admin_table_gain == 0)
        {
          $page_content .= '<td>'
                         . '最新の状態です'
                         . '</td>';
        }
        else
        {
          $page_content .= '<td>'
                         . '<font color="#ff0000">'
                         . '最適化しました'
                         . '</font></td>';
        }
      }
      $page_content .= <<<_EOT_
</tr>
</table>
</center>
<br>
<br>
_EOT_;
    }
  }

////////////////////////////////////////////////////////////////////////
// データベースのバックアップ

  elseif($input_type == "backup")
  {
    if(ereg($PHP_SELF, $HTTP_REFERER))
    {
      // ファイル名の生成
      $admin_backfile = "${db_name}_" . date("Y-m-d") . ".sql";

      // コメントの生成
      $admin_date = date("Y-m-d h:i:s");
      $admin_server_version = mysql_get_server_info($db);
      $admin_php_version = phpversion();

      $admin_backup = <<<_EOT_
#
# Web Portal System
#
# 文字エンコーディング : EUC-JP (改行コード=LF)
# 作成日時             : ${admin_date}
# ホスト名             : ${db_host}
# データベース名       : ${db_name}
# サーバーのバージョン : ${admin_server_version}
# PHPのバージョン      : ${admin_php_version}
#
_EOT_;
      $admin_backup .= "\n\n";

      // テーブルの構造のダンプ
      $admin_table_data = mysql_query("show tables from ${db_name}", $db);
      while($admin_table = mysql_fetch_row($admin_table_data))
      {
        $admin_backup .= <<<_EOT_
#
# テーブルの構造 '${admin_table[0]}'
#
_EOT_;
        $admin_backup .= "\n\n";

        $admin_query = "show create table ${admin_table[0]}";
        mysql_query("set SQL_QUOTE_SHOW_CREATE=0", $db);
        $admin_struct_data = mysql_query($admin_query);
        $admin_struct = mysql_fetch_row($admin_struct_data);
        $admin_backup .= $admin_struct[1] . ";\n\n";

        // テーブルのデータのダンプ
        $admin_backup .= <<<_EOT_
#
# テーブルのデータ '${admin_table[0]}'
#
_EOT_;
        $admin_backup .= "\n\n";

        $admin_query = "select * from ${admin_table[0]}";
        $admin_dump_data = mysql_query($admin_query, $db);

        $admin_type = array();
        for($i = 0; $i < mysql_num_fields($admin_dump_data); $i++)
        {
          $field_name = mysql_field_name($admin_dump_data, $i);
          $field_type = mysql_field_type($admin_dump_data, $i);
          $admin_type[$field_name] = $field_type;
        }
        while($admin_dump = mysql_fetch_assoc($admin_dump_data))
        {
          $admin_backup .= "INSERT INTO ${admin_table[0]} VALUES(";
          foreach($admin_type as $col_name => $col_type)
          {
            if($admin_dump[$col_name] == '0' || $admin_dump[$col_name] != '')
            {
              if($col_type == 'tinyint' || $col_type == 'smallint' ||
                 $col_type == 'mediumint' || $col_type == 'int' ||
                 $col_type == 'bigint' || $col_type == 'timestamp')
              {
                $admin_backup .= $admin_dump[$col_name] . ",";
              }
              else
              {
                $admin_backup .= "'"
                               . addslashes($admin_dump[$col_name])
                               . "',";
              }
            }
            else
            {
              $admin_backup .= "'',";
            }
          }
          $admin_backup = rtrim($admin_backup, ",");
          $admin_backup .= ");\n";
        }
        $admin_backup .= "\n";
      }

      // 改行の処理
      $admin_backup = str_replace("\r\n", "\n", $admin_backup);
      $admin_backup = str_replace("\r", "\n", $admin_backup);

      // ヘッダの出力
      header("Content-type: application/octet-stream");
      header("Content-disposition: attachment; filename=${admin_backfile}");

      // テキスト出力
      print $admin_backup;
      exit();
    }
  }
?>