<?php

class Errors {
	var $errorcnt = 0;
	var $errorTextsIptNames = array();
	var $errorAltTextsIptNames = array();

	public function CollectErrors($errortext, $iptname) {
		$this->errorTextsIptNames[$iptname] = $errortext;
		$this->errorcnt++;
		return true;
	}
	
	public function CollectErrorsAltTexts($errortext, $erroralttext, $iptname) {
		$this->errorTextsIptNames[$iptname] = $errortext;
		$this->errorAltTextsIptNames[$iptname] = $erroralttext;
		$this->errorcnt++;
		return true;
	}

	public function addCollectErrors($errors){
		$this->errorcnt += $errors->errorcnt;
		$this->errorTextsIptNames = array_merge($this->errorTextsIptNames, $errors->errorTextsIptNames);
	}	

	public function addCollectErrorsAltTexts($errors){
		$this->errorcnt += $errors->errorcnt;
		$this->errorTextsIptNames = array_merge($this->errorTextsIptNames, $errors->errorTextsIptNames);
		$this->errorAltTextsIptNames = array_merge($this->errorAltTextsIptNames, $errors->errorAltTextsIptNames);
	}	
	
	public function CreateErrorTextList() {
		$content = '';
		if ($this->errorcnt == 1) {
			foreach ($this->errorTextsIptNames as $errTxt) {
				$content .= '<a href="javascript:document.getElementById(\''. array_search($errTxt, $this->errorTextsIptNames) .'\').focus()" class="err"><img src="../images/err_sum.png" alt="Form error" title="Form error" class="errSum"> ' . $errTxt . '</a>';
			}
		} elseif ($this->errorcnt > 1) {
			$content = '<ul class="errorSummary">';
			foreach ($this->errorTextsIptNames as $errTxt) {
				$content .= '<li><a href="javascript:document.getElementById(\''. array_search($errTxt, $this->errorTextsIptNames) .'\').focus()" class="err"><img src="../images/err_sum.png" alt="Form error" title="Form error" class="errSum">' . $errTxt . '</a></li>';
			}
			$content .= '</ul>';
		}
		return $content;
	}
	
	public function CreateErrorList() {
		return $this->errorTextsIptNames;
	}
	
	public function CreateAltTextList() {
		return $this->errorAltTextsIptNames;
	}
}
?>
