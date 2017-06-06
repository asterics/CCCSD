<!doctype html>
<html lang="en">
<head>
	<title>Roles</title>
	<link rel="stylesheet" href="../styles/main.css">
</head>
<body>
	<header></header>
	<img src="../images/asterics_logo.png" class="asterics">
	<nav id="mainmenu">
		<ul class="horizontal">
		<li class="main_active"><a href="Users.php"><span class="users"></span>Users</a></li>
		<li class="main"><a href="model.php"><span class="models"></span>Models</a></li>
		<li class="main"><a href="mymodels.php"><span class="myModels"></span>My Models</a></li>
		<li class="username">
			<label class="username">Logged in as: <?php
				error_reporting(E_ALL);
				require('errors.php');
				require('connect.php');
				require_once('roles_class.php');
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
		<li class="side"><a href="Users.php">Users</a></li>
		<li class="side_active"><a href="roles.php">Roles</a></li>
		</ul>
	</nav>
	<main class="content">
		<?php
			if(!isset($_SESSION['username']))
				header("Location: Login.php");
			
			$role = new roles();
			
			if (isset($_POST['cancel'])) $_GET['action'] = 'table';
			
			if (isset($_GET['action'])) {
				switch($_GET['action']) {
					case "new":
						if (isset($_GET['state'])) {
							switch($_GET['state']) {
								case "ok":
									$roles = $_SESSION['role'];
									echo $role->CreateSelectorTable('Roles', 'Role &quot;' . $roles->name . '&quot; was successfully saved.', '');
									break;
								case "error":
									$role = $_SESSION['role'];
									echo $role->CreateSelectorTable('Roles', '', 'Role &quot;' . $roles->name . '&quot; could not be saved.');
									break;
								case "do":
									$role->name = $_POST['roleName'];
									$err = $role->ValidateFormData();
									if (!$err) {
										$_SESSION['role'] = $role;
										if ($role->InsertDB()) {
											echo 'ok';
											header("Location: roles.php?action=new&state=ok");					
										} else {
											echo 'not ok';
											header("Location: roles.php?action=new&state=error");					
										}
									} else {
										echo $role->CreateForm('New role', "roles.php?action=new&amp;state=do", '');
									}
									break;
							}
						} else {
							echo $role->CreateForm('New role', "roles.php?action=new&amp;state=do", '');
						}
						break;
					case "update":
						if (isset($_GET['state'])) {
							switch($_GET['state']) {
								case "ok":
									$role = $_SESSION['role'];
									echo $role->CreateSelectorTable('Roles', 'Role &quot;' . $role->name . '&quot; was successfully updated.', '');
									break;
								case "error":
									$role = $_SESSION['role'];
									echo $role->CreateSelectorTable('Roles', '', 'Role &quot;' . $role->name . '&quot; could not be updated.');
									break;
								case "edit":
									if (isset($_GET['id'])) {
										$role->ID = $_GET['id'];
										$role->LoadDB();
										$_SESSION['role'] = $role;
										echo $role->CreateForm('Update role "' . $role->name . '"', "roles.php?action=update&amp;state=do", '');
									} else {
										echo $role->CreateSelector('Update role "' . $role->name . '"', "roles.php?action=update&amp;state=edit", '');
									}
									break;
								case "do":
									$role = $_SESSION['role'];
									$role->name = $_POST['roleName'];
									$err = $role->ValidateFormData();
									if (!$err) {
										if ($role->UpdateDB()) {
											header("Location: roles.php?action=update&state=ok");					
										} else {
											header("Location: roles.php?action=update&state=error");					
										}
									} else {
										echo $role->CreateForm('Update role "' . $role->name . '"', "roles.php?action=update&amp;state=do", '');
									}
									break;
							} 
						} else {
							echo $role->CreateSelectorTable('Roles', '', '');
						}
						break;
					case "delete":
						if (isset($_GET['state'])) {
							switch($_GET['state']) {
								case "ok":
									$role = $_SESSION['role'];
									echo $role->CreateSelectorTable('Roles', 'Role &quot;' . $role->name . '&quot; was successfully deleted.', '');
									break;
								case "error":
									$role = $_SESSION['role'];
									echo $role->CreateSelectorTable('Roles', '', 'Roless &quot;' . $role->name . '&quot; could not be deleted.');
									break;
								case "do":
									$role->ID = $_GET['id'];
									$role->LoadDB();
									$_SESSION['role'] = $role;
									if ($role->DeleteDB()) {
										header("Location: roles.php?action=delete&state=ok");					
									} else {
										header("Location: roles.php?action=delete&state=error");					
									}
									break;
							} 
						} else {
							echo $role->CreateSelectorTable('Roles', '', '');
						}
						break;
					case "table":
						echo $role->CreateSelectorTable('Roles', '', '');
						break;
				}
			} else {
				echo $role->CreateSelectorTable('Roles', '', '');
			}
		?>
	</main>
	</div>
	<footer></footer>
</body>
</html>