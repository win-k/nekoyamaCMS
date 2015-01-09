# MySQL dump 8.22
#
# Host: localhost    Database: wps
#-------------------------------------------------------
# Server version  3.23.55-nt

#
# Table structure for table 'wps_access'
#

CREATE TABLE wps_access (
  id int(11) NOT NULL auto_increment,
  date date NOT NULL default '0000-00-00',
  wday tinyint(1) NOT NULL default '0',
  time time NOT NULL default '00:00:00',
  user_agent varchar(255) default NULL,
  host varchar(255) default NULL,
  remote_addr varchar(255) default NULL,
  client_ip varchar(255) default NULL,
  via varchar(255) default NULL,
  coming_from varchar(255) default NULL,
  forwarded varchar(255) default NULL,
  forwarded_for varchar(255) default NULL,
  x_coming_from varchar(255) default NULL,
  x_forwarded varchar(255) default NULL,
  x_forwarded_for varchar(255) default NULL,
  referer varchar(255) default NULL,
  PRIMARY KEY  (id)
) TYPE=MyISAM COMMENT='アクセスログ';

#
# Dumping data for table 'wps_access'
#



#
# Table structure for table 'wps_admin'
#

CREATE TABLE wps_admin (
  id int(11) NOT NULL auto_increment,
  name varchar(8) binary NOT NULL default '',
  pass varchar(16) binary NOT NULL default '',
  PRIMARY KEY  (id)
) TYPE=MyISAM COMMENT='管理者データ';

#
# Dumping data for table 'wps_admin'
#


INSERT INTO wps_admin VALUES (1,'test','135178557bec100b');

#
# Table structure for table 'wps_bbs'
#

CREATE TABLE wps_bbs (
  id int(11) NOT NULL auto_increment,
  pid int(11) NOT NULL default '0',
  cid int(11) NOT NULL default '0',
  title varchar(255) NOT NULL default '無題',
  name varchar(255) NOT NULL default '匿名',
  homepage varchar(255) default NULL,
  email varchar(255) default NULL,
  date datetime NOT NULL default '0000-00-00 00:00:00',
  content text NOT NULL,
  host varchar(255) default NULL,
  active enum('Y','N') default 'Y',
  PRIMARY KEY  (id)
) TYPE=MyISAM COMMENT='掲示板ログ';

#
# Dumping data for table 'wps_bbs'
#



#
# Table structure for table 'wps_contents'
#

CREATE TABLE wps_contents (
  id int(11) NOT NULL auto_increment,
  name varchar(255) NOT NULL default '',
  topic varchar(255) NOT NULL default '',
  title varchar(255) NOT NULL default '',
  content text NOT NULL,
  date datetime NOT NULL default '0000-00-00 00:00:00',
  active enum('Y','N') default 'Y',
  PRIMARY KEY  (id)
) TYPE=MyISAM COMMENT='フリーコンテンツデータ';

#
# Dumping data for table 'wps_contents'
#



#
# Table structure for table 'wps_general'
#

CREATE TABLE wps_general (
  id int(11) NOT NULL auto_increment,
  cid int(11) NOT NULL default '0',
  name varchar(255) NOT NULL default '',
  comment varchar(255) NOT NULL default '',
  content varchar(255) default NULL,
  date datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (id)
) TYPE=MyISAM COMMENT='初期設定';

#
# Dumping data for table 'wps_general'
#


INSERT INTO wps_general VALUES (1,1,'title','タイトルバーに表示するタイトル','Web Portal System','2003-02-07 11:31:39');
INSERT INTO wps_general VALUES (2,1,'logo_image','ロゴ画像のURL','images/logo.gif','2003-02-07 11:31:44');
INSERT INTO wps_general VALUES (3,1,'logo_alt','ロゴ画像の代替テキスト','Web Portal System','2003-02-07 11:31:48');
INSERT INTO wps_general VALUES (4,1,'access_prepend','アクセスカウンタの前に付加する文字列','','2003-02-07 11:31:53');
INSERT INTO wps_general VALUES (5,1,'access_append','アクセスカウンタの後に付加する文字列',' Hits from 2003-02-07','2003-02-07 11:31:59');
INSERT INTO wps_general VALUES (6,1,'update_prepend','更新日時の前に付加する文字列','Last Modified ：','2003-02-07 11:32:08');
INSERT INTO wps_general VALUES (7,1,'update_append','更新日時の後に付加する文字列','&nbsp;','2003-02-07 11:32:13');
INSERT INTO wps_general VALUES (8,1,'footer','ページフッタに表示する文字列','Copyright &copy; Yusaku Takashima 2003. All rights reserved.','2003-02-07 11:32:22');
INSERT INTO wps_general VALUES (9,2,'wps_home','このシステムを設置したURL','http://localhost:8080/~wps','2003-02-07 11:32:28');
INSERT INTO wps_general VALUES (10,2,'contact_to','管理者のメールアドレス','admin@localhost','2003-02-07 11:32:35');
INSERT INTO wps_general VALUES (11,3,'bbs_show','掲示板の表示件数','10','2003-02-07 11:32:41');
INSERT INTO wps_general VALUES (12,3,'search_show','検索結果の表示件数','10','2003-02-07 11:32:46');
INSERT INTO wps_general VALUES (13,3,'link_show','リンク集の表示件数','10','2003-02-07 11:32:51');

#
# Table structure for table 'wps_general_category'
#

CREATE TABLE wps_general_category (
  id int(11) NOT NULL auto_increment,
  category varchar(255) default NULL,
  PRIMARY KEY  (id)
) TYPE=MyISAM COMMENT='設定カテゴリ';

#
# Dumping data for table 'wps_general_category'
#


INSERT INTO wps_general_category VALUES (1,'ページ設定');
INSERT INTO wps_general_category VALUES (2,'アドレス設定');
INSERT INTO wps_general_category VALUES (3,'表示設定');

#
# Table structure for table 'wps_link'
#

CREATE TABLE wps_link (
  id int(11) NOT NULL auto_increment,
  cid int(11) NOT NULL default '0',
  title varchar(255) NOT NULL default '',
  url varchar(255) NOT NULL default '',
  date datetime NOT NULL default '0000-00-00 00:00:00',
  content text NOT NULL,
  host varchar(255) default NULL,
  username varchar(64) default NULL,
  count int(11) NOT NULL default '0',
  active enum('Y','N') default 'Y',
  PRIMARY KEY  (id)
) TYPE=MyISAM COMMENT='リンク集データ';

#
# Dumping data for table 'wps_link'
#



#
# Table structure for table 'wps_link_category'
#

CREATE TABLE wps_link_category (
  id int(11) NOT NULL auto_increment,
  category varchar(255) NOT NULL default '',
  weight int(11) NOT NULL default '0',
  count int(11) NOT NULL default '0',
  active enum('Y','N') NOT NULL default 'Y',
  PRIMARY KEY  (id)
) TYPE=MyISAM COMMENT='リンク集カテゴリ';

#
# Dumping data for table 'wps_link_category'
#


INSERT INTO wps_link_category VALUES (1,'コンピュータ・インターネット',1,0,'Y');
INSERT INTO wps_link_category VALUES (2,'エンターテイメント',2,0,'Y');
INSERT INTO wps_link_category VALUES (3,'趣味・スポーツ',3,0,'Y');
INSERT INTO wps_link_category VALUES (4,'旅行・地域情報',4,0,'Y');
INSERT INTO wps_link_category VALUES (5,'CG・ゲーム',5,0,'Y');
INSERT INTO wps_link_category VALUES (6,'映画・音楽',6,0,'Y');
INSERT INTO wps_link_category VALUES (7,'美容・健康',7,0,'Y');
INSERT INTO wps_link_category VALUES (8,'政治・経済',8,0,'Y');
INSERT INTO wps_link_category VALUES (9,'社会・仕事',9,0,'Y');
INSERT INTO wps_link_category VALUES (10,'技術・教育',10,0,'Y');
INSERT INTO wps_link_category VALUES (11,'ショッピング',11,0,'Y');
INSERT INTO wps_link_category VALUES (12,'ノンセクション',12,0,'Y');

#
# Table structure for table 'wps_menu'
#

CREATE TABLE wps_menu (
  id int(11) NOT NULL auto_increment,
  internal_name varchar(255) NOT NULL default '',
  display_name varchar(255) NOT NULL default '',
  weight int(11) NOT NULL default '0',
  active enum('Y','N') default 'Y',
  PRIMARY KEY  (id)
) TYPE=MyISAM COMMENT='メニュー設定';

#
# Dumping data for table 'wps_menu'
#


INSERT INTO wps_menu VALUES (1,'menu_contents','メインメニュー',1,'Y');
INSERT INTO wps_menu VALUES (2,'menu_functions','サブメニュー',2,'Y');
INSERT INTO wps_menu VALUES (3,'menu_poll','アンケート',3,'Y');
INSERT INTO wps_menu VALUES (4,'menu_banner','リンクバナー',4,'Y');
INSERT INTO wps_menu VALUES (5,'menu_user','マイメニュー',5,'N');

#
# Table structure for table 'wps_menu_banner'
#

CREATE TABLE wps_menu_banner (
  id int(11) NOT NULL auto_increment,
  name varchar(255) default '',
  item varchar(255) NOT NULL default '',
  active enum('Y','N') NOT NULL default 'Y',
  weight int(11) NOT NULL default '0',
  header enum('Y','N') NOT NULL default 'N',
  PRIMARY KEY  (id)
) TYPE=MyISAM COMMENT='メニュー４';

#
# Dumping data for table 'wps_menu_banner'
#


INSERT INTO wps_menu_banner VALUES (1,'-','<img src=\"images/bk.gif\" width=\"1\" height=\"8\"><br>','Y',1,'N');
INSERT INTO wps_menu_banner VALUES (2,'PHP','<center><a href=\"http://www.php.net/\" target=\"_blank\"><img src=\"images/php-small-black.gif\" width=\"88\" height=\"31\" border=\"0\"></a><br></center>','Y',2,'N');
INSERT INTO wps_menu_banner VALUES (3,'-','<img src=\"images/bk.gif\" width=\"1\" height=\"8\"><br>','Y',3,'N');
INSERT INTO wps_menu_banner VALUES (4,'MySQL','<center><a href=\"http://www.mysql.com\" target=\"_blank\"><img src=\"images/poweredbymysql-88.png\" width=\"88\" height=\"31\" border=\"0\"></a><br></center>','Y',4,'N');
INSERT INTO wps_menu_banner VALUES (5,'-','<img src=\"images/bk.gif\" width=\"1\" height=\"8\"><br>','Y',5,'N');
INSERT INTO wps_menu_banner VALUES (6,'Apache','<center><a href=\"http://httpd.apache.org\" target=\"_blank\"><img src=\"images/asf_logo.gif\" width=\"130\" height=\"33\" border=\"0\"></a><br></center>','Y',6,'N');
INSERT INTO wps_menu_banner VALUES (7,'-','<img src=\"images/bk.gif\" width=\"1\" height=\"8\"><br>','Y',7,'N');

#
# Table structure for table 'wps_menu_contents'
#

CREATE TABLE wps_menu_contents (
  id int(11) NOT NULL auto_increment,
  cid int(11) NOT NULL default '0',
  name varchar(255) default '',
  item varchar(255) NOT NULL default '',
  active enum('Y','N') NOT NULL default 'Y',
  weight int(11) NOT NULL default '0',
  header enum('Y','N') NOT NULL default 'N',
  PRIMARY KEY  (id)
) TYPE=MyISAM COMMENT='メニュー１';

#
# Dumping data for table 'wps_menu_contents'
#


INSERT INTO wps_menu_contents VALUES (1,0,'Home','&nbsp;<a href=\"_page_base_?action=top\">_item_content_</a><br>','Y',1,'Y');

#
# Table structure for table 'wps_menu_functions'
#

CREATE TABLE wps_menu_functions (
  id int(11) NOT NULL auto_increment,
  module varchar(255) NOT NULL default '',
  name varchar(255) default '',
  item varchar(255) NOT NULL default '',
  active enum('Y','N') NOT NULL default 'Y',
  weight int(11) NOT NULL default '0',
  header enum('Y','N') NOT NULL default 'N',
  PRIMARY KEY  (id)
) TYPE=MyISAM COMMENT='メニュー２';

#
# Dumping data for table 'wps_menu_functions'
#


INSERT INTO wps_menu_functions VALUES (1,'access','アクセス解析','&nbsp;<a href=\"_page_base_?action=access\">_item_content_</a><br>','Y',1,'Y');
INSERT INTO wps_menu_functions VALUES (2,'poll','アンケート','&nbsp;<a href=\"_page_base_?action=poll\">_item_content_</a><br>','Y',2,'Y');
INSERT INTO wps_menu_functions VALUES (3,'bbs','掲示板','&nbsp;<a href=\"_page_base_?action=bbs\">_item_content_</a><br>','Y',3,'Y');
INSERT INTO wps_menu_functions VALUES (4,'search','サイト検索','&nbsp;<a href=\"_page_base_?action=search\">_item_content_</a><br>','Y',4,'Y');
INSERT INTO wps_menu_functions VALUES (5,'contact','メールフォーム','&nbsp;<a href=\"_page_base_?action=contact\">_item_content_</a><br>','Y',5,'Y');
INSERT INTO wps_menu_functions VALUES (6,'link','リンク集','&nbsp;<a href=\"_page_base_?action=link\">_item_content_</a><br>','Y',6,'Y');

#
# Table structure for table 'wps_menu_poll'
#

CREATE TABLE wps_menu_poll (
  id int(11) NOT NULL auto_increment,
  iid int(11) NOT NULL default '0',
  name varchar(255) default '',
  item varchar(255) NOT NULL default '',
  active enum('Y','N') NOT NULL default 'Y',
  weight int(11) NOT NULL default '0',
  header enum('Y','N') NOT NULL default 'N',
  PRIMARY KEY  (id)
) TYPE=MyISAM COMMENT='メニュー３';

#
# Dumping data for table 'wps_menu_poll'
#


INSERT INTO wps_menu_poll VALUES (1,0,'','<form method=\"POST\" action=\"_page_base_\">','Y',1,'N');
INSERT INTO wps_menu_poll VALUES (2,0,'タイトル','&nbsp;このサイトはいかが？<br><br>','Y',2,'N');
INSERT INTO wps_menu_poll VALUES (3,1,'なかなか','&nbsp;<input type=\"radio\" name=\"poll\" value=\"1\"> _item_content_<br>','Y',3,'N');
INSERT INTO wps_menu_poll VALUES (4,2,'まあまあ','&nbsp;<input type=\"radio\" name=\"poll\" value=\"2\"> _item_content_<br>','Y',4,'N');
INSERT INTO wps_menu_poll VALUES (5,3,'そこそこ','&nbsp;<input type=\"radio\" name=\"poll\" value=\"3\"> _item_content_<br>','Y',5,'N');
INSERT INTO wps_menu_poll VALUES (6,4,'いまいち','&nbsp;<input type=\"radio\" name=\"poll\" value=\"4\"> _item_content_<br>','Y',6,'N');
INSERT INTO wps_menu_poll VALUES (7,5,'いやはや','&nbsp;<input type=\"radio\" name=\"poll\" value=\"5\"> _item_content_<br>','Y',7,'N');
INSERT INTO wps_menu_poll VALUES (8,0,'','<input type=\"hidden\" name=\"action\" value=\"poll\"> <input type=\"hidden\" name=\"type\" value=\"vote\">','Y',8,'N');
INSERT INTO wps_menu_poll VALUES (9,0,'','<br><center><input type=\"submit\" value=\"投票する\"></center>','Y',9,'N');
INSERT INTO wps_menu_poll VALUES (10,0,'','</form>','Y',10,'N');

#
# Table structure for table 'wps_menu_user'
#

CREATE TABLE wps_menu_user (
  id int(11) NOT NULL auto_increment,
  name varchar(255) default NULL,
  item varchar(255) NOT NULL default '',
  active enum('Y','N') NOT NULL default 'N',
  weight int(11) NOT NULL default '0',
  header enum('Y','N') NOT NULL default 'N',
  PRIMARY KEY  (id)
) TYPE=MyISAM COMMENT='メニュー5';

#
# Dumping data for table 'wps_menu_user'
#



#
# Table structure for table 'wps_poll'
#

CREATE TABLE wps_poll (
  id int(11) NOT NULL auto_increment,
  vote tinyint(4) NOT NULL default '0',
  date datetime NOT NULL default '0000-00-00 00:00:00',
  host varchar(255) default NULL,
  PRIMARY KEY  (id)
) TYPE=MyISAM COMMENT='アンケート投票データ';

#
# Dumping data for table 'wps_poll'
#



#
# Table structure for table 'wps_top'
#

CREATE TABLE wps_top (
  id int(11) NOT NULL auto_increment,
  title varchar(255) NOT NULL default '',
  content text NOT NULL,
  date datetime NOT NULL default '0000-00-00 00:00:00',
  active enum('Y','N') default 'Y',
  PRIMARY KEY  (id)
) TYPE=MyISAM COMMENT='トップページデータ';

#
# Dumping data for table 'wps_top'
#


