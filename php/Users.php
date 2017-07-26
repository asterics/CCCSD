<!doctype html>
<html lang="en">
<head>
	<title>Model</title>
	<link rel="stylesheet" href="../styles/main.css">
</head>
<body>
	<header></header>
	<img src="../images/asterics_logo.png" class="asterics">
	<nav id="mainmenu">
		<ul class="horizontal">
		<li class="main_active"><a href="users.php"><span class="users"></span>Users</a></li>
		<li class="main"><a href="model.php"><span class="models"></span>Models</a></li>
		<li class="main"><a href="mymodels.php"><span class="myModels"></span>My Models</a></li>
		<li class="username">
			<label class="username">Logged in as: <?php
				error_reporting(E_ALL);
				require('errors.php');
				require('connect.php');
				require_once('model_class.php');
				require_once('user_class.php');
				session_start();			
				echo $_SESSION['username']; 
			?></label>
		</li>
		<li class="Logout">
			<form action="Logout.php" method="post" name="logout" id="logout" class="logout">
				<input type="submit" value="Logout" class="Logout">
			</form>
		</li>
		</ul>
	</nav>
	<div class="submenumaincontainer">
	<nav id="submenu">
		<ul class ="vertical">
		<li class="side_active"><a href="users.php">Users</a></li>
		<?php
			if($_SESSION['admin'] == 1){
				echo '<li class="side"><a href="roles.php">Roles</a></li>';
			}
		?>
		</ul>
	</nav>
	<main class="content">
		<?php
			if(!isset($_SESSION['username']))
				header("Location: Login.php");
			
			$user = new User();
			if (isset($_POST['cancel'])) $_GET['action'] = 'table';
			
			if (isset($_GET['action'])) {
				switch($_GET['action']) {
					case "new":
						if (isset($_GET['state'])) {
							switch($_GET['state']) {
								case "ok":
									$user = $_SESSION['user'];
									echo $user->CreateSelectorTable('Users', 'User &quot;' . $user->username . '&quot; was successfully saved.', '');
									break;
								case "error":
									$user = $_SESSION['user'];
									echo $user->CreateSelectorTable('Users', '', 'User &quot;' . $user->username . '&quot; could not be saved.');
									break;
								case "do":
									$user->firstName = $_POST['firstName'];
									$user->lastName = $_POST['lastName'];
									$user->email = $_POST['email'];
									$user->username = $_POST['username'];
									$user->password = $_POST['password'];
									$user->vPassword = $_POST['vpassword'];
									$err = $user->ValidateFormData();
									if (!$err) {
										$_SESSION['user'] = $user;
										
										if ($user->InsertDB()) {
											foreach($_POST['Roles'] as $r){
												if(isset($r))
													$user->UserLinkRole($r);
											}											
											echo 'ok';
											header("Location: users.php?action=new&state=ok");
										}else {
											echo 'not ok';
											header("Location: users.php?action=new&state=error");					
										}
									}else {
										echo $user->CreateForm('New user', "users.php?action=new&amp;state=do", '');
									}
									break;
							}
						} else {
							echo $user->CreateForm('New user', "users.php?action=new&amp;state=do", '');
						}
						break;
					case "update":
						if (isset($_GET['state'])) {
							switch($_GET['state']) {
								case "ok":
									$user = $_SESSION['user'];
									echo $user->CreateSelectorTable('Users', 'User &quot;' . $user->username . '&quot; was successfully updated.', '');
									break;
								case "error":
									$user = $_SESSION['user'];
									echo $user->CreateSelectorTable('Users', '', 'User &quot;' . $user->username . '&quot; could not be updated.');
									break;
								case "edit":
									if (isset($_GET['ID'])) {
										$user->loginID = $_GET['ID'];
										$user->LoadDB();
										$_SESSION['user'] = $user;
										echo $user->CreateUpdateForm('Update user "' . $user->username . '"', "users.php?action=update&amp;state=do", '');
									} else {
										echo $user->CreateSelector('user update', "users.php?action=update&amp;state=edit", '');
									}
									break;
								case "do":
									$user = $_SESSION['user'];
									$user->firstName = $_POST['firstName'];
									$user->lastName = $_POST['lastName'];
									$user->email = $_POST['email'];
									$user->password = $_POST['password'];
									$user->vPassword = $_POST['vpassword'];
									$err = $user->ValidateUpdateFormData();
									if (!$err) {
										$_SESSION['user'] = $user;
										if($user->UpdateDB()){
											foreach($_POST['Roles'] as $r){
												if(isset($r))
													$user->UserLinkRole($r);
											}
											echo 'ok';
											header("Location: users.php?action=update&state=ok");
										}else {
											echo 'not ok';
											header("Location: users.php?action=update&state=error");
										}
											 
									} else {
										echo $user->CreateForm('user update', "users.php?action=update&amp;state=do", '');
									}
									break;
							} 
						} else {
							echo $user->CreateSelectorTable('Users', '', '');
						}
						break;
					case "delete":
						if (isset($_GET['state'])) {
							switch($_GET['state']) {
								case "ok":
									$user = $_SESSION['user'];
									echo $user->CreateSelectorTable('Users', 'User &quot;' . $user->username . '&quot; was successfully deleted.', '');
									break;
								case "error":
									$user = $_SESSION['user'];
									echo $user->CreateSelectorTable('Users', '', 'User &quot;' . $user->username . '&quot; could not be deleted.');
									break;
								case "do":
									$user->ID = $_GET['ID'];
									$user->loginID = $_GET['LoginID'];
									$user->LoadDB();
									$logout = false;
									
									if($user->loginID == $_SESSION['ID'])
										$logout = true;
									
									$_SESSION['user'] = $user;	
									
									if ($user->DeleteDB()) {
										if($logout == true){
											$logout = false;
											header("Location: Login.php");
										}
										else
											header("Location: users.php?action=delete&state=ok");
									}else {
										header("Location: users.php?action=delete&state=error");					
									}
									break;
							} 
						} else {
							echo $user->CreateSelectorTable('Users', '', '');
						}
						break;
					case "table":
						echo $user->CreateSelectorTable('Users', '', '');
						break;
				}
			} else {
				echo $user->CreateSelectorTable('Users', '', '');
			}
		?>
	</main>
	</div>
	<footer></footer>
</body>
</html>


<!--
</body>
</html>-->