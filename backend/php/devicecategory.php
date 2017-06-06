<!doctype html>
<html lang="en">
<head>
	<title>Device Categories</title>
	<link rel="stylesheet" href="../styles/main.css">
</head>
<body>
	<header></header>
	<img src="../images/asterics_logo.png" class="asterics">
	<nav id="mainmenu">
		<ul class="horizontal">
		<li class="main"><a href="Users.php"><span class="users"></span>Users</a></li>
		<li class="main_active"><a href="model.php"><span class="models"></span>Models</a></li>
		<li class="main"><a href="mymodels.php"><span class="myModels"></span>My Models</a></li>
		<li class="username">
			<label class="username"s>Logged in as: <?php
				error_reporting(E_ALL);
				require('errors.php');
				require('connect.php');
				require_once('devicecategory_class.php');
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
		<li class="side"><a href="model.php">Models</a></li>
		<li class="side"><a href="techprerequisites.php">Tech-prerequesites</a></li>
		<li class="side"><a href="bodyfunctions.php">Bodyfunctions</a></li>
		<li class="side_active"><a href="devicecategory.php">Device categories</a></li>
		</ul>
	</nav>
	<main class="content">
		<?php
			if(!isset($_SESSION['username']))
				header("Location: Login.php");
			
			$devCat = new DeviceCategory();
			
			if (isset($_POST['cancel'])) $_GET['action'] = 'table';
			
			if (isset($_GET['action'])) {
				switch($_GET['action']) {
					case "new":
						if (isset($_GET['state'])) {
							switch($_GET['state']) {
								case "ok":
									$devCat = $_SESSION['devicecategory'];
									echo $devCat->CreateSelectorTable('Device category', 'Device Categories &quot;' . $devCat->name . '&quot; was successfully saved.', '');
									break;
								case "error":
									$devCat = $_SESSION['devicecategory'];
									echo $devCat->CreateSelectorTable('Device categories', '', 'Device Category &quot;' . $devCat->name . '&quot; could not be saved.');
									break;
								case "do":
									$devCat->name = $_POST['devName'];
									$err = $devCat->ValidateFormData();
									if (!$err) {
										$_SESSION['devicecategory'] = $devCat;
										if ($devCat->InsertDB()) {
											echo 'ok';
											header("Location: devicecategory.php?action=new&state=ok");					
										} else {
											echo 'not ok';
											header("Location: devicecategory.php?action=new&state=error");					
										}
									} else {
										echo $devCat->CreateForm('new devicecategory', "devicecategory.php?action=new&amp;state=do", '');
									}
									break;
							}
						} else {
							echo $devCat->CreateForm('new devicecategory', "devicecategory.php?action=new&amp;state=do", '');
						}
						break;
					case "update":
						if (isset($_GET['state'])) {
							switch($_GET['state']) {
								case "ok":
									$devCat = $_SESSION['devicecategory'];
									echo $devCat->CreateSelectorTable('Device categories', 'Device category &quot;' . $devCat->name . '&quot; was successfully updated.', '');
									break;
								case "error":
									$devCat = $_SESSION['devicecategory'];
									echo $devCat->CreateSelectorTable('Device categories', '', 'Device category &quot;' . $devCat->name . '&quot; could not be updated.');
									break;
								case "edit":
									if (isset($_GET['id'])) {
										$devCat->ID = $_GET['id'];
										$devCat->LoadDB();
										$_SESSION['devicecategory'] = $devCat;
										echo $devCat->CreateForm('update device category "' . $devCat->name . '"', "devicecategory.php?action=update&amp;state=do", '');
									} else {
										echo $devCat->CreateSelector('devicecategory update', "devicecategory.php?action=update&amp;state=edit", '');
									}
									break;
								case "do":
									$devCat = $_SESSION['devicecategory'];
									$devCat->name = $_POST['devName'];
									$err = $devCat->ValidateFormData();
									if (!$err) {
										if ($devCat->UpdateDB()) {
											header("Location: devicecategory.php?action=update&state=ok");					
										} else {
											header("Location: devicecategory.php?action=update&state=error");					
										}
									} else {
										echo $devCat->CreateForm('devicecategory update', "devicecategory.php?action=update&amp;state=do", '');
									}
									break;
							} 
						} else {
							echo $devCat->CreateSelectorTable('Device categories', '', '');
						}
						break;
					case "delete":
						if (isset($_GET['state'])) {
							switch($_GET['state']) {
								case "ok":
									$devCat = $_SESSION['devicecategory'];
									echo $devCat->CreateSelectorTable('Device categories', 'Device category &quot;' . $devCat->name . '&quot; was successfully deleted.', '');
									break;
								case "error":
									$devCat = $_SESSION['devicecategory'];
									echo $devCat->CreateSelectorTable('Device categories', '', 'Device categories &quot;' . $devCat->name . '&quot; could not be deleted.');
									break;
								case "do":
									$devCat->ID = $_GET['id'];
									$devCat->LoadDB();
									$_SESSION['devicecategory'] = $devCat;
									if ($devCat->DeleteDB()) {
										header("Location: devicecategory.php?action=delete&state=ok");					
									} else {
										header("Location: devicecategory.php?action=delete&state=error");					
									}
									break;
							} 
						} else {
							echo $devCat->CreateSelectorTable('Device categories', '', '');
						}
						break;
					case "table":
						echo $devCat->CreateSelectorTable('Device categories', '', '');
						break;
				}
			} else {
				echo $devCat->CreateSelectorTable('Device categories', '', '');
			}
		?>
	</main>
	</div>
	<footer></footer>
</body>
</html>