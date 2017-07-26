<?php
require_once('errors.php');
require_once('connect.php');
require_once('helpfunc.php');

abstract class BaseClass {
	var $err;
	
	public $ID;
	public $name;
	
	
	function __construct(){
		$this->err = new Errors();
	}

	public function ValidateFormData() {
		$this->err = new Errors();
		$error = false;
		if ($this->name == '') {
			$error |= $this->err->CollectErrorsAltTexts('No data entered. Please enter data.', 'No data entered.', 'name');
		}
		return $error;
	}

	//type = DeviceCategory || TechPrerequisite || Bodyfunction
	public function CreateForm($caption, $formaction, $successtext, $type) { 
		$errortext = $this->err->CreateErrorTextList();
		$erroralttextlist = $this->err->CreateAltTextList();
		$errorlist = $this->err->CreateErrorList();
		$content = '';
		$content .= '<h1>' . $caption . '</h1>';
		if ($successtext) {
			$content .= '<div class="successMessage"><p>' . $successtext . '</p></div>';
		}
		if ($errortext) {
			$content .= '<div class="errorMessage"><p>' . $errortext . '</p></div>';
		}
		$content .= '<form action="' . $formaction . '" method="post" name="' . $type . '" id="' . $type . '">';
		$content .= '<fieldset><legend>' . $type . '</legend>';
		$content .= createTextInput('Name', 'qualilabel', 'name', 'qualiinput_medium', $this->name, true, $errorlist, $erroralttextlist);
		$content .= '</fieldset>';
		$content .= '  <input type="submit" name="save" id="save" value="save" />';
		$content .= '  <input type="submit" name="cancel" id="cancel" value="cancel" />';
		$content .= '</form>';
		return $content;
	}

	public function ValidateSelectorData() {
		return $this->ValidateModelSelect();
	}

	//type = DeviceCategory || TechPrerequisite || Bodyfunction
	public function CreateSelector($caption, $formaction, $successtext, $type) {
		$content = '<h1>' . $caption . '</h1>';
		if ($successtext) {
			$content .= '<div class="successMessage">' . $successtext . '</div>';
		}
		if ($errortext) {
			$content .= '<div class="errorMessage">' . $errortext . '</div>';
		}
		$content .= '<form action="' . $formaction . '" method="post" name="' . $type . '" id="' . $type . '">';
		$content .= $this->CreateModelSelect();
		$content .= '  <input type="submit" name="save" id="save" value="select" />';
		$content .= '</form>';
		return $content;
	}

	//////////////////////////////////////////////////
	// ------------------------- !!! -----------------------------------------
	//////////////////////////////////////////////////
	public function CreateSelectorTable($caption, $successtext, $errortext, $type, $table) {
		$query = "select ID, ". $type ." from ". $table ." order by ". $type;
		$result = mysql_query($query);

		$content = '';
		$content .= '<h1>' . $caption . '</h1>';
		if ($successtext) {
			$content .= '<div class="successMessage"><p>' . $successtext . '</p></div>';
		}
		if ($errortext) {
			$content .= '<div class="errorMessage"><p>' . $errortext . '</p></div>';
		}
		if (mysql_num_rows($result) == 0 ) {
			$content .= '<div class="warningMessage"><p>No data saved so far.</p></div>';
		} else {
			$content .= '<table summary="This table contains the "'. $table .'". "'. $table .'" can be updated by selecting update. "'. $table .'" can be deleted by selecting delete.">';
			$content .= '<caption>"'. $table .'"</caption>';
			$content .= '	<tr>';
			$content .= '		<th scope="col" class="id">ID</th>';
			$content .= '		<th scope="col" class="modname">name</th>';
			$content .= '		<th scope="col" class="update">Update</th>';
			$content .= '		<th scope="col" class="delete">Delete</th>';
			$content .= '	</tr>';
			$i = 1;
			while (list($id, $name) = mysql_fetch_row($result)) {
				$query1 = "select count(ID)
								from ". $table ." 
								where ID = " . $id . ";"; //from >>models<<?
				$result1 = mysql_query($query1);
				list($res1) = mysql_fetch_row($result1);

				$content .= ($i %2)? '<tr class="eventab">':'<tr class="oddtab">';
				$content .= '		<td class="id">' . $id .  '</td>';
				$content .= '		<td class="modname">' . $name .  '</td>';
				$content .= '		<td style="text-align: center;" class="image">';
				//if ($res1 == 0) {
					$content .= '			<a href="model.php?action=update&amp;state=edit&amp;id=' . $id . '" >';
					$content .= '			<img src="../images/update.ico" alt="model &quot;' . $name .'&quot; update" title="model &quot;' . $name .'&quot; update" class="DataIcons" /></a>';
				//}
				$content .=	'		</td>';
				$content .=	'		<td style="text-align: center;" class="image">';
				//if ($res1 == 0) {
					$content .= '		<a href="model.php?action=delete&amp;state=do&amp;id=' . $id . '" onclick="return confirm(\'model &quot;' . $name .'&quot; delete?\');">';
					$content .= '		<img src="../images/delete.png" alt="model &quot;' . $name .'&quot; delete" title="model &quot;' . $name .'&quot; delete" class="DataIcons" /></a>';
				//}
				$content .=	'		</td>';
				$content .= '	</tr>';
				$i++;
			}
			$content .= '</table>';
		}
		$content .= '<form action="'. $table .'".php?action=new" method="post" name="'. $table .'" id="'. $table .'">';
		$content .= '<input type="submit" value="add new '. $table .'"/>';
		$content .= '</form>';
		return $content;
	}

	public function ValidateDataSelect () {
		$error = false;
		if ($this->ID == '' || $this->ID == '0'){
			$error |= $this->err->CollectErrorsAltTexts('No data selected. Select data.', 'No data selected.', 'data_id');
			$error = true;
		}
		return $error;
	}
	
	//////////////////////////////////////////////////
	//----------------- !!! ------------------------
	//////////////////////////////////////////////////
	public function CreateDataSelect($type, $table) {
		$options = '';
		$errorlist = $this->err->CreateErrorList();
		$erroralttextlist = $this->err->CreateAltTextList();
		$query = "select ID, " . $type . " from ". $table . " order by " . $type;
		$result = mysql_query($query);
		if($this->ID == ''){
			$options .= '  <option value="0" selected="selected">Please select ...</option>';
		}else{
			$options .= '  <option value="0">Please select ...</option>';
		}
		while(list($id, $name) = mysql_fetch_row($result)) {
			if($this->ID == $id){
				$options .= '  <option value="' . $id . '" selected="selected">' . $name . '</option>';
			} else {
				$options .= '  <option value="' . $id . '">' . $name . '</option>';
			}
		}
		return createSelect("'". $type ."'", 'qualilabel', 'ID', 'qualiinput', $options, true, $errorlist, $erroralttextlist, '', false);
	}
	
	abstract protected function LoadDB();

	abstract protected function InsertDB();

	abstract protected function UpdateDB();

	abstract protected function DeleteDB($id);
}
?>