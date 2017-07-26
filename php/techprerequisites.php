<!doctype html>
<html lang="en">
<head>
	<title>Tech-Prerequisites</title>
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
			<label  class="username">Logged in as: <?php
				error_reporting(E_ALL);
				require('errors.php');
				require('connect.php');
				require_once('techprerequisites_class.php');
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
		<li class="side"><a href="model.php">Models</a></li>
		<li class="side_active"><a href="techprerequisites.php">Tech-prerequesites</a></li>
		<li class="side"><a href="bodyfunctions.php">Bodyfunctions</a></li>
		<li class="side"><a href="devicecategory.php">Device categories</a></li>
		</ul>
	</nav>
	<main class="content">
		<?php
			if(!isset($_SESSION['username']))
				header("Location: Login.php");
		
			$techPre = new TechPrerequisites();
			
			if (isset($_POST['cancel'])) $_GET['action'] = 'table';
			
			if (isset($_GET['action'])) {
				switch($_GET['action']) {
					case "new":
						if (isset($_GET['state'])) {
							switch($_GET['state']) {
								case "ok":
									$techPre = $_SESSION['techprerequisites'];
									echo $techPre->CreateSelectorTable('Tech-Prerequesites', 'Tech-Prerequesite &quot;' . $techPre->name . '&quot; was successfully saved.', '');
									break;
								case "error":
									$techPre = $_SESSION['techprerequisites'];
									echo $techPre->CreateSelectorTable('Tech-Prerequesites', '', 'Tech-Prerequesite &quot;' . $techPre->name . '&quot; could not be saved.');
									break;
								case "do":
									$techPre->name = $_POST['techName'];
									$err = $techPre->ValidateFormData();
									if (!$err) {
										$_SESSION['techprerequisites'] = $techPre;
										if ($techPre->InsertDB()) {
											echo 'ok';
											header("Location: techprerequisites.php?action=new&state=ok");					
										} else {
											echo 'not ok';
											header("Location: techprerequisites.php?action=new&state=error");					
										}
									} else {
										echo $techPre->CreateForm('New tech-prerequisite', "techprerequisites.php?action=new&amp;state=do", '');
									}
									break;
							}
						} else {
							echo $techPre->CreateForm('New tech-prerequisite', "techprerequisites.php?action=new&amp;state=do", '');
						}
						break;
					case "update":
						if (isset($_GET['state'])) {
							switch($_GET['state']) {
								case "ok":
									$techPre = $_SESSION['techprerequisites'];
									echo $techPre->CreateSelectorTable('Tech-Prerequesites', 'Tech-Prerequesite &quot;' . $techPre->name . '&quot; was successfully updated.', '');
									break;
								case "error":
									$techPre = $_SESSION['techprerequisites'];
									echo $techPre->CreateSelectorTable('Tech-Prerequesites', '', 'Tech-Prerequesite &quot;' . $techPre->name . '&quot; could not be updated.');
									break;
								case "edit":
									if (isset($_GET['id'])) {
										$techPre->ID = $_GET['id'];
										$techPre->LoadDB();
										$_SESSION['techprerequisites'] = $techPre;
										echo $techPre->CreateForm('Update tech-prerequisite "' . $techPre->name . '"', "techprerequisites.php?action=update&amp;state=do", '');
									} else {
										echo $techPre->CreateSelector('Update tech-prerequisite', "techprerequisites.php?action=update&amp;state=edit", '');
									}
									break;
								case "do":
									$techPre = $_SESSION['techprerequisites'];
									$techPre->name = $_POST['techName'];
									$err = $techPre->ValidateFormData();
									if (!$err) {
										if ($techPre->UpdateDB()) {
											header("Location: techprerequisites.php?action=update&state=ok");					
										} else {
											header("Location: techprerequisites.php?action=update&state=error");					
										}
									} else {
										echo $techPre->CreateForm('Update tech-prerequisite', "techprerequisites.php?action=update&amp;state=do", '');
									}
									break;
							} 
						} else {
							echo $techPre->CreateSelectorTable('Tech-Prerequesites', '', '');
						}
						break;
					case "delete":
						if (isset($_GET['state'])) {
							switch($_GET['state']) {
								case "ok":
									$techPre = $_SESSION['techprerequisites'];
									echo $techPre->CreateSelectorTable('Tech-Prerequesites', 'Tech-Prerequesite &quot;' . $techPre->name . '&quot; was successfully deleted.', '');
									break;
								case "error":
									$techPre = $_SESSION['techprerequisites'];
									echo $techPre->CreateSelectorTable('Tech-Prerequesites', '', 'Tech-Prerequesite &quot;' . $techPre->name . '&quot; could not be deleted.');
									break;
								case "do":
									$techPre->ID = $_GET['id'];
									$techPre->LoadDB();
									$_SESSION['techprerequisites'] = $techPre;
									if ($techPre->DeleteDB()) {
										header("Location: techprerequisites.php?action=delete&state=ok");					
									} else {
										header("Location: techprerequisites.php?action=delete&state=error");					
									}
									break;
							} 
						} else {
							echo $techPre->CreateSelectorTable('Tech-Prerequesites', '', '');
						}
						break;
					case "table":
						echo $techPre->CreateSelectorTable('Tech-Prerequesites', '', '');
						break;
				}
			} else {
				echo $techPre->CreateSelectorTable('Tech-Prerequesites', '', '');
			}
		?>
	</main>
	</div>
	<footer></footer>
</body>
</html>