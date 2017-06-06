/**
 *
 */
var position = 0;

function GetDevices() {
	
	var index = 0;


	
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
			setTimeout('setChecked(0);',50);
			document.getElementById("title").innerHTML = "Device Categories";
			document.getElementById("category").innerHTML = 'What do you want to control? </br></br>If you choose nothing here, we will show you the available models regardless of hardware.';
			document.getElementById("prevQ").style.visibility = 'hidden';
			localStorage.setItem("Page", "DeviceCategory");
			setTimeout('move(25);', 50);
			document.getElementById("next").onclick = function() {
				saveChecked(index);
				GetPreRequisits();
				setTimeout('setChecked(1);', 50);

			}
		}
	};

	xmlhttp.open("GET", "../PHP/querys.php?index=" + index, true);
	xmlhttp.send();
}

