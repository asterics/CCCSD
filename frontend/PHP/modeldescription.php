<?php

include 'connection.php';

$id = intval($_GET['id']);


$query = "select * from models where ID = " . $id . "";
$queryDevice = "select devicecategory.category from link_model_devicecategory, devicecategory 
  				where link_model_devicecategory.ID_DeviceCategory = devicecategory.ID 
      			and ID_ModelDev = " . $id . "";

$queryTechPRe = "select techprerequisites.device from link_model_techprerequisites, techprerequisites 
  				where link_model_techprerequisites.ID_techPrerequisite = techprerequisites.ID 
      			and ID_ModelTech = " . $id . "";

$queryBody = "select bodyfunctions.function from link_model_bodyfunction, bodyfunctions 
  				where link_model_bodyfunction.ID_bodyFunction = bodyfunctions.ID 
      			and ID_ModelBody = " . $id . "";

$queryUser ="select userlogin.Username 
			 from (models inner join link_user_models on models.id = link_user_models.ID_ModelUser) 
			 inner join userlogin on link_user_models.ID_UserModel = userlogin.UserID    
			 where link_user_models.ID_ModelUser = " . $id . "";
			 	
$result = mysql_query($query);
$resultName = mysql_query($query);
$resultDescription = mysql_query($query);
$resultDownloads = mysql_query($query);
$resultDev = mysql_query($queryDevice);
$resultBody = mysql_query($queryBody);
$resultTechPre = mysql_query($queryTechPRe);
$resultUser = mysql_query($queryUser);
$fileName = mysql_fetch_array($result);

echo '	<h1>Model Name: ';
while ($row = mysql_fetch_array($resultName)) {
	echo $row["name"];
}
echo '   </h1>';

echo'<p class="uploader"><i>Uploader: ';
while ($row = mysql_fetch_array($resultUser)) {
	echo $row["Username"];
}
echo '</i>';
echo'</br><i class="uploader">Downloads: ';
while ($row = mysql_fetch_array($resultDownloads)) {
	echo $row["downloads"];
}
echo '</i></p>';

echo '	<h2>Model description:</h2>';
	
while ($row = mysql_fetch_array($resultDescription)) {
	echo'<p class="modeldesc">';
	echo $row["modelDescription"];
	echo'</p>';
}

echo'<h2>Devices:</h2>';
echo'<ul class="modeldesc">';
while ($row = mysql_fetch_array($resultDev)) {
	echo'<li class="modeldesc">';
	echo $row["category"];
	echo'</li>';
}
echo'</ul>';

echo'<h2>Techprerequisits:</h2>';
echo'<ul class="modeldesc">';
while ($row = mysql_fetch_array($resultTechPre)) {
	echo'<li class="modeldesc">';
	echo $row["device"];
	echo'</li>';
}
echo'</ul>';

echo'<h2>BodyFunctions:</h2>';
echo'<ul class="modeldesc">';
while ($row = mysql_fetch_array($resultBody)) {
	echo'<li class="modeldesc">';
	echo $row["function"];
	echo'</li>';
}
echo'</ul>';

echo '
				<nav>
				<div class="buttonrow_r">
					<a href="#" onclick="getModelList()" id="back" class="namebtn modelbtn">Back</a>
					<a download="' . $fileName["fileName"] . '" href="http://localhost/Wizard/Backend/Models/M' . $id . '/M' . $id . '.acs" class="btn" onclick="countDownloads('.$id .')">Download</a>
					<a href="javascript:pushModel()" class="btn">Send to ARE</a>
					<a href="http://localhost/WebACS_JS/WebACS.html?../Front-End/Models/' . $fileName["fileName"] . '" class="btn">Send to ACS</a>
					</div>
				</nav>
			';
?>