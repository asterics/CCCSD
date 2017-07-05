<?php
require_once('errors.php');
require_once('connect.php');
require_once('helpfunc.php');

class User {
	var $err;
	
	public $ID;
	public $loginID;
	public $username;
	public $password;
	public $vPassword;
	public $firstName;
	public $lastName;
	public $email;
	public $roles = array();
	public $admin;
	
	function __construct(){
		$this->err = new Errors();
	}

	public function ValidateFormData() {
		$this->err = new Errors();
		$error = false;
		if ($this->firstName == '') {
			$error |= $this->err->CollectErrorsAltTexts('No first name entered. Please enter a first name.', 'No first name entered.', 'firstName');
		}
		
		if ($this->lastName == '') {
			$error |= $this->err->CollectErrorsAltTexts('No last name entered. Please enter a last name', 'No last name entered.', 'lastName');
		}
		
		if ($this->email == '') {
			$error |= $this->err->CollectErrorsAltTexts('No email entered. Please enter an email address.', 'No email entered.', 'email');
		}
		
		if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
			$error |= $this->err->CollectErrorsAltTexts('Entered email is of invalid format. Please reenter your email address.', 'Invalid email format.', 'email');	
		}
		
		if ($this->username == '') {
			$error |= $this->err->CollectErrorsAltTexts('No username entered. Please enter a username', 'No email entered.', 'username');
		}
		
		$query1 = 'select * from Userlogin where Username = "' . $this->username . '";';
		$result1 = mysql_query($query1);
		while(list($username) = mysql_fetch_row($result1))
		{
			if(isset($username)){
				$error |= $this->err->CollectErrorsAltTexts('Username is already taken. Please choose a different username.', 'Username taken.', 'unameTaken');
			}
		}	
		
		if ($this->password == ''){
			$error |= $this->err->CollectErrorsAltTexts('No password entered. Please enter a password.', 'No password entered.', 'password');
		}
		
		if ($this->password != $this->vPassword){
			$error |= $this->err->CollectErrorsAltTexts('Passwords do not match. Please reenter your passwords.', 'Passwords do not match.', 'password');
		}
		
		if(empty($_POST['Roles'])){
			$error |= $this->err->CollectErrorsAltTexts('No user role selected. Please select at least one of the available user roles.', 'No user role selected.', 'roles');
		}
		
		return $error;
	}
	
	public function ValidateUpdateFormData(){
		$this->err = new Errors();
		$error = false;
		if ($this->firstName == '') {
			$error |= $this->err->CollectErrorsAltTexts('No first name entered. Please enter a first name.', 'No first name entered.', 'firstName');
		}
		
		if ($this->lastName == '') {
			$error |= $this->err->CollectErrorsAltTexts('No last name entered. Please enter a last name', 'No last name entered.', 'lastName');
		}
		
		if ($this->email == '') {
			$error |= $this->err->CollectErrorsAltTexts('No email entered. Please enter an email address.', 'No email entered.', 'email');
		}
		
		if ($this->password == '') {
			$error |= $this->err->CollectErrorsAltTexts('No password entered. Please enter a password.', 'No password entered.', 'password');
		}		
		
		if ($this->password != $this->vPassword){
			$error |= $this->err->CollectErrorsAltTexts('Passwords do not match. Please reenter your passwords', 'Passwords do not match.', 'vpassword');
		}
		
		if(empty($_POST['Roles'])){
			$error |= $this->err->CollectErrorsAltTexts('No user role selected. Please select at least one of the available user roles.', 'No user role selected.', 'roles');
		}
		
		return $error;
	}

	public function CreateForm($caption, $formaction, $successtext) {
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
		$content .= '<form action="' . $formaction . '" method="post" name="user" ID="user" enctype="multipart/form-data">';
		$content .= '<fieldset class="userContainer"><legend>user</legend>';
		$content .= '<div class="userFormContainer">';
		$content .= createTextInput('First name', 'qualilabel', 'firstName', 'qualiinput_medium', $this->firstName, true, $errorlist, $erroralttextlist) . '<br />';
		$content .= createTextInput('Last name', 'qualilabel', 'lastName', 'qualiinput_medium', $this->lastName, true, $errorlist, $erroralttextlist) . '<br />';
		$content .= createTextInput('Email', 'qualilabel', 'email', 'qualiinput_medium', $this->email, true, $errorlist, $erroralttextlist) . '<br />';
		$content .= createTextInput('Username', 'qualilabel', 'username', 'qualiinput_medium', $this->username, true, $errorlist, $erroralttextlist) . '<br />';
		$content .= createPassword('Password', 'Verify Password', 'qualilabel', 'password', 'vpassword', 'qualiinput_medium', true, $errorlist, $erroralttextlist) . '<br />';
		$content .= '</div>';
		
		$content .= '<fieldset class="formContainer"><legend>Roles</legend><div class="roles">';
		
		if($_SESSION['admin'] == 1){
			$queryRoles = "select Role, ID from userroles";
			$resultRoles = mysql_query($queryRoles);
			
			$queryRoleLink = "select role_ID from link_userlogin_roles where userlogin_ID =" . $this->loginID;
			$resultRoleLink = mysql_query($queryRoleLink);
			
			$roleIDs = array();
			
			if($resultRoleLink){
				while(list($roleID) = mysql_fetch_row($resultRoleLink)){
					$roleIDs[$roleID] = 1;
				}
					
			}
			
			while(list($rName, $rID) = mysql_fetch_row($resultRoles)){
				if(!empty($_POST['Roles'])){
					if(in_array($rID, $_POST['Roles']))
						$content .= '<input type="checkbox" name="Roles[]" ID="' . $rName . '" value="' . $rID . '" checked/>' . $rName . '<br>';
					else
						$content .= '<input type="checkbox" name="Roles[]" ID="' . $rName . '" value="' . $rID . '" />' . $rName . '<br>';
				}
				
				else{
					if(isset($roleIDs[$rID])){
						$content .= '<input type="checkbox" name="Roles[]" ID="' . $rName . '" value="' . $rID . '" checked/>' . $rName . '<br>';
					}
					
					else{
						$content .= '<input type="checkbox" name="Roles[]" ID="' . $rName . '" value="' . $rID . '" />' . $rName . '<br>';
					}
				}
			}
			
		}
		
		$content .= '</div></fieldset>';
		
		$content .= '</fieldset>';
		$content .= '<div class="buttonContainer">';
		$content .= '  <input type="submit" name="save" id="save" value="save" class="formButtonSave"/>';
		$content .= '  <input type="submit" name="cancel" id="cancel" value="cancel" class="formButtonCancel" onclick="javascript:location.href=\'Users.php\'"/>';
		$content .= '</div>';
		$content .= '</form>';
		return $content;
	}
	
	public function CreateUpdateForm($caption, $formaction, $successtext) {
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
		$content .= '<form action="' . $formaction . '" method="post" name="user" ID="user" enctype="multipart/form-data">';
		$content .= '<fieldset class="userContainer"><legend>user</legend>';
		$content .= '<div class="userFormContainer">';
		$content .= createTextInput('First name', 'qualilabel', 'firstName', 'qualiinput_medium', $this->firstName, true, $errorlist, $erroralttextlist) . '<br />';
		$content .= createTextInput('Last name', 'qualilabel', 'lastName', 'qualiinput_medium', $this->lastName, true, $errorlist, $erroralttextlist) . '<br />';
		$content .= createTextInput('Email', 'qualilabel', 'email', 'qualiinput_medium', $this->email, true, $errorlist, $erroralttextlist) . '<br />';
		$content .= createPassword('Password', 'Verify Password', 'qualilabel', 'password', 'vpassword', 'qualiinput_medium', true, $errorlist, $erroralttextlist) . '<br />';
		$content .= '</div>';

		$content .= '<fieldset class="formContainer"><legend>Roles</legend><div class="roles">';
		if($_SESSION['admin'] == 1){
			$queryRoles = "select Role, ID from userroles";
			$resultRoles = mysql_query($queryRoles);
			
			$queryRoleLink = "select role_ID from link_userlogin_roles where userlogin_ID =" . $this->loginID;
			$resultRoleLink = mysql_query($queryRoleLink);
			
			$roleIDs = array();
			
			if($resultRoleLink){
				while(list($roleID) = mysql_fetch_row($resultRoleLink)){
					$roleIDs[$roleID] = 1;
				}
					
			}
			
			while(list($rName, $rID) = mysql_fetch_row($resultRoles)){
				if(!empty($_POST['Roles'])){
					if(in_array($rID, $_POST['Roles']))
						$content .= '<input type="checkbox" name="Roles[]" ID="' . $rName . '" value="' . $rID . '" checked/>' . $rName . '<br>';
					else
						$content .= '<input type="checkbox" name="Roles[]" ID="' . $rName . '" value="' . $rID . '" />' . $rName . '<br>';
				}
				
				else{
					if(isset($roleIDs[$rID])){
						$content .= '<input type="checkbox" name="Roles[]" ID="' . $rName . '" value="' . $rID . '" checked/>' . $rName . '<br>';
					}
					
					else{
						$content .= '<input type="checkbox" name="Roles[]" ID="' . $rName . '" value="' . $rID . '" />' . $rName . '<br>';
					}
				}
			}
			
		}
		$content .= '</fieldset>';
		
		$content .= '</fieldset>';
		$content .= '<div class="buttonContainer">';
		$content .= '  <input type="submit" name="save" id="save" value="save" class="formButtonSave"/>';
		$content .= '  <input type="submit" name="cancel" id="cancel" value="cancel" class="formButtonCancel" onclick="javascript:location.href=\'Users.php\'"/>';
		$content .= '</div>';
		$content .= '</form>';
		return $content;
	}

	public function ValidateSelectorData() {
		return $this->ValidateuserSelect();
	}

	public function CreateSelector($caption, $formaction, $successtext) {
		$content = '<h1>' . $caption . '</h1>';
		if ($successtext) {
			$content .= '<div class="successMessage">' . $successtext . '</div>';
		}
		if ($errortext) {
			$content .= '<div class="errorMessage">' . $errortext . '</div>';
		}
		$content .= '<form action="' . $formaction . '" method="post" name="user" ID="user">';
		$content .= $this->CreateuserSelect();
		$content .= '  <input type="submit" name="save" ID="save" value="select" />';
		$content .= '</form>';
		return $content;
	}

	public function CreateSelectorTable($caption, $successtext, $errortext) {		
		
		$query = "select userlogin.ID, UserID, Username, FirstName, LastName, Email from userlogin inner join users on userlogin.userID = users.ID";
		if($_SESSION['admin'] != 1)
			$query .=  " where userlogin.ID = " . $_SESSION['ID'];
		else{
			$query .= " where active = 1"; 
		$query .= " order by Username asc";
		}
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
			$content .= '<div class="warningMessage"><p>No users saved so far.</p></div>';
		} else {
			$content .= '<table aria-described-by="p_users">';
			$content .= '<p id="p_users" hidden>This table contains all users. For regular users, only their own entry is visible. Users can be updated and deleted by administrators. A user can update and delete his own entry.</p>';
			$content .= '<caption>Users</caption>';
			$content .= '	<tr>';
			$content .= '		<th scope="col" class="id">ID</th>';
			$content .= '		<th scope="col" class="username">Username</th>';
			$content .= '		<th scope="col" class="username">Role(s)</th>';
			$content .= '		<th scope="col" class="username">First Name</th>';
			$content .= '		<th scope="col" class="username">Last Name</th>';
			$content .= '		<th scope="col" class="username">Email</th>';
			$content .= '		<th scope="col" class="update">Update</th>';
			$content .= '		<th scope="col" class="delete">Delete</th>';
			$content .= '	</tr>';
			$i = 1;
			while (list($id, $uid, $Username, $FirstName, $LastName, $Email) = mysql_fetch_row($result)) {
				$query1 = "select count(UserID)
								from userlogin 
								where UserID = " . $uid . ";";
				$result1 = mysql_query($query1);
				list($res1) = mysql_fetch_row($result1);
				
				$content .= ($i %2)? '<tr class="eventab">':'<tr class="oddtab">';
				$content .= '		<td class="id">' . $uid .  '</td>';
				$content .= '		<td class="username">' . $Username .  '</td>';
				$content .= '		<td class="username">';
				
				//fetches roles
				$queryRoles = "select Role, ID from userroles";
				$resultRoles = mysql_query($queryRoles);
				
				$queryRoleLink = "select role_ID from link_userlogin_roles where userlogin_ID =" . $id;
				$resultRoleLink = mysql_query($queryRoleLink);
				
				$roleIDs = array();
				
				if($resultRoleLink){
					while(list($roleID) = mysql_fetch_row($resultRoleLink)){
						$roleIDs[$roleID] = 1;
					}
						
				}
			
				while(list($rName, $rID) = mysql_fetch_row($resultRoles)){
					if(isset($roleIDs[$rID])){
						$content .= $rName . ' ';
					}
				}
				
				$content .= '</td>';
				$content .= '		<td class="username">' . $FirstName .  '</td>';
				$content .= '		<td class="username">' . $LastName .  '</td>';
				$content .= '		<td class="username">' . $Email .  '</td>';
				$content .= '		<td style="text-align: center;" class="image">';
				//if ($res1 == 0) {
					$content .= '			<a href="users.php?action=update&amp;state=edit&amp;ID=' . $id . '" >';
					$content .= '			<img src="../images/update.ico" alt="user &quot;' . $Username .'&quot; update" title="user &quot;' . $Username .'&quot; update" class="UserIcons" /></a>';
				//}
				$content .=	'		</td>';
				$content .=	'		<td style="text-align: center;" class="image">';
				if ($_SESSION['ID'] == $id) {
					$content .= '		<a href="users.php?action=delete&amp;state=do&amp;LoginID=' . $id . '&amp;ID=' . $uid . '" onclick="return confirm(\'With this action, your account will be removed from the website, and you will be logged out, continue?\');">';
					$content .= '		<img src="../images/delete.png" alt="Delete user &quot;' . $Username .'&quot;" title="Delete user &quot;' . $Username .'&quot;" class="UserIcons" /></a>';
				} else{
					$content .= '		<a href="users.php?action=delete&amp;state=do&amp;LoginID=' . $id . '&amp;ID=' . $uid . '" onclick="return confirm(\'Delete user &quot;' . $Username . '&quot;?\');">';
					$content .= '		<img src="../images/delete.png" alt="Delete user &quot;' . $Username .'&quot;" title="Delete user &quot;' . $Username .'&quot;" class="UserIcons" /></a>';
				}
				$content .=	'		</td>';
				$content .= '	</tr>';
				$i++;
			}
			$content .= '</table>';
		}
		if($_SESSION['admin'] == 1){
			$content .= '<form action="users.php?action=new" method="post" name="user" ID="user">';
			$content .= '<input type="submit" value="add new user" class="button"/>';
			$content .= '</form>';
		}
		return $content;
	}

	public function ValidateUserSelect () {
		$error = false;
		if ($this->ID == '' || $this->ID == '0'){
			$error |= $this->err->CollectErrorsAltTexts('No user selected. Select a user.', 'No user selected.', 'user_id');
			$error = true;
		}
		return $error;
	}
	
	public function CreateUserSelect() {
		$options = '';
		$errorlist = $this->err->CreateErrorList();
		$erroralttextlist = $this->err->CreateAltTextList();
		$query = "select ID, name from users order by name";
		$result = mysql_query($query);
		if($this->ID == ''){
			$options .= '  <option value="0" selected="selected">Please select ...</option>';
		}else{
			$options .= '  <option value="0">Please select ...</option>';
		}
		while(list($ID, $name) = mysql_fetch_row($result)) {
			if($this->ID == $ID){
				$options .= '  <option value="' . $ID . '" selected="selected">' . $name . '</option>';
			} else {
				$options .= '  <option value="' . $ID . '">' . $name . '</option>';
			}
		}
		return createSelect('user', 'qualilabel', 'ID', 'qualiinput', $options, true, $errorlist, $erroralttextlist, '', false);
	}

	public function LoadDB() {
		$query = 'select UserID, Username, FirstName, LastName, Email from userlogin inner join users on users.ID = userlogin.UserID where userlogin.ID = ' . $this->loginID . ';';
		$result = mysql_query($query);
		list($this->ID, $this->username, $this->firstName, $this->lastName, $this->email) = mysql_fetch_row($result);
		$query1 = 'select role_ID from link_userlogin_roles where userlogin_ID = ' . $this->loginID;
		$result1 = mysql_query($query1);
		
		if($result1){
			$count = 0;
			while(list($resRole) = mysql_fetch_row($result1)){
				$this->roles[$count] = $resRole;
				$count++;
			}
			
			$count = 0;
			while($count < count($this->roles)){
				if(isset($this->roles[$count])){
					$queryRoleName = "select Role from userroles where ID = " . $this->roles[$count];
					$resultRoleName = mysql_query($queryRoleName);
					$this->roles[$count] = mysql_fetch_row($resultRoleName);
				}
				$count++;
			}
		}
	}

	public function InsertDB() {
		$fail = false;
		$query = "insert into users(FirstName, LastName, Email) values('" . mysql_real_escape_string($this->firstName) . "', '" . mysql_real_escape_string($this->lastName) ."', '" . mysql_real_escape_string($this->email) . "');";
		$result1 = mysql_query($query);
		$fail = $fail || mysql_errno() != 0;
		
		//fetch latest added user id
		$file_id= '';
		$query = "select ID from users order by ID desc limit 1";
		$resultid = mysql_query($query);
		$user_id = mysql_fetch_array($resultid);
		$this->ID = $user_id[0];
		
		$query = "insert into userlogin(UserID, Password, Username) values('" . mysql_real_escape_string($this->ID) ."', '" . password_hash($this->password, PASSWORD_DEFAULT) . "', '". mysql_real_escape_string($this->username) ."');";
		$result2 = mysql_query($query);
		$fail = $fail || mysql_errno() != 0;
		
		//fetch latest added userlogin id
		$query = "select ID from userlogin order by ID desc limit 1";
		$resultid = mysql_query($query);
		$user_id = mysql_fetch_array($resultid);
		$this->loginID = $user_id[0];
		
		return !$fail;
	}

	public function UpdateDB() {
		$fail = false;
		
		$query = "delete from link_user_bodyfunctions where ID_UserBody = " . $this->ID . ";";
		echo mysql_query($query);
		$query = "delete from link_userlogin_roles where userlogin_ID = " . $this->loginID . ";";
		echo mysql_query($query);
		
		$query = "update users set FirstName = '" . mysql_real_escape_string($this->firstName) . "', LastName = '" . mysql_real_escape_string($this->lastName) . "', Email = '" . mysql_real_escape_string($this->email) ."' where ID = " . $this->ID . ";";
		$result = mysql_query($query);
		
		if($this->password != ''){	
			$query = "update userlogin set Password = '" . password_hash(mysql_real_escape_string($this->password), PASSWORD_DEFAULT) . "' where UserID = '" . $this->ID . "';";
			$result = mysql_query($query);
		}
		
		$fail = $fail || mysql_errno() != 0;

		return !$fail;
	}
	public function DeleteDB() {
		$fail = false;
		
		$query = "select count(ID_UserModel) from link_user_models where ID_UserModel = " . $this->ID . ";";
		$result = mysql_query($query);
		
		while(list($count) = mysql_fetch_row($result)){
			if($count > 0){
				$query = "update users set active = 0 where ID = " . $this->ID . ";";
				echo mysql_query($query);
				$fail = $fail || mysql_errno() != 0;
			}
			else{
				$query = "delete from link_user_bodyfunctions where ID_UserBody = " . $this->ID . ";";
				echo mysql_query($query);
				$query = "delete from link_userlogin_roles where userlogin_ID = " . $this->loginID . ";";
				echo mysql_query($query);
				$query = "delete from userlogin where UserID = " . $this->ID . ";";
				echo mysql_query($query);
				$query = "delete from users where ID = " . $this->ID . ";";
				echo mysql_query($query);
				$result = mysql_query($query);
				$fail = $fail || mysql_errno() != 0;
			} 
		}
		
		return !$fail;
	}
	
	public function UserLinkRole($link)
	{
		$fail = false;
		
		$query = "insert into link_userlogin_roles(userlogin_ID, role_ID) values(". $this->loginID .", ". $link . ");";
		$result = mysql_query($query);
		
		$fail = $fail || mysql_errno() != 0;

		return !$fail;
	}
}
?>
