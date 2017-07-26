<!doctype html>
<?php
	require_once('errors.php');
	require_once('connect.php');
	require_once('helpfunc.php');
	session_start();
	
	if($_SERVER["REQUEST_METHOD"] == "POST"){
		$username = $_POST['uname'];
		$psw = $_POST['psw'];
		
		$query = 'select UserID from userlogin where Username = "' . $username . '";' ;
		$result = mysql_query($query);
		
		if($result){
			$_SESSION['username'] = $username;
			
			$query = 'select password from userlogin where Username = "' . $_SESSION['username'] . '";';
			$result = mysql_query($query);
			
			if($result){
				$pswVerify = mysql_fetch_row($result);
				
				if(password_verify($psw, $pswVerify[0]))
				{
					$query = 'select ID from userlogin where username = "' . $username . '";';
					$result = mysql_query($query);
					$usrID = mysql_fetch_row($result);

					$query = 'select UserID from userlogin where username = "' . $username . '";';
					$result = mysql_query($query);
					$uID = mysql_fetch_array($result);
					$uID = $uID[0];
					
					$query = 'select active from users where ID = ' . $uID;
					$result = mysql_query($query);
					$active = mysql_fetch_array($result);
					$active = $active[0];
					
					if($active == 1){
						if(isset($usrID[0])){
							$_SESSION['ID'] = $usrID[0];
							$query1 = 'select userroles.Role from link_userlogin_roles inner join userroles on role_ID = userroles.ID where userlogin_ID = ' . $usrID[0] . ' and Role = "Admin";';
							$result1 = mysql_query($query1);
							$admin = mysql_fetch_row($result1);
							if(isset($admin[0])){
								$_SESSION['admin'] = 1;
							}else {
								$_SESSION['admin'] = 0;
							}
							
							$_SESSION['ID'] = $usrID[0];
							$query1 = 'select username from Userlogin where UserID = ' . $_SESSION['ID'] . ';';
							$result1 = mysql_query($query1);
							$usrname = mysql_fetch_row($result1);
							if(isset($usrname[0]))
								$_SESSION['username'] = $usrname[0];
							header("Location: mymodels.php");
						}
					} else {
						$error = "Username or password invalid.";
					} 
				}else {
					$error = "Password invalid.";
				}
			}
			
			
		}else {
			$error = "Username or password invalid.";
		}
	}
?>
<html lang="en">
<head>
	<title>Login</title>
	<link rel="stylesheet" href="../styles/main.css">
</head>
<body>
	<img src="../images/asterics_logo.png" class="asterics_login">
	<fieldset class="login">
	<h2 id="login">Prosperity4all backend user login</h2>
	<div class="Login">
	
		<form action="" method="post" class="login">
		
			<label><b>Username:</b></label>
			<input type="text" placeholder="Enter Username" name="uname" required><br/><br/>
			
			<label><b>Password:</b></label>
			<input type="password" placeholder="Enter password" name="psw" required><br /><br /><br />
			
			<button type="submit" class="Login">Login</button>
		</form><br />
		<?php
				if(isset($error))
					 echo $error; 
				if(isset($_GET['action'])){
					if($_GET['action'] == 'logout')
						echo 'You were successfully logged out.';
				}
		?>
	</div>
	</fieldset>
</body>
</html>
