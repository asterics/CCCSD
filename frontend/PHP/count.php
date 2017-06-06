<?php
include 'connection.php';

$index = intval($_GET['id']); 

$query = "UPDATE models set downloads = downloads + 1 where ID = ".$index."";
mysql_query($query);

?>