<?php
include 'connection.php';

$index = intval($_GET['index']);

$zeile = 0;

echo '	<div class="progress">
 			 <div id="progressbar" class="progress-bar" role="progressbar" aria-valuenow="70"
 				 aria-valuemin="0" aria-valuemax="100" style="width:50%">
  			</div>
		</div>';

echo '<h1 id="title"></h1>
			<p id="category"></p>
				<fieldset>
					<legend>
						Answers:
					</legend>';

if ($index == 0) {
	getDeviceCategory();
} else if ($index == 1) {
	getTechPreRequvisits();
} else if ($index == 2) {
	getBodyFunctions();
}

function getDeviceCategory() {
	$query = "select * from devicecategory order by category";
	$result = mysql_query($query);
	$zaehler = 0;

	echo '<ul class="box">';
	while ($row = mysql_fetch_array($result)) {
		if ($zaehler % 3 == 0 && $zaehler != 0)
			echo '</br>';
		echo '<li class="cbRow">';
		echo '<input onchange="saveChecked(0)" id="cb' . $zaehler . '" type="checkbox" name="answers" value="' . $row["ID"] . '" />';
		echo '<label for="cb' . $zaehler . '">' . $row["category"] . '</label>';
		echo '</li>';

		$zaehler++; 	
	}
		echo '</ul>';
}

function getTechPreRequvisits() {
	$query = "select * from techprerequisites order by device";
	$result = mysql_query($query);
	$zaehler = 0;
	
	echo '<ul class="box">';
	while ($row = mysql_fetch_array($result)) {
		if ($zaehler % 3 == 0 && $zaehler != 0)
			echo '</br>';
		echo '<li class="cbRow">';
		echo '<input onchange="saveChecked(1)" id="cb' . $zaehler . '" type="checkbox" name="answers" value="' . $row["ID"] . '" />';
		echo '<label for="cb' . $zaehler . '">' . $row["device"] . '</label>';
		echo '</li>';

		$zaehler++;
	}
	echo '</ul>';
}

function getBodyFunctions() {
	$query = "select * from bodyfunctions order by function";
	$result = mysql_query($query);
	$zaehler = 0;
	
	echo '<ul class="box">';
	while ($row = mysql_fetch_array($result)) {
		if ($zaehler % 3 == 0 && $zaehler != 0)
			echo '</br>';
		echo '<li class="cbRow">';
		echo '<input onchange="saveChecked(2)" id="cb' . $zaehler . '" type="checkbox" name="answers" value="' . $row["ID"] . '" />';
		echo '<label for="cb' . $zaehler . '">' . $row["function"] . '</label>';
		echo '</li>';

		$zaehler++;
	}
		echo '</ul>';
}

echo '
				</fieldset>
				<p class="buttonrow">
					<a onclick="prevQuestion()" href="#" id="prevQ" class="prevQ">Previous question</a>
					<a onclick="GetPreRequisits()" id="next" href="#" class="ok">Next</a>
				</p>';
?>

