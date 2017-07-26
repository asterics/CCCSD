<?php
require_once('errors.php');
require_once('connect.php');
require_once('helpfunc.php');

	$query = "select Username, password from userlogin where Username = '" . $_POST['uname'] . "' and Password = '" . $_POST['psw'] . "';";
	$result1 = mysql_query($query);
	
	if(!$result1 )
		echo '<script type="text/javascript"> window.alert("Wrong username or password") </script>';
	else
		header(model.php);
?>