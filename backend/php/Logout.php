<?php
	session_start();
	unset($_SESSION['ID']);
	unset($_SESSION['admin']);
	unset($_SESSION['username']);
	header("Location:Login.php?action=logout");
?>