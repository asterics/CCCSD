/**
 * 
 */

function GetBodyFunctions() {

	var index = 2;
	
	
	if (window.XMLHttpRequest) {
		// code for IE7+, Firefox, Chrome, Opera, Safari
		xmlhttp = new XMLHttpRequest();
	} else {
		// code for IE6, IE5
		xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
	}
	xmlhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			document.getElementById("main").innerHTML = this.responseText;
			document.getElementById("header").style.borderBottom = "";
			document.getElementById("header").style.paddingBottom = "0em";
			setTimeout('setChecked(2);',50);
			document.getElementById("title").innerHTML = "Body Functions";
			document.getElementById("category").innerHTML = 'What body functions can / do you want to use?</br></br>If you choose nothing here, we will show you the available models regardless of hardware.';
			document.getElementById("prevQ").style.visibility='visible';
			setTimeout('move(75);',50);
			localStorage.setItem("Page","BodyFunctions");
			document.getElementById("prevQ").onclick = function() {
				saveChecked(index);
				GetPreRequisits();
				//setTimeout('move(50);',50);	
				setTimeout('setChecked(1);',50);								
			}
			document.getElementById("next").onclick = function() {
				saveChecked(index);				
				getModelList();
				
				
			}
		}
	};
	xmlhttp.open("GET", "../PHP/querys.php?index="+index, true);
	xmlhttp.send();
}

