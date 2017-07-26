<!doctype html>
<html lang="en">
<head>
	<title>Registration</title>
	<link rel="stylesheet" href="../styles/main.css">
</head>
<body>
<?php
			error_reporting(E_ALL);
			require('errors.php');
			require('connect.php');
			require_once('user_class.php');
			
			$user = new User();
			
			session_start();
			if (isset($_GET['state'])) {
				switch($_GET['state']) {
									case "ok":
										$user = $_SESSION['user'];
										echo $user->CreateSelectorTable('users', 'user &quot;' . $user->username . '&quot; was successfully saved.', '');
										break;
									case "error":
										$user = $_SESSION['user'];
										echo $user->CreateSelectorTable('users', '', 'user &quot;' . $user->username . '&quot; could not be saved.');
										break;
									case "do":
										$user->firstName = $_POST['firstName'];
										$user->lastName = $_POST['lastName'];
										$user->email = $_POST['email'];
										$err = $user->ValidateFormData();
										if (!$err) {
											$_SESSION['user'] = $user;
											
											if(isset($_POST['admin'])){
												$user->role = 1;
												if ($user->InsertDB()) {	
													if(isset($_POST['normUser'])){
														$user->role = 2;
														if ($user->InsertDB()) {
															echo 'ok';
															header("Location: register.php?action=new&state=ok");
														}else {
														echo 'not ok';
														header("Location: register.php?action=new&state=error");					
														}
													}else {
														echo 'not ok';
														header("Location: register.php?action=new&state=error");					
													}
												}else {
													echo 'not ok';
													header("Location: register.php?action=new&state=error");					
												}
											}
										}else {
											echo $user->CreateForm('new user', "register.php?action=new&amp;state=do", '');
										}
										break;
				}
			}else {
				echo $user->CreateForm('user new', "register.php?action=new&amp;state=do", '');
			}
?>
</body>
</html>