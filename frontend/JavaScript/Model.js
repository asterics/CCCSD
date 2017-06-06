/**
 *
 */
var DeviceArray = [];
var TechPreArray = [];
var BodyArray = [];
var CheckedDevice = [];
var CheckedTechPre = [];
var CheckedBody = [];

function getModel(id) {
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
			document.getElementById("header").style.borderBottom = "1px solid #18bef0";
			document.getElementById("header").style.paddingBottom = "0em";
			localStorage.setItem("Page", "Model");
			localStorage.setItem("ModelID", id);
		}
	};
	xmlhttp.open("GET", "../PHP/modeldescription.php?id=" + id, true);
	xmlhttp.send();
}

function getModelList() {
	var AllArray = [DeviceArray, TechPreArray, BodyArray];
	//var AllArray = DeviceArray;
	AllArray = JSON.stringify(AllArray);

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
			localStorage.setItem("Page", "Modellist");
			setTimeout('move(100);', 50);
		}
	};
	xmlhttp.open("POST", "../PHP/model.php", true);
	xmlhttp.setRequestHeader("Content-Type", "application/json");
	//xmlhttp.setRequestHeader("Connection", "close");
	xmlhttp.send(AllArray);
}

function saveChecked(index) {
	var i = 0;

	if (index == 0) {
		CheckedDevice = [];
		DeviceArray = [];

		$('input[type=checkbox]').each(function() {
			if (this.checked) {
				DeviceArray[i] = this.value;
				CheckedDevice[i] = this.id;
				i++;
			}

		});
	} else if (index == 1) {
		TechPreArray = [];
		CheckedTechPre = [];

		$('input[type=checkbox]').each(function() {
			if (this.checked) {
				TechPreArray[i] = this.value;
				CheckedTechPre[i] = this.id;
				i++;
			}

		});

	} else if (index == 2) {
		BodyArray = [];
		CheckedBody = [];

		$('input[type=checkbox]').each(function() {
			if (this.checked) {
				BodyArray[i] = this.value;
				CheckedBody[i] = this.id;
				i++;
			}

		});

	}
}

function setChecked(index) {
	var i = 0;

	if (index == 0) {
		while (i < CheckedDevice.length) {
			document.getElementById(CheckedDevice[i]).checked = true;
			i++;
		}

	} else if (index == 1) {
		while (i < CheckedTechPre.length) {
			document.getElementById(CheckedTechPre[i]).checked = true;
			i++;
		}
	} else if (index == 2) {
		while (i < CheckedBody.length) {
			document.getElementById(CheckedBody[i]).checked = true;
			i++;
		}
	}

}

function sleep(delay) {
	var start = new Date().getTime();
	while (new Date().getTime() < start + delay);
}

function countDownloads(id) {
	
	if (window.XMLHttpRequest) {
		// code for IE7+, Firefox, Chrome, Opera, Safari
		xmlhttp = new XMLHttpRequest();
	} else {
		// code for IE6, IE5
		xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
	}
	xmlhttp.open("GET", "../PHP/count.php?id=" + id, true);
	xmlhttp.send();
}

function moveBack() {
	GetBodyFunctions();
	setTimeout('move(75);', 50);
	setTimeout('setChecked(2);', 50);

}

function move(process) {
	$('.progress-bar').css('width', process + '%').attr('aria-valuenow', process);
}

function onload() {

	
	
	CheckedBody = JSON.parse(localStorage.getItem("CheckedBody"));
	CheckedTechPre = JSON.parse(localStorage.getItem("CheckedTechPre"));
	CheckedDevice = JSON.parse(localStorage.getItem("CheckedDevice"));
	DeviceArray = JSON.parse(localStorage.getItem("DeviceArray"));
	TechPreArray = JSON.parse(localStorage.getItem("TechPreArray"));
	BodyArray = JSON.parse(localStorage.getItem("BodyArray"));
	ModelID = localStorage.getItem("ModelID");	
		
	if (localStorage.getItem("Page") == "DeviceCategory") {
		setTimeout('GetDevices();',50);
	} else if (localStorage.getItem("Page") == "TechPreRequisits") {
		setTimeout('GetPreRequisits();',50);
	} else if (localStorage.getItem("Page") == "BodyFunctions") {
		setTimeout('GetBodyFunctions();',50);
	} else if (localStorage.getItem("Page") == "Modellist") {
		setTimeout('getModelList();',50);
	} else if (localStorage.getItem("Page") == "Model") {	
		getModel(ModelID);
	}
	else{
		start();
	}

}

function unload() {

	localStorage.setItem("CheckedBody", JSON.stringify(CheckedBody));
	localStorage.setItem("CheckedTechPre", JSON.stringify(CheckedTechPre));
	localStorage.setItem("CheckedDevice", JSON.stringify(CheckedDevice));
	localStorage.setItem("DeviceArray", JSON.stringify(DeviceArray));
	localStorage.setItem("TechPreArray", JSON.stringify(TechPreArray));
	localStorage.setItem("BodyArray", JSON.stringify(BodyArray));
}

function start(){
	
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
			document.getElementById("header").style.borderBottom = "1px solid #18bef0";
		}
	};
	xmlhttp.open("GET", "../PHP/start.php", true);
	xmlhttp.send();
}







