<?php
require_once('errors.php');
require_once('connect.php');
require_once('helpfunc.php');

class Model {
	var $err;
	
	public $ID;
	public $name;
	public $modelDescription;
	public $filename;
	public $checkfile;
	public $approved = 0;
	public $old_file = null;
	public $old_tmp_name = null;
	
	function __construct(){
		$this->err = new Errors();
	}

	public function ValidateFormData() {
		$this->err = new Errors();
		$error = false;
		if ($this->name == '') {
			$error |= $this->err->CollectErrorsAltTexts('No name entered. Please enter a name.', 'No name entered.', 'name');
		}
		
		if ($this->modelDescription == '') {
			$error |= $this->err->CollectErrorsAltTexts('No model description entered. Please enter a model description.', 'No model description entered.', 'modelDescription');
		}
		
		if ($this->filename == '') {
			$error |= $this->err->CollectErrorsAltTexts('No filename entered. Please enter a filename.', 'No filename entered.', 'filename');
		}
		
		if (empty($_POST['devices'])){
			$error |= $this->err->CollectErrorsAltTexts('No tech-prerequisite selected. Please select at least one tech-prerequisite.', 'No tech-prerequisite selected.','tech');
		}
		
		if (empty($_POST['categories'])){
			$error |= $this->err->CollectErrorsAltTexts('No device category selected. Please select at least one device category.', 'No device category selected.','dev');
		}
		
		if (empty($_POST['functions'])){
			$error |= $this->err->CollectErrorsAltTexts('No bodyfunction selected. Please select at least one bodyfunction.', 'No bodyfunction selected.','body');
		}
		
		return $error;
	}
	
	public function ValidateFormDataUpdate() {
		$this->err = new Errors();
		$error = false;
		if ($this->name == '') {
			$error |= $this->err->CollectErrorsAltTexts('No name entered.', 'No name entered.', 'name');
		}
		
		if ($this->modelDescription == '') {
			$error |= $this->err->CollectErrorsAltTexts('No model description entered.', 'No model description entered.', 'modelDescription');
		}
		
		if (empty($_POST['devices'])){
			$error |= $this->err->CollectErrorsAltTexts('No tech-prerequisite selected. Please select at least one tech-prerequisite.', 'No tech-prerequisite selected.','tech');
		}
		
		if (empty($_POST['categories'])){
			$error |= $this->err->CollectErrorsAltTexts('No device category selected. Please select at least one device category.', 'No device category selected.','dev');
		}
		
		if (empty($_POST['functions'])){
			$error |= $this->err->CollectErrorsAltTexts('No bodyfunction selected. Please select at least one bodyfunction.', 'No bodyfunction selected.','body');
		}
		
		return $error;
	}

	public function CreateForm($caption, $formaction, $successtext) {
		$errortext = $this->err->CreateErrorTextList();
		$erroralttextlist = $this->err->CreateAltTextList();
		$errorlist = $this->err->CreateErrorList();
		$content = '<div class="formContent">';
		$content .= '<h1>' . $caption . '</h1>';
		$content .= '<br/><br/>';
		if ($successtext) {
			$content .= '<div class="successMessage"><p><img src="../images/success.png" alt="Success" title="success" class="suc">' . $successtext . '</p></div>';
		}
		if ($errortext) {
			$content .= '<div class="errorMessage"><p>' . $errortext . '</p></div>';
		}
		$content .= '<form action="' . $formaction . '" method="post" name="model" id="model" enctype="multipart/form-data">';
		$content .= '<fieldset class="parentContainer"><legend>Model</legend>';
		$content .= createTextInput('Name', 'qualilabel', 'name', 'qualiinput_medium', $this->name, true, $errorlist, $erroralttextlist);
		$content .= createTextArea('Model Description', 'qualilabel', 'modelDescription', 'qualiinput_medium', $this->modelDescription, true, $errorlist, $erroralttextlist, false);
		$content .= createFileInput('Filename', 'qualilabel', 'filename', 'qualiinput_medium', $this->filename, true, $errorlist, $erroralttextlist);
		
		$this->old_file = null;
		$this->old_tmp_name = null;
		
		if(!empty($_FILES['filename']['name']))
			$this->old_file = $_FILES['filename']['name'];
		else if(!empty($_POST['oldfile']))
			$this->old_file = $_POST['oldfile'];
		
		if(!empty($_FILES['filename']['tmp_name']))
			$this->old_tmp_name = $_FILES['filename']['tmp_name'];
		else if(!empty($_POST['tmp_name']))
			$this->old_tmp_name = $_POST['tmp_name'];		
		
		if($this->old_file != null){
			$content .= '<label id="oldfile_label" name="oldfile_label">currently selected: ' . $this->old_file . '</label>';
			$content .= '<input type="text" name="oldfile" id="oldfile" value="' . $this->old_file . '" hidden>';
		}
		
		if($this->old_tmp_name != null){
			$content .= '<input type="text" id="tmp_name" name="tmp_name" value="' . $this->old_tmp_name .'" hidden>';
		}
		
		if($_SESSION['admin']){
			if($this->approved)
				$content .= '<br><br /><input type="checkbox" name="approved" id="approved" value="1" checked/>approved';
			else
				$content .= '<br><br /><input type="checkbox" name="approved" id="approved" value="1"/>approved';
		}
		$content .= '<br /><br /></fieldset>';
		
		$content .= '<br/><br />';
		
		$content .= '<fieldset class="attributeContainer"><legend>Attributes</legend>';
		$max = 0;
		$query = "select count(ID) from techprerequisites";
		$result = mysql_query($query);
		while(list($count) = mysql_fetch_row($result))
			$max = $count;
		
		$query = "select ID, device from techprerequisites order by device asc";
		$result = mysql_query($query);
		
		$query1 = "select ID_techPrerequisite from link_model_techprerequisites where ID_modelTech = " . $this->ID;
		$result1 = mysql_query($query1);
		
		$content.='<br/><fieldset class="formContainer"><legend>Tech-Prerequisites</legend><div class="tech_row_container"><ul id="tech">';
		
		$tech_ids = array();
		
		if($result1){
			while(list($id_tech) = mysql_fetch_row($result1)){
				$tech_ids[$id_tech] = 1;
			}
		}
		
		$i = 1;
		$count = 0;
		$row = "row_1";
		while(list($id, $device) = mysql_fetch_row($result)){
			if(!empty($_POST['devices'])){
				if(in_array($id, $_POST['devices'])){
					$content .= '<li class="' . $row . '"><input type="checkbox" name="devices[]" value="' . $id . '" id="tech_' . $device . '" class="attrCbx" checked/><label class="attrCbx">'. $device .'</label></li>';
				}
				else{
					$content .= '<li class="' . $row . '"><input type="checkbox" name="devices[]" value="' . $id . '" id="tech_' . $device . '" class="attrCbx" /><label class="attrCbx">'. $device .'</label></li>';
				}
			}
			
			else{
				if(isset($tech_ids[$id])){
					$content .= '<li class="' . $row . '"><input type="checkbox" name="devices[]" value="' . $id . '" id="tech_' . $device . '" class="attrCbx" checked/><label class="attrCbx">'. $device .'</label></li>';
				}
				else{
					$content .= '<li class="' . $row . '"><input type="checkbox" name="devices[]" value="' . $id . '" id="tech_' . $device . '" class="attrCbx" /><label class="attrCbx">'. $device .'</label></li>';
				}
			}
			
			if($i % ($max/3) == 0){
				$count++;
				$row .= "1";
			}
			
			$i++;
		}
		
		$content.='</ul></div></fieldset>';
		
		$query = "select count(ID) from devicecategory";
		$result = mysql_query($query);
		while(list($count) = mysql_fetch_row($result))
			$max = $count;
		
		$query = "select ID, category from devicecategory order by category asc";
		$result = mysql_query($query);
		$content.='<br/><br/><fieldset class="formContainer"><legend>Device categories</legend><div class="tech_row_container"><ul id="dev">';
		
		$query1 = "select ID_DeviceCategory from link_model_devicecategory where ID_ModelDev = " . $this->ID;
		$result1 = mysql_query($query1);
		
		if($result1){
			while(list($dev_cat) = mysql_fetch_row($result1)){
				$dev_cats[$dev_cat] = 1;
			}
		}
		
		$i = 1;
		$count = 0;
		$row = "row_1";
		while(list($id, $category) = mysql_fetch_row($result)){
			if(!empty($_POST['categories'])){
				if(in_array($id, $_POST['categories']))
					$content .= '<li class="' . $row . '"><input type="checkbox" name="categories[]" value="' . $id . '" id="dev_' . $category . '" class="attrCbx" checked/><label class="attrCbx">'. $category .'</label></li>';
				else
					$content .= '<li class="' . $row . '"><input type="checkbox" name="categories[]" value="' . $id . '" id="dev_' . $category . '" class="attrCbx" /><label class="attrCbx">'. $category .'</label></li>';
			
			}
			else{
				if(isset($dev_cats[$id]))
					$content .= '<li class="' . $row . '"><input type="checkbox" name="categories[]" value="' . $id . '" id="dev_' . $category . '" class="attrCbx" checked/><label class="attrCbx">'. $category .'</label></li>';
				else
					$content .= '<li class="' . $row . '"><input type="checkbox" name="categories[]" value="' . $id . '" id="dev_' . $category . '" class="attrCbx" /><label class="attrCbx">'. $category .'</label></li>';
			}
			
			if($i % ($max/3) == 0){
				$count++;
				$row .= "1";
			}
			
			$i++;
		}
		
		$content.='</ul></div></fieldset>';
		
		$query = "select count(ID) from bodyfunctions";
		$result = mysql_query($query);
		while(list($count) = mysql_fetch_row($result))
			$max = $count;
		
		$query = "select ID, function from bodyfunctions order by function asc";
		$result = mysql_query($query);
		$content.='<br/><br/><fieldset class="formContainer"><legend>Bodyfunctions</legend><div class="tech_row_container"><ul id="body">';
		
		$query1 = "select ID_bodyFunction from link_model_bodyfunction where ID_modelBody = " . $this->ID;
		$result1 = mysql_query($query1);
			
		if($result1){
			while(list($body_func) = mysql_fetch_row($result1)){
				$body_funcs[$body_func] = 1;
			}
		}
		
		$i = 1;
		$count = 0;
		$row = "row_1";
		while(list($id, $function) = mysql_fetch_row($result)){
			if(!empty($_POST['functions'])){
				if(in_array($id, $_POST['functions']))
					$content .= '<li class="'. $row . '"><input type="checkbox" name="functions[]" value="' . $id . '" id="body_' . $function . '" class="attrCbx" checked/><label class="attrCbx">'. $function . '</label></li>';
				else
					$content .= '<li class="'. $row . '"><input type="checkbox" name="functions[]" value="' . $id . '" id="body_' . $function . '" class="attrCbx" /><label class="attrCbx">'. $function . '</label></li>';						
			}
			
			else{
				if(isset($body_funcs[$id]))
					$content .= '<li class="'. $row . '"><input type="checkbox" name="functions[]" value="' . $id . '" id="body_' . $function . '" class="attrCbx" checked/><label class="attrCbx">'. $function . '</label></li>';
				else
					$content .= '<li class="'. $row . '"><input type="checkbox" name="functions[]" value="' . $id . '" id="body_' . $function . '" class="attrCbx" /><label class="attrCbx">'. $function . '</label></li>';			
			}
			
			if($i % ($max/3) == 0){
				$count++;
				$row .= "1";
			}
			
			$i++;
		}
		$content.='</ul></div></fieldset><br/>';
		$content .= '</fieldset><br/>';
		$content .= '<div class="buttonContainer">';
		$content .= '  <input type="submit" name="save" id="save" value="save" class="formButtonSave"/>';
		$content .= '  <input type="submit" name="cancel" id="cancel" value="cancel" class="formButtonCancel" onclick="javascript:location.href=\'model.php\'"/>';
		$content .= '</div>';
		$content .= '</form>';
		$content .= '</div>';
		return $content;
	}

	public function ValidateSelectorData() {
		return $this->ValidateModelSelect();
	}

	public function CreateSelector($caption, $formaction, $successtext) {
		$content = '<h1>' . $caption . '</h1>';
		if ($successtext) {
			$content .= '<div class="successMessage">' . $successtext . '</div>';
		}
		if ($errortext) {
			$content .= '<div class="errorMessage">' . $errortext . '</div>';
		}
		$content .= '<form action="' . $formaction . '" method="post" name="model" id="model">';
		$content .= $this->CreateModelSelect();
		$content .= '  <input type="submit" name="save" id="save" value="select" />';
		$content .= '</form>';
		return $content;
	}

	public function CreateSelectorTable($caption, $successtext, $errortext) {
		$admin = $_SESSION['admin'];
		
		$query = "select models.ID, name, modelDescription, filename, ID_UserModel from models inner join link_user_models on models.ID = ID_ModelUser order by name";
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
			$content .= '<div class="warningMessage"><p>No models saved so far.</p></div>';
		} else {
			$content .= '<table aria-described-by="p_models">';
			$content .= '<p id="p_models" hidden>This table contains all models. Models can be updated and deleted by administrators.</p>';
			$content .= '<caption>Models</caption>';
			$content .= '	<tr>';
			$content .= '		<th scope="col" class="id">ID</th>';
			$content .= '		<th scope="col" class="modname">name</th>';
			$content .= '		<th scope="col" class="modname">Model description</th>';
			$content .= '		<th scope="col" class="modname">Filename</th>';
			$content .= ' 		<th scope="col" class="modname">User</th>';
			if($admin){
				$content .= '		<th scope="col" class="update">Update</th>';
				$content .= '		<th scope="col" class="delete">Delete</th>';
			}
			$content .= '		<th scope="col" class="approved">Approved</th>';
			$content .= '	</tr>';
			$i = 1;
			while (list($id, $name, $modelDescription, $filename, $userid) = mysql_fetch_row($result)) {
				$query1 = "select Username
								from userlogin
								where UserID = " . $userid . ";"; //from >>models<<?
				$result1 = mysql_query($query1);
				$moduser = mysql_fetch_row($result1);
					
				
				//fetches approved status (0 or 1)
				$queryApp = "select approved from models where ID = " . $id . ";";
				$resultApp = mysql_query($queryApp);
				list($resApp) = mysql_fetch_row($resultApp);
				
				$content .= ($i %2)? '<tr class="eventab">':'<tr class="oddtab">';
				$content .= '		<td class="id">' . $id .  '</td>';
				$content .= '		<td class="modname">' . $name .  '</td>';
				$content .= '		<td class="modname">' . $modelDescription .  '</td>';
				$content .= '		<td class="modname">' . $filename .  '</td>';
				$content .= '		<td class="modname">' . $moduser[0] . '</td>';
				
				if ($admin == 1) {
					$content .= '		<td style="text-align: center;">';
					$content .= '			<a href="model.php?action=update&amp;state=edit&amp;id=' . $id . '" >';
					$content .= '			<img src="../images/update.ico" alt="Update model &quot;' . $name .'&quot;" title="Update model &quot;' . $name .'&quot;" class="Icons" /></a>';
					$content .=	'		</td>';
				}
					
				if ($admin == 1) {
					$content .=	'		<td style="text-align: center;" class="image">';
					$content .= '		<a href="model.php?action=delete&amp;state=do&amp;id=' . $id . '" onclick="return confirm(\'Delete model &quot;' . $name .'&quot;?\');">';
					$content .= '		<img src="../images/delete.png" alt="Delete model &quot;' . $name .'&quot;" title="delete model &quot;' . $name .'&quot;" class="Icons" /></a>';
					$content .=	'		</td>';
				}
				$content .= '		<td class="approved">';
				if($resApp == 0){
					$content .= '			<img src="../images/notapproved.png" alt="model &quot;' . $name .'&quot; is not approved" title="model &quot;' . $name .'&quot; is not approved" class="Icons" />';
				}
				if($resApp == 1){
					$content .= '			<img src="../images/approved.png" alt="model &quot;' . $name .'&quot; is approved" title="model &quot;' . $name .'&quot; is approved" class="Icons" />';
				}
				$content .=	'		</td>';	
				$content .= '	</tr>';
				$i++;
			}
			$content .= '</table>';
		}
		$content .= '<form action="model.php?action=new" method="post" name="model" id="model">';
		$content .= '<input type="submit" value="add new model" class="button"/>';
		$content .= '</form>';
		return $content;
	}
	
	public function CreateMySelectorTable($caption, $successtext, $errortext) {
		$query = "select UserID from userlogin where ID = " . $_SESSION['ID'];
		$result = mysql_query($query);
		$uid = mysql_fetch_array($result);
		$uid = $uid[0];
		
		$query = "select models.ID, name, modelDescription, filename, ID_UserModel from models inner join link_user_models on models.ID = ID_ModelUser where ID_UserModel = " . $uid . " order by name";
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
			$content .= '<div class="warningMessage"><p>No models saved so far.</p></div>';
		} else {
			$content .= '<table acia-described-by="p_myModels">';			
			$content .= '<p id="p_myModels" hidden>This table contains the models you have uploaded. Your models can be updated and deleted here.</p>';
			$content .= '<caption>My Models</caption>';
			$content .= '	<tr>';
			$content .= '		<th scope="col" class="id">ID</th>';
			$content .= '		<th scope="col" class="modname">Name</th>';
			$content .= '		<th scope="col" class="modname">Model Description</th>';
			$content .= '		<th scope="col" class="modname">Filename</th>';
			$content .= ' 		<th scope="col" class="modname">User</th>';
			$content .= '		<th scope="col" class="update">Update</th>';
			$content .= '		<th scope="col" class="delete">Delete</th>';
			$content .= '		<th scope="col" class="approved">Approved</th>';
			$content .= '	</tr>';
			$i = 1;
			while (list($id, $name, $modelDescription, $filename, $userid) = mysql_fetch_row($result)) {
				$query1 = "select Username
								from userlogin
								where UserID = " . $userid . ";"; //from >>models<<?
				$result1 = mysql_query($query1);
				$moduser = mysql_fetch_row($result1);
					
				
				//fetches approved status (0 or 1)
				$queryApp = "select approved from models where ID = " . $id . ";";
				$resultApp = mysql_query($queryApp);
				list($resApp) = mysql_fetch_row($resultApp);
				
				$content .= ($i %2)? '<tr class="eventab">':'<tr class="oddtab">';
				$content .= '		<td class="id">' . $id .  '</td>';
				$content .= '		<td class="modname">' . $name .  '</td>';
				$content .= '		<td class="modname">' . $modelDescription .  '</td>';
				$content .= '		<td class="modname">' . $filename .  '</td>';
				$content .= '		<td class="modname">' . $moduser[0] . '</td>';
				$content .= '		<td style="text-align: center;">';
				$content .= '		<a href="mymodels.php?action=update&amp;state=edit&amp;id=' . $id . '" >';
				$content .= '		<img src="../images/update.ico" alt="Update model &quot;' . $name .'&quot;" title="Update model &quot;' . $name .'&quot;" class="Icons" /></a>';
				$content .=	'		</td>';
				$content .=	'		<td style="text-align: center;" class="image">';
				$content .= '		<a href="mymodels.php?action=delete&amp;state=do&amp;id=' . $id . '" onclick="return confirm(\'Delete model &quot;' . $name .'&quot;?\');">';
				$content .= '		<img src="../images/delete.png" alt="Delete model &quot;' . $name .'&quot;" title="Delete model &quot;' . $name .'&quot;" class="Icons" /></a>';
				$content .=	'		</td>';
				$content .= '		<td class="approved">';
				if($resApp == 0){
					$content .= '			<img src="../images/notapproved.png" alt="model &quot;' . $name .'&quot; is not approved" title="model &quot;' . $name .'&quot; is not approved" class="Icons" />';
				}
				if($resApp == 1){
					$content .= '			<img src="../images/approved.png" alt="model &quot;' . $name .'&quot; is approved" title="model &quot;' . $name .'&quot; is approved" class="Icons" />';
				}
				$content .=	'		</td>';	
				$content .= '	</tr>';
				$i++;
			}
			$content .= '</table>';
		}
		$content .= '<form action="mymodels.php?action=new" method="post" name="model" id="model">';
		$content .= '<input type="submit" value="add new model" class="button"/>';
		$content .= '</form>';
		return $content;
	}

	public function ValidateModelSelect () {
		$error = false;
		if ($this->ID == '' || $this->ID == '0'){
			$error |= $this->err->CollectErrorsAltTexts('No model selected. Select a model.', 'No Model selected.', 'model_id');
			$error = true;
		}
		return $error;
	}
	
	public function CreateModelSelect() {
		$options = '';
		$errorlist = $this->err->CreateErrorList();
		$erroralttextlist = $this->err->CreateAltTextList();
		$query = "select ID, name from Models order by name";
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
		return createSelect('model', 'qualilabel', 'ID', 'qualiinput', $options, true, $errorlist, $erroralttextlist, '', false);
	}

	public function LoadDB() {
		$query = 'select ID, name, modelDescription, filename, approved from Models where ID = ' . $this->ID . ';';
		$result = mysql_query($query);
		list($this->ID, $this->name, $this->modelDescription, $this->filename, $this->approved) = mysql_fetch_row($result);
	}

	public function InsertDB() {
		$fail = false;
		$query = "insert into models(name, modelDescription, filename, approved) values('" . mysql_real_escape_string($this->name) ."', '". mysql_real_escape_string($this->modelDescription) ."', '". mysql_real_escape_string($this->filename) ."', " . $this->approved . ");";
		$result = mysql_query($query);
		$fail = $fail || mysql_errno() != 0;
		
		$mod_id= '';
		$query = "select id from models order by id desc limit 1";
		$result = mysql_query($query);
		$mod_id = mysql_fetch_array($result);
		$this->ID = $mod_id[0];
		
		$user_id = '';
		$query = "select UserID from userlogin where ID = " . $_SESSION['ID'];
		$result = mysql_query($query);
		$user_id = mysql_fetch_array($result);
		$user_id = $user_id[0];
		
		$query = "insert into link_user_models(ID_UserModel, ID_ModelUser) values(". $user_id .", " . $this->ID .");";
		$result = mysql_query($query);	
		
		return !$fail;
	}

	public function UpdateDB() {
		$fail = false;
		
		$query = "delete from link_model_bodyfunction where ID_modelBody = " . $this->ID . ";";
		echo mysql_query($query);
		$query = "delete from link_model_devicecategory where ID_ModelDev = " . $this->ID . ";";
		echo mysql_query($query);
		$query = "delete from link_model_techprerequisites where ID_modelTech = " . $this->ID . ";";
		echo mysql_query($query);
		
		$query = "update models set name = '" . mysql_real_escape_string($this->name) . "', modelDescription = '" . mysql_real_escape_string($this->modelDescription) ."'";
		if($this->filename != '')
			$query .= ", filename = '" . mysql_real_escape_string($this->filename) . "'";
		$query .= " where ID = " . $this->ID . ";";
		$query2 = "update models set approved = 0 where ID = " . $this->ID . ";";
		if($_SESSION['admin']){
			if(isset($this->approved))
				$query2 = "update models set approved = '" . $this->approved . "' where ID = " . $this->ID . ";";
		}
		$result = mysql_query($query);
		$result = mysql_query($query2);
		$fail = $fail || mysql_errno() != 0;

		return !$fail;
	}

	public function DeleteDB() {
		$fail = false;
		$query = "delete from link_model_bodyfunction where ID_modelBody = " . $this->ID . ";";
		echo mysql_query($query);
		$query = "delete from link_model_devicecategory where ID_ModelDev = " . $this->ID . ";";
		echo mysql_query($query);
		$query = "delete from link_model_techprerequisites where ID_modelTech = " . $this->ID . ";";
		echo mysql_query($query);
		$query = "delete from link_user_models where ID_ModelUser = " . $this->ID . ";";
		echo mysql_query($query);
		$query = "delete from Models where id = " . $this->ID . ";";
		echo mysql_query($query);
		$result = mysql_query($query);
		$fail = $fail || mysql_errno() != 0;
		
		
		return !$fail;
	}
	
	//File upload
	public function ModelUpload($tmp_file) {
		
		//get latest added ID
		/*
		$file_id= '';
		$query = "select id from models order by id desc limit 1";
		$result = mysql_query($query);
		$file_id = mysql_fetch_array($result);
		$id = $file_id[0];
		*/
		
		$target_dir = "../models/";
		$target_file = $this->filename;
		$err = 1;
		$errMsg = '<div class="errorMessage"><p>';
		$succMsg = '<div class="successMessage"><p>Model "'. basename($target_file) . '" was successfully uploaded</p></div>';
		$modFileType = pathinfo($target_file,PATHINFO_EXTENSION);
		
		//Check if directory exists
		/*
		if(!file_exists($target_dir)){
				mkdir($target_dir);
		}*/
		
		//Check if file exists
		/* 
		if(file_exists($upload_file)){
			if(!unlink($target_dir . basename($upload_file))){		
				$err = 0;
			}
		}*/
		
		//Check if file is .acs
		if($modFileType != "acs"){
			$errMsg += 'Model "'. basename($target_file) . '" file extension not supported.';
			$err = 0;
		} else {
			//$upload_file = $target_dir .'M' . $id . '.acs';
			$upload_file = $target_dir . $this->filename;
		}
		
		//Check if Errors occurred
		if(!$err){
			$errMsg += '</div></p>';
			return $err;
		}
		
		//file upload
		else{
			if(move_uploaded_file($tmp_file, $upload_file))
				return $err;
			else{
				$errMsg += 'Model "'. basename($upload_file) . '" could not be uploaded</div></p>';
				$err = 0;
				return $err;
			}
		}
			
	}
	
	public function ModelRename(){
		if(rename("../models/".$this->filename, "../models/M".$this->ID.".acs"))
			return true;
		else
			return false;
	}
	
	public function ModelUpdate($tmp_file)
	{
		$target_dir = "../models/M" . $this->ID . "/";
		$target_file = $this->filename;
		$err = 1;
		$errMsg = '<div class="errorMessage"><p>';
		$succMsg = '<div class="successMessage"><p>Model "'. basename($target_file) . '" was successfully uploaded</p></div>';
		$modFileType = pathinfo($target_file,PATHINFO_EXTENSION);
		
		//Check if directory exists
		if(!file_exists($target_dir)){
				mkdir($target_dir);
		}
		
		//Check if file is .acs
		if($modFileType != "acs"){
			$errMsg += 'Model "'. basename($target_file) . '" file extension not supported.';
			return $err = 0;
		} else {
			$upload_file = $target_dir .'M' . $this->ID . '.acs';
		}
		
		//Delete previous model file
		if(file_exists($upload_file)){
			if(!unlink($upload_file)){		
				$err = 1;
			}
		}
		
		//Check if Errors occurred
		if(!$err){
			$errMsg += '</div></p>';
			return $err;
		}
		
		//file upload
		else{
				if(move_uploaded_file($tmp_file, $upload_file))
					return $err;
				else{
					$errMsg += 'Model "'. basename($upload_file) . '" could not be uploaded</div></p>';
					$err = 0;
					return $err;
				}
		}	
	}
	
	//File Remove
	public function RemoveModel()
	{
		$target_dir = "../models/";
		$target_file = "M".$this->ID.".acs";
		$err=1;
		
		if(unlink($target_dir . basename($target_file)))
			return $err;
		
		else
			return $err = 0;
	}
	
	public function ModelLinkTech($link)
	{
		$fail = false;
		
		$query = "insert into link_model_techprerequisites(ID_modelTech, ID_techPrerequisite) values(". $this->ID .", ". $link . ");";
		$result = mysql_query($query);
		
		$fail = $fail || mysql_errno() != 0;

		return !$fail;
	}
	
	public function ModelLinkDev($link)
	{
		$fail = false;

		$query = "insert into link_model_devicecategory(ID_modelDev, ID_DeviceCategory) values(". $this->ID .", ". $link . ");";
		$result = mysql_query($query);
		
		$fail = $fail || mysql_errno() != 0;

		return !$fail;
	}
	
	public function ModelLinkBody($link)
	{
		$fail = false;
		
		$query = "insert into link_model_bodyfunction(ID_modelBody, ID_bodyFunction) values(". $this->ID .", ". $link . ");";
		$result = mysql_query($query);
		
		$fail = $fail || mysql_errno() != 0;

		return !$fail;
	}
}
?>
