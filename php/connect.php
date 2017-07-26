<?php
/*
  $db_host = "127.0.0.1";
  $db_user = "root";
  $db_pass = "";
  $db_name = "prosperity4all";
*/
    
  $db_host = "127.0.0.1";
  $db_user = "root";
  $db_pass = "";
  $db_name = "prosperity4all";
	
	$db = @mysql_connect($db_host,$db_user,$db_pass) or die("Es konnte keine Verbindung zur Datenbank hergestellt werden! 0x1");
	$db_select = @MYSQL_SELECT_DB($db_name, $db); 
	
	if (!$db_select) {
	   die ('Es konnte keine Verbindung zur Datenbank hergestellt werden! 0x2');
	}

	mysql_query("set names 'utf8'");

?>
