/**
 *
 */

function GetPreRequisits() {

	var index = 1;
	

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
			setTimeout('setChecked(1);',50);
			document.getElementById("title").innerHTML = "Technical Prerequisits";
			document.getElementById("prevQ").style.visibility = 'visible';
			document.getElementById("category").innerHTML = 'What hardware do you have available / are you willing to buy? </br></br>If you choose nothing here, we will show you the available models regardless of hardware.';															
			setTimeout('move(50);', 50);
			sessionStorage.setItem("Page", "TechPreRequisits");
			document.getElementById("next").onclick = function() {
				saveChecked(index);
				GetBodyFunctions();
				setTimeout('setChecked(2);', 50);
			}
			document.getElementById("prevQ").onclick = function() {
				saveChecked(index);
				GetDevices();
				setTimeout('setChecked(0);', 50);
			}
		}
	};
	xmlhttp.open("GET", "../PHP/querys.php?index=" + index, true);
	xmlhttp.send();
}
