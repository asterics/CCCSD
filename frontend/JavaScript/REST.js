/**
 *
 */
function pushModel(model){

	if (window.XMLHttpRequest) {
		// code for IE7+, Firefox, Chrome, Opera, Safari
		xmlhttp = new XMLHttpRequest();
	} else {
		// code for IE6, IE5
		xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
	}
	xmlhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {

			var modelInXML = $.parseXML(xmlhttp.responseText);
			
			setBaseURI("http://localhost:8081/rest/");
		
		uploadModel(UM_successCallback, UM_errorCallback, xmlhttp.responseText);
		
		function UM_successCallback(data, HTTPstatus) {
			console.log('success: ' + data);
		}
		
		function UM_errorCallback(HTTPstatus, AREerrorMessage) {
			alert('error: ' + AREerrorMessage + HTTPstatus);
		}

		}			
	};
	xmlhttp.open("GET", model, true);
	xmlhttp.send();

}
