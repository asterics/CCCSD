<!doctype html>
<html lang="en">
<head>
	<title>Bodyfunctions</title>
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
			<label class="username">Logged in as: <?php
				error_reporting(E_ALL);
				require('errors.php');
				require('connect.php');
				require_once('bodyfunction_class.php');
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
		<li class="side_active"><a href="bodyfunctions.php">Bodyfunctions</a></li>
		<li class="side"><a href="devicecategory.php">Device categories</a></li>
		</ul>
	</nav>
	<main class="content">
		<?php
			if(!isset($_SESSION['username']))
				header("Location: Login.php");
			
			$bodyfunction = new bodyfunctions();
			
			if (isset($_POST['cancel'])) $_GET['action'] = 'table';
			
			if (isset($_GET['action'])) {
				switch($_GET['action']) {
					case "new":
						if (isset($_GET['state'])) {
							switch($_GET['state']) {
								case "ok":
									$bodyfunctions = $_SESSION['bodyfunction'];
									echo $bodyfunction->CreateSelectorTable('Bodyfunctions', 'Bodyfunction &quot;' . $bodyfunctions->name . '&quot; was successfully saved.', '');
									break;
								case "error":
									$bodyfunction = $_SESSION['bodyfunction'];
									echo $bodyfunction->CreateSelectorTable('Bodyfunctions', '', 'Bodyfunction &quot;' . $bodyfunctions->name . '&quot; could not be saved.');
									break;
								case "do":
									$bodyfunction->name = $_POST['bodyName'];
									$err = $bodyfunction->ValidateFormData();
									if (!$err) {
										$_SESSION['bodyfunction'] = $bodyfunction;
										if ($bodyfunction->InsertDB()) {
											echo 'ok';
											header("Location: bodyfunctions.php?action=new&state=ok");					
										} else {
											echo 'not ok';
											header("Location: bodyfunctions.php?action=new&state=error");					
										}
									} else {
										echo $bodyfunction->CreateForm('New bodyfunction', "bodyfunctions.php?action=new&amp;state=do", '');
									}
									break;
							}
						} else {
							echo $bodyfunction->CreateForm('New bodyfunction', "bodyfunctions.php?action=new&amp;state=do", '');
						}
						break;
					case "update":
						if (isset($_GET['state'])) {
							switch($_GET['state']) {
								case "ok":
									$bodyfunction = $_SESSION['bodyfunction'];
									echo $bodyfunction->CreateSelectorTable('Bodyfunctions', 'Bodyfunction &quot;' . $bodyfunction->name . '&quot; was successfully updated.', '');
									break;
								case "error":
									$bodyfunction = $_SESSION['bodyfunction'];
									echo $bodyfunction->CreateSelectorTable('Bodyfunctions', '', 'Bodyfunction &quot;' . $bodyfunction->name . '&quot; could not be updated.');
									break;
								case "edit":
									if (isset($_GET['id'])) {
										$bodyfunction->ID = $_GET['id'];
										$bodyfunction->LoadDB();
										$_SESSION['bodyfunction'] = $bodyfunction;
										echo $bodyfunction->CreateForm('Update bodyfunction "' . $bodyfunction->name . '"', "bodyfunctions.php?action=update&amp;state=do", '');
									} else {
										echo $bodyfunction->CreateSelector('Update bodyfunction', "bodyfunctions.php?action=update&amp;state=edit", '');
									}
									break;
								case "do":
									$bodyfunction = $_SESSION['bodyfunction'];
									$bodyfunction->name = $_POST['bodyName'];
									$err = $bodyfunction->ValidateFormData();
									if (!$err) {
										if ($bodyfunction->UpdateDB()) {
											header("Location: bodyfunctions.php?action=update&state=ok");					
										} else {
											header("Location: bodyfunctions.php?action=update&state=error");					
										}
									} else {
										echo $bodyfunction->CreateForm('Update bodyfunction "' . $bodyfunction->name . '"', "bodyfunctions.php?action=update&amp;state=do", '');
									}
									break;
							} 
						} else {
							echo $bodyfunction->CreateSelectorTable('Bodyfunctions', '', '');
						}
						break;
					case "delete":
						if (isset($_GET['state'])) {
							switch($_GET['state']) {
								case "ok":
									$bodyfunction = $_SESSION['bodyfunction'];
									echo $bodyfunction->CreateSelectorTable('Bodyfunctions', 'Bodyfunction &quot;' . $bodyfunction->name . '&quot; was successfully deleted.', '');
									break;
								case "error":
									$bodyfunction = $_SESSION['bodyfunction'];
									echo $bodyfunction->CreateSelectorTable('Bodyfunctions', '', 'Bodyfunctionss &quot;' . $bodyfunction->name . '&quot; could not be deleted.');
									break;
								case "do":
									$bodyfunction->ID = $_GET['id'];
									$bodyfunction->LoadDB();
									$_SESSION['bodyfunction'] = $bodyfunction;
									if ($bodyfunction->DeleteDB()) {
										header("Location: bodyfunctions.php?action=delete&state=ok");					
									} else {
										header("Location: bodyfunctions.php?action=delete&state=error");					
									}
									break;
							} 
						} else {
							echo $bodyfunction->CreateSelectorTable('Bodyfunctions', '', '');
						}
						break;
					case "table":
						echo $bodyfunction->CreateSelectorTable('Bodyfunctions', '', '');
						break;
				}
			} else {
				echo $bodyfunction->CreateSelectorTable('Bodyfunctions', '', '');
			}
		?>
	</main>
	</div>
	<footer></footer>
</body>
</html>