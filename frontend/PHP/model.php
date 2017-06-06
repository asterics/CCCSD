<?php

include 'connection.php';

$allArrays = file_get_contents("php://input");
$allArrays = json_decode($allArrays);

$DeviceArray = $allArrays[0];
$TechPreArray = $allArrays[1];
$BodyArray = $allArrays[2];

$loop = 0;
$index = 0;
$string = "";
$joinwhere = " ";
$jointable = "";

$query = "";

if (!empty($DeviceArray) and empty($TechPreArray) and empty($BodyArray)) {
	$jointable = "select models.ID, models.name, models.fileName
	from models, link_model_devicecategory";

	$joinwhere .= "where models.ID = link_model_devicecategory.ID_ModelDev";
} elseif (!empty($DeviceArray) and !empty($TechPreArray) and empty($BodyArray)) {
	$jointable = "select models.ID, models.name, models.fileName 
	from models, link_model_devicecategory, link_model_techprerequisites";

	$joinwhere .= "where models.ID = link_model_devicecategory.ID_ModelDev
				   and models.ID = link_model_techprerequisites.ID_modelTech";
} elseif (empty($DeviceArray) and !empty($TechPreArray) and empty($BodyArray)) {
	$jointable = "select models.ID, models.name, models.fileName
	from models, link_model_techprerequisites";

	$joinwhere .= "where models.ID = link_model_techprerequisites.ID_modelTech";
} elseif (empty($DeviceArray) and !empty($TechPreArray) and !empty($BodyArray)) {
	$jointable = "select models.ID, models.name, models.fileName
	from models, link_model_bodyfunction, link_model_techprerequisites";

	$joinwhere .= "where models.ID = link_model_techprerequisites.ID_modelTech
				   and models.ID = link_model_bodyfunction.ID_modelBody";
} elseif (empty($DeviceArray) and empty($TechPreArray) and !empty($BodyArray)) {
	$jointable = "select models.ID, models.name, models.fileName
	from models, link_model_bodyfunction";

	$joinwhere .= "where models.ID = link_model_bodyfunction.ID_modelBody";
} elseif (!empty($DeviceArray) and empty($TechPreArray) and !empty($BodyArray)) {
	$jointable = "select models.ID, models.name, models.fileName
	from models, link_model_bodyfunction, link_model_devicecategory";

	$joinwhere .= "where models.ID = link_model_devicecategory.ID_ModelDev
				   and models.ID = link_model_bodyfunction.ID_modelBody";
} elseif (!empty($DeviceArray) and !empty($TechPreArray) and !empty($BodyArray)) {
	$jointable = "select models.ID, models.name, models.fileName
				  from models, link_model_bodyfunction, link_model_devicecategory, link_model_techprerequisites";

	$joinwhere .= "where models.ID = link_model_bodyfunction.ID_modelBody 
					and models.ID = link_model_devicecategory.ID_ModelDev
					and models.ID = link_model_techprerequisites.ID_modelTech";
} elseif (empty($DeviceArray) and empty($TechPreArray) and empty($BodyArray)) {
	$jointable = "select models.ID, models.name, models.fileName from models";
}

$string = "";

if (!empty($DeviceArray)) {
	$string .= " and (";
	while ($loop < count($DeviceArray)) {
		$string .= " link_model_devicecategory.ID_DeviceCategory  = " . $DeviceArray[$loop] . " or ";
		$loop++;
	}
	$string = substr($string, 0, strrpos($string, " or "));
	$string .= " )";
	$joinwhere .= $string;
}

$loop = 0;
$string = "";
if (!empty($TechPreArray)) {
	$string .= " and (";
	while ($loop < count($TechPreArray)) {
		$string .= " link_model_techprerequisites.ID_techPrerequisite = " . $TechPreArray[$loop] . " or ";
		$loop++;
	}
	$string = substr($string, 0, strrpos($string, " or "));
	$string .= " )";
	$joinwhere .= $string;
}

$loop = 0;
$string = "";
if (!empty($BodyArray)) {
	$string .= " and (";
	while ($loop < count($BodyArray)) {
		$string .= " link_model_bodyfunction.ID_bodyFunction = " . $BodyArray[$loop] . " or ";
		$loop++;
	}
	$string = substr($string, 0, strrpos($string, " or "));
	$string .= " )";
	$joinwhere .= $string;
}

$query = $jointable . $joinwhere;
$result = mysql_query($query);
//echo $query;

$anzahl = 0;
if ($result)
	$anzahl = mysql_num_rows($result);

if ($anzahl != 0) {
	echo '	<div class="progress">
 			 <div id="progressbar" class="progress-bar" role="progressbar" aria-valuenow="70"
  				aria-valuemin="0" aria-valuemax="100" style="width:70%">
    
 			 </div>
		</div>';
	echo '<h1>Result</h1>';
	echo '<p id="TableDescription" class="hidden">In this Table there are the Models</p>';
	echo '<table aria-describedby="TableDescription">
		<caption>Modellist</caption>
	   	<tr class="gerade">
		<th scope="col" class="id">ID</th>
		<th scope="col" class="name">Name</th>
		<th  scope="col" class="interactions">Download</th>
		<th scope="col" class="interactions">Send to ACS</th>
		<th scope="col" class="interactions">Send to ARE</th>
		</tr>';

	while ($row = mysql_fetch_array($result)) {
		if ($index % 2 == 0) {
			echo '<tr class="ungerade">';
		} else {
			echo '<tr class="gerade">';
		}

		echo '<td class="id">' . $row["ID"] . '</td>';
		echo '<td scope="row"  id="' . $row["ID"] . '"><a href="#" class="name" onclick="getModel(' . $row["ID"] . ')">' . $row["name"] . '</a></th>';
		echo '<td class="interactions"><a id="downloadModel" download="' . $row["fileName"] . '" href="http://localhost/Wizard/Backend/Models/M' . $row["ID"] . '/M' . $row["ID"] . '.acs"><img src="../Pictures/1.svg" alt="Download ' . $row["name"] . '" title="Download ' . $row["name"] . '" class="pic"></a></td>';
		echo '<td class="interactions" title="Send ' . $row["name"] . ' to ACS"><a href="http://localhost/WebACS_JS/WebACS.html?../Backend/Models/M' . $row["ID"] . '/M' . $row["ID"] . '.acs"><img src="../Pictures/2.svg" alt="Send ' . $row["name"] . ' to ACS" title="Send ' . $row["name"] . ' to ACS" class="pic"></a></td>';
		echo '<td class="interactions" title="Send ' . $row["name"] . ' to ARE"><a href="javascript:pushModel()"><img src="../Pictures/2.svg" alt="Send ' . $row["name"] . ' to ARE" title="Send ' . $row["name"] . ' to ARE" class="pic"></a></td>';
		echo '</tr>';

		$index++;
	}
	echo '</table>';
} else {
	echo '	<div class="progress">
 			 <div id="progressbar" class="progress-bar" role="progressbar" aria-valuenow="70"
  				aria-valuemin="0" aria-valuemax="100" style="width:70%">
    
 			 </div>
		</div>';
	echo '<h1>Result</h1>';
	echo 'Sorry - there are no Models available that meet your requirements.';
}
echo '<p class="buttonrow"><a href="#" onclick="moveBack()" class="namebtn">Back</a></p>';
?>