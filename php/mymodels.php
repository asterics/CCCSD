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
		<li class="main"><a href="Users.php"><span class="users"></span>Users</a></li>
		<li class="main"><a href="model.php"><span class="models"></span>Models</a></li>
		<li class="main_active"><a href="mymodels.php"><span class="myModels"></span>My Models</a></li>
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
	</nav>
	<main class="content">
		<?php
			if(!isset($_SESSION['username']))
				header("Location: Login.php");
			
			$model = new Model();
			
			if (isset($_POST['cancel'])) $_GET['action'] = 'table';
			
			if (isset($_GET['action'])) {
				switch($_GET['action']) {
					case "new":
						if (isset($_GET['state'])) {
							switch($_GET['state']) {
								case "ok":
									$model = $_SESSION['model'];
									echo $model->CreateMySelectorTable('models', 'model &quot;' . $model->name . '&quot; was successfully saved.', '');
									break;
								case "error":
									$model = $_SESSION['model'];
									echo $model->CreateMySelectorTable('models', '', 'model &quot;' . $model->name . '&quot; could not be saved.');
									break;
								case "do":
									$model->name = $_POST['name'];
									$model->modelDescription = $_POST['modelDescription'];
									$model->filename = $_FILES['filename']['name'];
									if(isset($_POST['approved']))
										$model->approved = $_POST['approved'];
									$err = $model->ValidateFormData();
									if (!$err) {
										$_SESSION['model'] = $model;
										
										if ($model->InsertDB()) {
											if($model->ModelUpload($_FILES['filename']['tmp_name'])){
													foreach($_POST['devices'] as $device){
														if(isset($device))
															$model->ModelLinkTech($device);
													}
													foreach($_POST['categories'] as $cat){
														if(isset($cat))
															$model->ModelLinkDev($cat);
													}
													foreach($_POST['functions'] as $func){
														if(isset($func))
															$model->ModelLinkBody($func);
													}
													echo 'ok';
													header("Location: mymodels.php?action=new&state=ok");
												} else {
												echo 'not ok';
												header("Location: mymodels.php?action=new&state=error");
												}											
												
											} else {
												echo 'not ok';
												header("Location: mymodels.php?action=new&state=error");					
											}
										
									}else {
										echo $model->CreateForm('new model', "mymodels.php?action=new&amp;state=do", '');
									}
									break;
							}
						} else {
							echo $model->CreateForm('model new', "mymodels.php?action=new&amp;state=do", '');
						}
						break;
					case "update":
						if (isset($_GET['state'])) {
							switch($_GET['state']) {
								case "ok":
									$model = $_SESSION['model'];
									echo $model->Create-MySelectorTable('My models', 'model &quot;' . $model->name . '&quot; was successfully updated.', '');
									break;
								case "error":
									$model = $_SESSION['model'];
									echo $model->CreateMySelectorTable('My models', '', 'model &quot;' . $model->name . '&quot; could not be updated.');
									break;
								case "edit":
									if (isset($_GET['id'])) {
										$model->ID = $_GET['id'];
										$model->LoadDB();
										$_SESSION['model'] = $model;
										echo $model->CreateForm('update model "' . $model->name . '"', "mymodels.php?action=update&amp;state=do", '');
									}else {
										echo $model->CreateSelector('model update', "mymodels.php?action=update&amp;state=edit", '');
									}
									break;
								case "do":
									$model = $_SESSION['model'];
									$model->name = $_POST['name'];
									$model->modelDescription = $_POST['modelDescription'];
									if(isset($_FILES['filename']['name']))
										$model->filename = $_FILES['filename']['name'];
									else
										$model->filename = '';
									if(isset($_POST['approved']))
										$model->approved = $_POST['approved'];
									$err = $model->ValidateFormDataUpdate();
									if (!$err) {
										$_SESSION['model'] = $model;
										if($model->filename != ''){
												$model->ModelUpdate($_FILES['filename']['tmp_name']);		
										}
										
										if ($model->UpdateDB()) {
											foreach($_POST['devices'] as $device){
												if(isset($device))
													$model->ModelLinkTech($device);
											}
											foreach($_POST['categories'] as $cat){
												if(isset($cat))
													$model->ModelLinkDev($cat);
											}
											foreach($_POST['functions'] as $func){
												if(isset($func))
													$model->ModelLinkBody($func);
											}
											echo 'ok';
											header("Location: mymodels.php?action=new&state=ok");					
										} else {
											echo 'not ok';
											header("Location: mymodels.php?action=new&state=error");					
										}
											 
									}else {
										echo $model->CreateForm('model update', "mymodels.php?action=update&amp;state=do", '');
									}
									break;
							} 
						} else {
							echo $model->CreateMySelectorTable('My models', '', '');
						}
						break;
					case "delete":
						if (isset($_GET['state'])) {
							switch($_GET['state']) {
								case "ok":
									$model = $_SESSION['model'];
									echo $model->CreateMySelectorTable('My models', 'model &quot;' . $model->name . '&quot; was successfully deleted.', '');
									break;
								case "error":
									$model = $_SESSION['model'];
									echo $model->CreateMySelectorTable('My models', '', 'models &quot;' . $model->name . '&quot; could not be deleted.');
									break;
								case "do":
									$model->ID = $_GET['id'];
									$model->LoadDB();
									$_SESSION['model'] = $model;
									if ($model->RemoveModel()){			
										if ($model->DeleteDB($model->ID)) {
											header("Location: mymodels.php?action=delete&state=ok");					
										} else {
											header("Location: mymodels.php?action=delete&state=error");					
										}
										
									}
									else {
										header("Location: mymodels.php?action=delete&state=error");
									}
									break;
							} 
						} else {
							echo $model->CreateMySelectorTable('My models', '', '');
						}
						break;
					case "table":
						echo $model->CreateMySelectorTable('My models', '', '');
						break;
				}
			} else {
				echo $model->CreateMySelectorTable('My models', '', '');
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