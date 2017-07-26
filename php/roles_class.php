<?php
require_once('errors.php');
require_once('connect.php');
require_once('helpfunc.php');
require_once('BaseClass_class.php');

class roles extends BaseClass
{
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
			$error |= $this->err->CollectErrorsAltTexts('No name entered. Please enter a name.', 'No name entered.', 'roleName');
		}
		return $error;
	}

	//type = DeviceCategory || TechPrerequisite || Bodyfunction || Role
	public function CreateForm($caption, $formaction, $successtext, $type="role") { 
		$errortext = $this->err->CreateErrorTextList();
		$erroralttextlist = $this->err->CreateAltTextList();
		$errorlist = $this->err->CreateErrorList();
		$content = '';
		$content .= '<h1>' . $caption . '</h1><br /><br />';
		if ($successtext) {
			$content .= '<div class="successMessage"><p>' . $successtext . '</p></div>';
		}
		if ($errortext) {
			$content .= '<div class="errorMessageData"><p>' . $errortext . '</p></div>';
		}
		$content .= '<form action="' . $formaction . '" method="post" name="' . $type . '" id="' . $type . '">';
		$content .= '<fieldset class="parentContainer"><legend>' . $type . '</legend>';
		$content .= createTextInput('Name', 'qualilabel', 'roleName', 'qualiinput_medium', $this->name, true, $errorlist, $erroralttextlist);
		$content .= '</fieldset><br />';
		$content .= '<div class="buttonContainer">';
		$content .= '  <input type="submit" name="save" id="save" value="save" class="formButtonSave"/>';
		$content .= '  <input type="submit" name="cancel" id="cancel" value="cancel" class="formButtonCancel" onclick="javascript:location.href=\'roles.php\'"/>';
		$content .= '</div>';
		$content .= '</form>';
		return $content;
	}

	public function ValidateSelectorData() {
		return $this->ValidateModelSelect();
	}

	//type = DeviceCategory || TechPrerequisite || Bodyfunction || Role
	public function CreateSelector($caption, $formaction, $successtext, $type="role") {
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
	public function CreateSelectorTable($caption, $successtext, $errortext, $type='role', $table='userroles') {		

		$query = "select ID, ". $type ." from ". $table ." order by ". $type;
		$result = mysql_query($query);

		$content = '';
		$content .= '<h1>' . $caption . '</h1>';
		if ($successtext) {
			$content .= '<div class="successMessage"><p><img src="../images/success.png" alt="Success" title="success" class="suc">' . $successtext . '</p></div>';
		}
		if ($errortext) {
			$content .= '<div class="errorMessage"><p>' . $errortext . '</p></div>';
		}
		if (mysql_num_rows($result) == 0 ) {
			$content .= '<div class="warningMessage"><p>No data saved so far.</p></div>';
		} else {
			$content .= '<table aria-described-by="p_roles">';
			$content .= '<p id="p_roles" hidden>This table contains all user roles. User roles can be updated and deleted by administrators. A user role can only be deleted if there are no users associated with it.</p>';
			$content .= '<caption>Userroles</caption>';
			$content .= '	<tr>';
			$content .= '		<th scope="col" class="id">ID</th>';
			$content .= '		<th scope="col" class="modname">Name</th>';
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
				if ($this->CheckDelete($id)) {
					$content .= '			<a href="roles.php?action=update&amp;state=edit&amp;id=' . $id . '" >';
					$content .= '			<img src="../images/update.ico" alt="Update role &quot;' . $name .'&quot;" title="Update role &quot;' . $name .'&quot;" class="DataIcons" /></a>';
				}else {
					$content .= '			<img src="../images/update.ico" alt="Role &quot;' . $name .'&quot; can not be updated" title="Role &quot;' . $name .'&quot; can not be updated" class="DataIconsGrey" /></a>';
				}
				//}
				$content .=	'		</td>';
				$content .=	'		<td style="text-align: center;" class="image">';
				if ($this->CheckDelete($id)) {
					$content .= '		<a href="roles.php?action=delete&amp;state=do&amp;id=' . $id . '" onclick="return confirm(\'Delete role &quot;' . $name .'&quot;?\');">';
					$content .= '		<img src="../images/delete.png" alt="Delete role &quot;' . $name .'&quot;" title="Delete role &quot;' . $name .'&quot;" class="DataIcons" /></a>';
				}else {
					$content .= '		<img src="../images/delete.png" alt="Role &quot;' . $name .'&quot; can not be deleted" title="Role &quot;' . $name .'&quot; can not be deleted" class="DataIconsGrey" />';
				}
				$content .=	'		</td>';
				$content .= '	</tr>';
				$i++;
			}
			$content .= '</table>';
		}
		$content .= '<form action="roles.php?action=new" method="post" name="'. $table .'" id="'. $table .'">';
		$content .= '<input type="submit" value="add new '. $table .'" class="button"/>';
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
	
	public function LoadDB(){
		$query = 'select ID, role from userroles where ID = ' . $this->ID . ';';
		$result = mysql_query($query);
		list($this->ID, $this->name) = mysql_fetch_row($result);
	}

	public function InsertDB(){
		$fail = false;
		$query = "insert into userroles(Role) values('" . mysql_real_escape_string($this->name) ."');";
		$result = mysql_query($query);
		$fail = $fail || mysql_errno() != 0;

		return !$fail;
	}

	public function UpdateDB(){
		$fail = false;
		$query = "update userroles set role= '" . mysql_real_escape_string($this->name) . "'  where ID = " . $this->ID . ";";
		$result = mysql_query($query);
		$fail = $fail || mysql_errno() != 0;

		return !$fail;
	}

	public function DeleteDB($id){
		$fail = false;
		$query = "delete from link_userlogin_roles where role_ID = " . $this->ID . ";";
		mysql_query($query);
		$query = "delete from userroles where id = " . $this->ID . ";";
		mysql_query($query);
		$result = mysql_query($query);
		$fail = $fail || mysql_errno() != 0;
		
		
		return !$fail;
	}
	
	public function CheckDelete($id){
		$fail = false;
		$query = "select count(role_ID) from link_userlogin_roles where role_ID = " . $id . ";";
		$result = mysql_query($query);
		while(list($count) = mysql_fetch_row($result)){
		if($count > 0)
			return false;
		else
			return true;
		} 
		return false;
	}
}
?>