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
		<li class="main_active"><a href="model.php"><span class="models"></span>Models</a></li>
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
		<li class="side_active"><a href="model.php">Models</a></li>
		<?php
			if($_SESSION['admin'] == 1){
				echo '<li class="side"><a href="techprerequisites.php">Tech-prerequesites</a></li>
						<li class="side"><a href="bodyfunctions.php">Bodyfunctions</a></li>
						<li class="side"><a href="devicecategory.php">Device categories</a></li>';
			}
		?>
		</ul>
	</nav>
	<main class="content">
		<?php
			if(!isset($_SESSION['username']))
				header("Location: Login.php");
			
			$model = new Model();
			$user = new User();
			
			if (isset($_POST['cancel'])) $_GET['action'] = 'table';
			
			if (isset($_GET['action'])) {
				switch($_GET['action']) {
					case "new":
						if (isset($_GET['state'])) {
							switch($_GET['state']) {
								case "ok":
									$model = $_SESSION['model'];
									echo $model->CreateSelectorTable('Models', 'model &quot;' . $model->name . '&quot; was successfully saved.', '');
									break;
								case "error":
									$model = $_SESSION['model'];
									echo $model->CreateSelectorTable('Models', '', 'model &quot;' . $model->name . '&quot; could not be saved. ');
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
										
										if ($model->ModelUpload($_FILES['filename']['tmp_name'])) {
											if($model->InsertDB()){
													if($model->ModelRename()){
														if(isset($_POST['devices'])){
															foreach($_POST['devices'] as $device){
																if(isset($device))
																	$model->ModelLinkTech($device);
															}
														}
														if(isset($_POST['categories'])){
															foreach($_POST['categories'] as $cat){
																if(isset($cat))
																	$model->ModelLinkDev($cat);
															}
														}
														if(isset($_POST['functions'])){
															foreach($_POST['functions'] as $func){
																if(isset($func))
																	$model->ModelLinkBody($func);
															}
														}
														echo 'ok';
														header("Location: model.php?action=new&state=ok");
													}else {
														header("Location: model.php?action=new&state=error");
													} 
														
												} else {
													echo 'not ok';
													header("Location: model.php?action=new&state=error");
												}											
												
											} else {
												echo 'not ok';
												header("Location: model.php?action=new&state=error");					
											}
										
									}else {
										echo $model->CreateForm('New model', "model.php?action=new&amp;state=do", '');
									}
									break;
							}
						} else {
							echo $model->CreateForm('New model', "model.php?action=new&amp;state=do", '');
						}
						break;
					case "update":
						if (isset($_GET['state'])) {
							switch($_GET['state']) {
								case "ok":
									$model = $_SESSION['model'];
									echo $model->CreateSelectorTable('Models', 'model &quot;' . $model->name . '&quot; was successfully updated.', '');
									break;
								case "error":
									$model = $_SESSION['model'];
									echo $model->CreateSelectorTable('Models', '', 'model &quot;' . $model->name . '&quot; could not be updated.');
									break;
								case "edit":
									if (isset($_GET['id'])) {
										$model->ID = $_GET['id'];
										$model->LoadDB();
										$_SESSION['model'] = $model;
										echo $model->CreateForm('Update model "' . $model->name . '"', "model.php?action=update&amp;state=do", '');
									} else {
										echo $model->CreateSelector('model update', "model.php?action=update&amp;state=edit", '');
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
											header("Location: model.php?action=new&state=ok");					
										} else {
											echo 'not ok';
											header("Location: model.php?action=new&state=error");					
										}
											 
									}else {
										echo $model->CreateForm('model update', "model.php?action=update&amp;state=do", '');
									}
									break;
							} 
						} else {
							echo $model->CreateSelectorTable('Models', '', '');
						}
						break;
					case "delete":
						if (isset($_GET['state'])) {
							switch($_GET['state']) {
								case "ok":
									$model = $_SESSION['model'];
									echo $model->CreateSelectorTable('Models', 'model &quot;' . $model->name . '&quot; was successfully deleted.', '');
									break;
								case "error":
									$model = $_SESSION['model'];
									echo $model->CreateSelectorTable('Models', '', 'models &quot;' . $model->name . '&quot; could not be deleted.');
									break;
								case "do":
									$model->ID = $_GET['id'];
									$model->LoadDB();
									$_SESSION['model'] = $model;
									if ($model->RemoveModel()){			
										if ($model->DeleteDB()) {
											header("Location: model.php?action=delete&state=ok");					
										} else {
											header("Location: model.php?action=delete&state=error");					
										}
										
									}
									else {
										header("Location: model.php?action=delete&state=error");
									}
									break;
							} 
						} else {
							echo $model->CreateSelectorTable('Models', '', '');
						}
						break;
					case "table":
						echo $model->CreateSelectorTable('Models', '', '');
						break;
				}
			} else {
				echo $model->CreateSelectorTable('Models', '', '');
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