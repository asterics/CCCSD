<?php
//require_once ('header_menu.php');

//$headerMenue = new headerMenu();

function checkIfSessionActive(){
	if(!isset($_SESSION['ID'])){
		header("Location: ../index.php");
	}
}

function isAdmin() {
    return $_SESSION['ACCESS'] == 'Admin';
}

function canEdit() {
    return $_SESSION['ACCESS'] == 'Admin' || $_SESSION['ACCESS'] == 'User';
}

function canDelete() {
    return $_SESSION['ACCESS'] == 'Admin';
}

/*function getHeaderAndMenue($act_function_id, $javascr){
	global $headerMenue;
	return $headerMenue->getHeaderAndMenue($act_function_id, $javascr);

}*/

/*function getHeaderAndMenueAddition($act_function_id, $javascr, $addition){
	global $headerMenue;
	return $headerMenue->getHeaderAndMenueAddition($act_function_id, $javascr, $addition);
}*/

function getFooter(){
	return "			</div>"."\n"."		</div> <!--Ende Inhalt-->"."\n"."	</div></body>"."\n"."</html>";
}

function doWortersetzung($text, $wortersetzungsliste_id,$kursiv=true) {
    
	if ($wortersetzungsliste_id != '') {
		$query = "select
  						Platzhalter, 
  						Wort 
  					from
  						worte,
  						platzhalter
  					where Wortersetzungsliste_id = " . $wortersetzungsliste_id . "
  					and Platzhalter_ID = platzhalter.ID";
               
		$result = mysql_query($query);
		while (list($platzhalter, $wort) = mysql_fetch_row($result)) {
                        $platzhalter = replaceUmlauteByHTML($platzhalter);
			$text = str_replace($platzhalter, $wort, $text);
		}
	}
	$query = "select
					Platzhalter, 
					Standardwort 
				from 
					platzhalter;";
	$result = mysql_query($query);
	while (list($platzhalter, $standardwort) = mysql_fetch_row($result)) {
                if ($kursiv)
                    $standardwort = '<em>' . $standardwort . '</em>';
		$text = str_replace(replaceUmlauteByHTML($platzhalter), $standardwort, $text);
                // TODO: Delete the following line when going live
                $text = str_replace($platzhalter, $standardwort, $text);
	}
	return $text;
}

function replaceUmlauteByHTML($text) {
    $text = str_replace("ä", "&auml;", $text);
    $text = str_replace("ö", "&ouml;", $text);
    $text = str_replace("ü", "&uuml;", $text);
    $text = str_replace("ß", "&szlig;", $text);
    return $text;
}

/* UI Elemente start */
function createTextInput($label, $labelclass, $inputname, $inputclass, $inputvalue, $required, $errorlist, $erroralttextlist,$enabled=true) {
        $reqImg = '';
		$content = '';
	if ($required) {
		$reqImg = ' <img src="../images/required.png" alt="required" title="required" class="required"/>';
	}
	$errImg = '';
	if ($errorlist != '' && $erroralttextlist != '' && isset($errorlist[$inputname])) {
		$errImg = '<img src="../images/err.png" alt="' . $erroralttextlist[$inputname] . '" title="' . $erroralttextlist[$inputname] . '" class="error"/> ';
	}
	$content .= '<div class="formrowLabel"><label class="' . $labelclass . '" for="' . $inputname . '">' . $errImg . $label . $reqImg . ':</label>';
	$content .= '</div>';
        $disabled = "";
        if ($enabled == false)
            $disabled = "disabled=\"true\"";
		
	if(!$required)
		$content .= '<div class="formrowText"><input class="' . $inputclass . '" type="text" id="' . $inputname . '" name="' . $inputname . '" value="' . $inputvalue . '" '.$disabled.'  maxlength="45" aria-described-by="p_' . $inputname . '" /></div>' . "\n";
	else
		$content .= '<div class="formrowText"><input class="' . $inputclass . '" type="text" id="' . $inputname . '" name="' . $inputname . '" value="' . $inputvalue . '" '.$disabled.' maxlength="45" aria-required="true" aria-described-by="p_' . $inputname . '"/></div>' . "\n";
		
	if ($errorlist != '' && $erroralttextlist != '' && isset($errorlist[$inputname])) {
		if($inputname == 'name')
			$content .= '<div class="err"><p id="p_' . $inputname .'" class="errMsg">No ' . $label . ' entered. Please enter a name for your model, such as "AsTeRICS Test Model".</p></a></div>';
		if($inputname == 'firstName')
			$content .= '<div class="err"><p id="p_' . $inputname .'" class="errMsg">No ' . $label . ' entered. Please enter your first name, such as "John".</p></div>';
		if($inputname == 'lastName')
			$content .= '<div class="err"><p id="p_' . $inputname .'" class="errMsg">No ' . $label . ' entered. Please enter your last name, such as "Doe".</p></div>';
		if($inputname == 'email')
			$content .= '<div class="err"><p id="p_' . $inputname .'" class="errMsg">No ' . $label . ' entered or entered email is of invalid format. Please reenter your email address, such as "johndoe@email.com".</p></div>';
		if($inputname == 'username')
			$content .= '<div class="err"><p id="p_' . $inputname .'" class="errMsg">No ' . $label . ' entered. Please enter your preffered username, such as "JohnDoe90".</p></div>';
		if($inputname == 'techName')
			$content .= '<div class="err"><p id="p_' . $inputname .'" class="errMsg">No ' . $label . ' entered. Please enter a name for the new tech-prerequisite, such as "Keyboard".</p></div>';
		if($inputname == 'devName')
			$content .= '<div class="err"><p id="p_' . $inputname .'" class="errMsg">No ' . $label . ' entered. Please enter a name for the new device category, such as "Keyboard".</p></div>';
		if($inputname == 'bodyName')
			$content .= '<div class="err"><p id="p_' . $inputname .'" class="errMsg">No ' . $label . ' entered. Please enter a name for the new bodyfunction, such as "Arms".</p></div>';
		if($inputname == 'roleName')
			$content .= '<div class="err"><p id="p_' . $inputname .'" class="errMsg">No ' . $label . ' entered. Please enter a name for the new role, such as "Admin".</p></div>';
	}
	
	return $content;
}

/* UI Elemente start */
function createTimeTextInput($label, $labelclass, $inputname, $inputclass, $inputvalue, $required, $errorlist, $erroralttextlist,$maskformat) {
        $reqImg = '';
	if ($required) {
		$reqImg = ' <img src="required.png" alt="Pflichtfeld" title="Pflichtfeld" />';
	}
	$errImg = '';
	if ($errorlist != '' && $erroralttextlist != '' && isset($errorlist[$inputname])) {
		$errImg = '<img src="err.png" alt="' . $erroralttextlist[$inputname] . '" title="' . $erroralttextlist[$inputname] . '" /> ';
	}
	$content  = '<div class="formrow"><label class="' . $labelclass . '" for="' . $inputname . '">' . $errImg . $label . $reqImg . ':</label>';
	$content .= '<input class="' . $inputclass . '" type="text" id="' . $inputname . '" name="' . $inputname . '" value="' . $inputvalue . '" /></div>' . "\n";
        $content .= '<script>jQuery(function($){
                        $("#'.$inputname.'").mask("'.$maskformat.'");
                     });</script>';
	return $content;
}

function createDateTextInput($label, $labelclass, $inputname, $inputclass, $inputvalue, $required, $errorlist, $erroralttextlist) {
        $reqImg = '';
	if ($required) {
		$reqImg = ' <img src="required.png" alt="Pflichtfeld" title="Pflichtfeld" />';
	}
	$errImg = '';
	if ($errorlist != '' && $erroralttextlist != '' && isset($errorlist[$inputname])) {
		$errImg = '<img src="err.png" alt="' . $erroralttextlist[$inputname] . '" title="' . $erroralttextlist[$inputname] . '" /> ';
	}
	$content  = '<div class="formrow"><label class="' . $labelclass . '" for="' . $inputname . '">' . $errImg . $label . $reqImg . ':</label></div>';
	$content .= '<div class="formrow"><input class="' . $inputclass . '" type="text" id="' . $inputname . '" name="' . $inputname . '" value="' . $inputvalue . '" /></div>' . "\n";
	$content .= '<script>
                        $(function() {
                        $( "#'.$inputname.'" ).datepicker({dateFormat: \'dd.mm.yy\',closeText: \'Kalender schließen\',
                        currentText: \'Heute\',
                        dayNames: [\'Sonntag\', \'Montag\', \'Dienstag\', \'Mittwoch\', \'Donnerstag\', \'Freitag\', \'Samstag\'],
                        dayNamesMin: [\'SO\', \'MO\', \'DI\', \'MI\', \'DO\', \'FR\', \'SA\'],
                        monthNames: [\'Januar\', \'Februar\', \'März\', \'April\', \'Mai\',
                          \'Juni\', \'Juli\', \'August\', \'September\', \'Oktober\',  \'November\', \'Dezember\'],});
                        });
    			</script>';
        return $content;
}


function createTextArea($label, $labelclass, $textareaname, $textareaclass, $textareavalue, $required, $errorlist, $erroralttextlist, $tinyMCE) {
	$content = '';
	$reqImg = '';
	if ($required) {
		$reqImg = ' <img src="../images/required.png" alt="required" title="required" class="required"/>';
	}
	$errImg = '';
	if ($errorlist != '' && $erroralttextlist != '' && isset($errorlist[$textareaname])) {
		$errImg = '<img src="../images/err.png" alt="' . $erroralttextlist[$textareaname] . '" title="' . $erroralttextlist[$textareaname] . '" class="error"/> ';	
	}
	
        if ($tinyMCE) {
		$content .= '
					<!-- TinyMCE -->
						<script type="text/javascript" src="../jscripts/tiny_mce/tiny_mce.js"></script>
						<script type="text/javascript">
							initTinyMCE();
						</script>
					<!-- /TinyMCE -->' . "\n";
	}
	$content .= '<div class="formrowArea"><label class="' . $labelclass . '" for="' . $textareaname . '">' . $errImg . $label . $reqImg . ':</label> ' . "\n";
	$content .= '</div>';
	if(!$required)
		$content .= '<div class="formrowAreaInput"><textarea class="' . $textareaclass . '" name="' . $textareaname . '" id="' . $textareaname . '" rows="20" cols="100" maxlength="65535" aria-described-by="p_' . $textareaname . '" >' . $textareavalue . '</textarea></div>' . "\n";
	else
		$content .= '<div class="formrowAreaInput"><textarea class="' . $textareaclass . '" name="' . $textareaname . '" id="' . $textareaname . '" rows="20" cols="100" maxlength="65535" aria-required="true" aria-described-by="p_' . $textareaname . '" >' . $textareavalue . '</textarea></div>' . "\n";
	
	if ($errorlist != '' && $erroralttextlist != '' && isset($errorlist[$textareaname])) {
		$content .= '<div class="err"><p id="p_' . $textareaname .'" class="errMsg">No ' . $label . ' entered. Please take your time to describe the model you are trying to upload in the textfield below.</p></div>';
	}
	
	return $content;
}

function createSelect($label, $labelclass, $inputname, $inputclass, $options, $required, $errorlist, $erroralttextlist, $onchange, $disabled) {
	$reqImg = '';
	if ($required) {
		$reqImg = ' <img src="required.png" alt="Pflichtfeld" title="Pflichtfeld" />';
	}
	$errImg = '';
	if ($errorlist != '' && $erroralttextlist != '' && isset($errorlist[$inputname])) {
		$errImg = '<img src="err.png" alt="' . $erroralttextlist[$inputname] . '" title="' . $erroralttextlist[$inputname] . '" /> ';
	}
	$onchg = '';
	if ($onchange != '') {
		$onchg = 'onchange="' . $onchange . '"';
	}
	$dis = '';
	if ($disabled) {
		$dis = 'disabled = "disabled"';
	}
	$content  = '<div class="formrow"><label class="' . $labelclass . '" for="' . $inputname . '">' . $errImg . $label . $reqImg . ':</label>';
	$content .= '<select class="' . $inputclass . '" name="' . $inputname . '" id="' . $inputname . '" ' . $onchg . ' ' . $dis . '>';
	$content .= $options;
	$content .= '</select>';
	$content .= '</div>' . "\n";
	return $content;
}

function createFileInput($label, $labelclass, $inputname, $inputclass, $inputvalue, $required, $errorlist, $erroralttextlist,$enabled=true){
        $reqImg = '';
		$content = '';
	if ($required) {
		$reqImg = ' <img src="../images/required.png" alt="required" title="required" class="required"/>';
	}
	$errImg = '';
	if ($errorlist != '' && $erroralttextlist != '' && isset($errorlist[$inputname])) {
		$errImg = '<img src="../images/err.png" alt="' . $erroralttextlist[$inputname] . '" title="' . $erroralttextlist[$inputname] . '" class="error"/> ';
	}
	$content .= '<div class="formrowLabel"><label class="' . $labelclass . '" for="' . $inputname . '">' . $errImg . $label . $reqImg . ':</label>';
	$content .= '</div>';
        $disabled = "";
        if ($enabled == false)
            $disabled = "disabled=\"true\"";
		
	if(!$required)
		$content .= '<div class="formrowFile"><input class="' . $inputclass . '" type="file" id="' . $inputname . '" name="' . $inputname . '" value="' . $inputvalue . '" '.$disabled.'  aria-described-by="p_' . $inputname . '" /></div>' . "\n";
	else
		$content .= '<div class="formrowFile"><input class="' . $inputclass . '" type="file" id="' . $inputname . '" name="' . $inputname . '" value="' . $inputvalue . '" '.$disabled.' aria-required="true" aria-described-by="p_' . $inputname . '"/></div>' . "\n";
	
	if ($errorlist != '' && $erroralttextlist != '' && isset($errorlist[$inputname])) {
		$content .= '<div class="err"><p id="p_' . $inputname .'" class="errMsg">No ' . $label . ' selected. Please select the file of the model you are trying to upload by pressing the button below.</p></div>';
	}
	
	return $content;
}

function createPassword($label, $labelv, $labelclass, $inputname, $inputnamev, $inputclass, $required, $errorlist, $erroralttextlist,$enabled=true){
        $reqImg = '';
		$content = '';
	if ($required) {
		$reqImg = ' <img src="../images/required.png" alt="required" title="required" class="required"/>';
	}
	$errImg = '';
	if ($errorlist != '' && $erroralttextlist != '' && isset($errorlist[$inputname])) {
		$errImg = '<img src="../images/err.png" alt="' . $erroralttextlist[$inputname] . '" title="' . $erroralttextlist[$inputname] . '" class="error"/> ';
	}

	if ($errorlist != '' && $erroralttextlist != '' && isset($errorlist[$inputnamev])) {
		$errImg = '<img src="../images/err.png" alt="' . $erroralttextlist[$inputnamev] . '" title="' . $erroralttextlist[$inputnamev] . '" class="error"/> ';
	}
	
	$content .= '<div class="formrowLabel"><label class="' . $labelclass . '" for="' . $inputname . '">' . $errImg . $label . $reqImg . ':</label>';
	$content .= '</div>';
        $disabled = "";
        if ($enabled == false)
            $disabled = "disabled=\"true\"";
		
	if(!$required)
		$content .= '<div class="formrowPass"><input class="' . $inputclass . '" type="password" id="' . $inputname . '" name="' . $inputname . '" '.$disabled.'  aria-described-by="p_' . $inputname . '" /></div>' . "\n";
	else
		$content .= '<div class="formrowPass"><input class="' . $inputclass . '" type="password" id="' . $inputname . '" name="' . $inputname . '" '.$disabled.' aria-required="true" aria-described-by="p_' . $inputname . '"/></div>' . "\n";
	
	$content .= '<div class="formrowLabel"><label class="' . $labelclass . '" for="' . $inputnamev . '">' . $errImg . $labelv . $reqImg . ':</label>';
	$content .= '</div>';
        $disabled = "";
        if ($enabled == false)
            $disabled = "disabled=\"true\"";
		
	if(!$required)
		$content .= '<div class="formrowPass"><input class="' . $inputclass . '" type="password" id="' . $inputnamev . '" name="' . $inputnamev . '" '.$disabled.'  aria-described-by="p_' . $inputnamev . '" /></div>' . "\n";
	else
		$content .= '<div class="formrowPass"><input class="' . $inputclass . '" type="password" id="' . $inputnamev . '" name="' . $inputnamev . '" '.$disabled.' aria-required="true" aria-described-by="p_' . $inputnamev . '"/></div>' . "\n";
	
	if ($errorlist != '' && $erroralttextlist != '' && isset($errorlist[$inputname])) {
		$content .= '<div class="err"><p id="p_' . $inputname .'" class="errMsg">No password entered or entered passwords do not match. Please reenter your password.</p></div>';
	}
	
	/*if ($errorlist != '' && $erroralttextlist != '' && isset($errorlist[$inputname])) {
		$content .= '<div class="err"><p id="p_' . $inputnamev .'Missmatch" class="errMsg">The entered passwords do not match. Please enter the passwords again.</p></div>';
	}*/
	
	return $content;
}
/* UI Elmente Ende */



function prepareFloatMysql($float) {
	return str_replace(',', '.', $float);
}

function prepareFloatPhp($float) {
	return str_replace('.', ',', $float);
}

/**
 * Validates an email-address.
 * The function changes the parameter by cutting of leading and following whitespaces and setting it to lower case.
 *
 * @param Reference to a string holding an email-address.
 * @param Boolean (default false) telling wether the validation should be strict or not.
 *        Strict validation does not allow special characters (like umlauts) in the email-address.
 * @returm Boolean which is true if parameter is a valid email-address, false otherwise.
 */
function validateEmailAddress(&$address_to_validate, $strict = false) {
	//Leading and following whitespaces are ignored
	$address_to_validate = trim($address_to_validate);
	//Email-address is set to lower case
	$address_to_validate = strtolower($address_to_validate);

	//List of signs which are illegal in name, subdomain and domain
	$illegal_string = '\\\\(\\n)@';

	//Parts of the regular expression = name@subdomain.domain.toplevel
	$name      = '([^\\.'.$illegal_string.'][^'.$illegal_string.']?)+';
	$subdomain = '([^\\._'.$illegal_string.']+\\.)?';
	$domain    = '[^\\.\\-_'.$illegal_string.'][^\\._'.$illegal_string.']*[^\\.\\-_'.$illegal_string.']';
	$toplevel  = '([a-z]{2,4}|museum|travel)';    //.museum and .travel are the only TLDs longer than four signs

	$regular_expression = '/^'.$name.'[@]'.$subdomain.$domain.'\.'.$toplevel.'$/';

	return preg_match($regular_expression, $address_to_validate) ? true : false;
}

function isInt ($x) {
	return (is_numeric($x) ? intval($x) == $x : false);
}

function isDate($date){
	$retVal = false;
	$pattern = "/^([0-9]{2})[\.]([0-9]{2})[\.]([0-9]{4})$/";
	if (preg_match($pattern, $date, $matches)) {
		$retVal= checkdate($matches[2], $matches[1], $matches[3]);
	}
	return $retVal;
}

function date_german2mysql($datum) {
	if ($datum != '') {
		list($tag, $monat, $jahr) = explode(".", $datum);
		return sprintf("%04d-%02d-%02d", $jahr, $monat, $tag);
	} else {
		return '';
	}
}

function date_mysql2german($datum) {
	if ($datum != '' && $datum != '0000-00-00') {
		list($jahr, $monat, $tag) = explode("-", $datum);
		return sprintf("%02d.%02d.%04d", $tag, $monat, $jahr);
	} else {
		return '';
	}
}


?>
