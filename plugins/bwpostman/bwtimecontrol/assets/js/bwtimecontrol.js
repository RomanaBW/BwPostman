function checkReasonableTimes(pressbutton) {

	if (document.getElementsByName("jform[automailing]")[1].checked) {
//		alert ("Wert Automailing im Script: " + document.getElementsByName("jform[automailing]")[1].checked);
		var errobj	= JSON.parse(err_obj)
		var nl_list	= JSON.parse(nllist);
		var nbr_nl	= nl_list.length - 1;
//alert("NL-Liste: " + nllist);
//	 	for (var i = 0; i < nl_list.length; i++) {
//	 		alert("NL-Titel: " + nl_list[i].title);
//	 	}
		
	 	var chaining		= document.getElementsByName("jform[chaining]");
	 	var chaining_val	= 1;
	 	
	 	for (var i = 0; i < chaining.length; i++) {
	 		if (chaining[i].checked) chaining_val = chaining[i].value;
	 	}

		var arrTimeDay	= document.getElementsByName("automailing_values[day][]");
		var arrTimeHour	= document.getElementsByName("automailing_values[hour][]");
		var arrTimeMin	= document.getElementsByName("automailing_values[minute][]");
		var arrTimeNL	= document.getElementsByName("automailing_values[nl_id][]");
		arr_day			= new Array();
		arr_hour		= new Array();
		arr_min			= new Array();
		arr_nl			= new Array();
		arr_sum			= new Array();

//		for (i = 0; i < arrTimeDay.length; i++)
//alert ("Werte Tag: " + arrTimeDay[i].value + '\n' + "Werte Stunde: " + arrTimeHour[i].value + '\n' + "Werte Minute: " + arrTimeMin[i].value + '\n' + "Werte NL: " + arrTimeNL[i].value);

		var k =	arrTimeNL.length;
		for (i = 0; i < k; i++) {
			if (arrTimeNL[i].value != '0') {
				arr_nl[i]	= arrTimeNL[i].value;
				arr_day[i]	= arrTimeDay[i].value*24*60;
				arr_hour[i]	= arrTimeHour[i].value*60;
				arr_min[i]	= arrTimeMin[i].value*1;
				arr_sum[i]	= arr_day[i] + arr_hour[i] + arr_min[i];
				if (arr_sum[i] == 0 && i > 0) {
					alert (errobj.TC1 + (i+1) + errobj.TC2);
				return false;
				}
				if ((chaining_val == 0) && (arr_sum[i] <= arr_sum[i-1])) {
					alert (errobj.TC4 + (i+1) + errobj.TC5);
				return false;
				}
			}	
		}
	
//alert ("Anzahl Sum: " + arr_sum.length + '\n' + "Anzahl NL: " + nbr_nl);
		if (nbr_nl > arr_sum.length) {
			alert (errobj.TC6);
			return false;
		}
		else if (nbr_nl < arr_sum.length) {
			alert (errobj.TC7);
			var del_nbr = arr_sum.length - nbr_nl;
			var max_row = document.getElementById("values").rows.length - 2;
			for (var i = 0; i < del_nbr; i++) {
				document.getElementById("values").deleteRow((max_row));
				max_row--;
			}
		} 
	//	var n =	arr_nl.length;
	//	alert ("Array-Laenge = " + n);
		
		if (arr_nl.length == 0 && document.getElementsByName("jform[automailing]")[1].checked) {
			alert (errobj.TC3);
			return false;
		}
	}
	submitform(pressbutton);
	return;
}
	
	
function display_hidden_content()
{
 	var automailing 	= document.getElementsByName("jform[automailing]");
 	var nbr_elements	= document.getElementsByName("hidden_content");
 	var css_Style		= "";
 	var autimailing_val	= 1;
 	
 	for (var i = 0; i < automailing.length; i++) {
 		if (automailing[i].checked) automailing_val = automailing[i].value;
 	}

 	if (automailing_val == 1) {
    	css_Style = "table-row";
    }
    else {
    	css_Style = "none";
    }
    for (i = 0; i < nbr_elements.length; i++) {
    	nbr_elements[i].style.display = css_Style;
    }
}


function rowDelete (id) 
{
//	alert("Zeilennummer: " + id);
//	alert("NL: " + document.getElementsByName("automailing_values[nl_id][]"));
//	alert ("Wert NL-ID: " + document.getElementsByName("automailing_values[nl_id][]")[id].value);
	document.getElementsByName("automailing_values[day][]")[id].value = 0;
	document.getElementsByName("automailing_values[hour][]")[id].value = 0;
	document.getElementsByName("automailing_values[minute][]")[id].value = 0;
	document.getElementsByName("automailing_values[nl_id][]")[id].value = 0;
//	alert ("Wert NL-ID korrigiert: " + document.getElementsByName("automailing_values[nl_id][]")[id].value);
	Joomla.submitbutton('campaign.apply');
}



function rowInsert (intval, button_text) {

	var table	= document.getElementById("values");
	var row		= table.rows.length - 1;
	
	var nl_list	= '';
	nl_list	= JSON.parse(nllist);
	
	var TR = document.getElementById("values").insertRow((row));
	var classTrTyp1 = document.createAttribute("class");
	classTrTyp1.nodeValue = "bwptable";
	TR.setAttributeNode(classTrTyp1);
	var classTrTyp2 = document.createAttribute("name");
	classTrTyp2.nodeValue = "hidden_content";
	TR.setAttributeNode(classTrTyp2);
	  
	var TD1 = document.createElement("td"); // first field: mailnumber
	var classTyp1 = document.createAttribute("class");
	classTyp1.nodeValue = "key";
	TD1.setAttributeNode(classTyp1);
	var classTyp2 = document.createAttribute("align");
	classTyp2.nodeValue = "right";
	TD1.setAttributeNode(classTyp2);
	var classTyp3 = document.createAttribute("width");
	classTyp3.nodeValue = "200px";
	TD1.setAttributeNode(classTyp3);
	var TD1span = document.createElement("span");
	var classSpan1 = document.createAttribute("class");
	classSpan1.nodeValue = "bwplabel";
	TD1span.setAttributeNode(classSpan1);
	var TD1text = document.createTextNode((row)+". Mail");
	TD1span.appendChild(TD1text);
	TD1.appendChild(TD1span);
	  
	var TD2 = document.createElement("td"); // second field: list day
	var TD2SelectElement = document.createElement("select");
	var classTyp2 = document.createAttribute("class");
	TD2SelectElement.setAttribute('class','auto_value_day');
	TD2SelectElement.setAttribute('name','automailing_values[day][]');  
	TD2SelectElement.setAttribute('id','automailing_values[day][' + (row) + ']');  
	var option2;
	for (var k = 0; k <= 31; k++) {
		option2 = document.createElement ("option");
		option2.setAttribute('class','auto_value_day');
		option2.setAttribute('value',k);
		option2.innerHTML = k;
		TD2SelectElement.appendChild (option2);
	}
	TD2.appendChild(TD2SelectElement);
	 
	var TD3 = document.createElement("td"); // third field: list hour
	var TD3SelectElement = document.createElement("select");
	TD3SelectElement.setAttribute('class','auto_value_hour');
	TD3SelectElement.setAttribute('name','automailing_values[hour][]');  
	TD3SelectElement.setAttribute('id','automailing_values[hour][' + (row) + ']');  
	var option3;
	for (var k = 0; k < 24; k++) {
		option3 = document.createElement ("option");
		option3.setAttribute('class','auto_value_hour');
		option3.setAttribute('value',k);
		option3.innerHTML = k;
		TD3SelectElement.appendChild (option3);
	}
	TD3.appendChild(TD3SelectElement);
	 
	var TD4 = document.createElement("td"); // fouth field: list minute 
	var TD4SelectElement = document.createElement("select");
	TD4SelectElement.setAttribute('class','automailing_value_minute');
	TD4SelectElement.setAttribute('name','automailing_values[minute][]');  
	TD4SelectElement.setAttribute('id','automailing_values[minute][' + (row) + ']');  
	var option4;
	for (var k = 0; k < 60; k += intval) {
		option4 = document.createElement ("option");
		option4.setAttribute('class','auto_value_minute');
		option4.setAttribute('value',k);
		option4.innerHTML = k;
		TD4SelectElement.appendChild (option4);
	}
	TD4.appendChild(TD4SelectElement);
	    
	var TD5 = document.createElement("td"); // fifth field: list newsletter
	var TD5SelectElement = document.createElement("select");
	TD5SelectElement.setAttribute('class','auto_value_nl');
	TD5SelectElement.setAttribute('name','automailing_values[nl_id][]');  
	TD5SelectElement.setAttribute('id','automailing_values[nl_id][' + (row) + ']');  
	var option5;

	for (id = 0; id < nl_list.length; id++) {
		option5 = document.createElement ("option");
		option5.setAttribute('class','auto_value_nl');
		option5.setAttribute('value',nl_list[id].nl_id);
		option5.innerHTML = nl_list[id].title;
		TD5SelectElement.appendChild (option5);
	}
	TD5.appendChild(TD5SelectElement);
	    
	var TD6 = document.createElement("td"); // sixth field: button apply

	var TD6InputElement = document.createElement("input"); 
	TD6InputElement.setAttribute('type','button');
	TD6InputElement.setAttribute('class','btn btn-small btn-success');
	TD6InputElement.setAttribute('onclick','Joomla.submitbutton("campaign.apply")');
	TD6InputElement.setAttribute('value',button_text);
	
	TD6.appendChild(TD6InputElement);
	  
	TR.appendChild(TD1);
	TR.appendChild(TD2);
	TR.appendChild(TD3);
	TR.appendChild(TD4);
	TR.appendChild(TD5);
	TR.appendChild(TD6);  
}
             
function checkLicenceCode(domain, licencecode){
	
	domain		= domain || '';
	licencecode	= licencecode || '';
	
	var xmlHttpObject	= false;
	var result			= '';
	
	// Überprüfen ob XMLHttpRequest-Klasse vorhanden und erzeugen von Objekte für IE7, Firefox, etc.
	if (typeof XMLHttpRequest != 'undefined') 
	{
	    xmlHttpObject = new XMLHttpRequest();
	}

	// Wenn im oberen Block noch kein Objekt erzeugt, dann versuche XMLHTTP-Objekt zu erzeugen
	// Notwendig für IE6 oder IE5
	if (!xmlHttpObject) 
	{
	    try 
	    {
	        xmlHttpObject = new ActiveXObject("Msxml2.XMLHTTP");
	    }
	    catch(e) 
	    {
	        try 
	        {
	            xmlHttpObject = new ActiveXObject("Microsoft.XMLHTTP");
	        }
	        catch(e) 
	        {
	            xmlHttpObject = null;
	        }
	    }
	}
	
    // Zieladresse festlegen
//	var server_url	= 'http://www.boldt-webservice.de/index.php?option=com_bwkeygen&amp;controller=licence&amp;task=checkLicence';
	var server_url	= 'http://www.dev3.nil/index.php?option=com_bwkeygen&amp;controller=licence&amp;task=checkLicence';
	
    // Übergabe-Parameter setzen
	var domain		= encodeURIComponent(domain);
	var licencecode	= encodeURIComponent(licencecode);
//	alert('Test Domain: ' + domain);
//	alert('Test Lizenz: ' + licencecode);
	var parameters	= 'domain=' + domain + '&licence_code=' + licencecode;
//	alert('Test Parameter: ' + parameters);

	// Funktion, die bei Statusänderungen reagiert
    function handleStateChange()
    {
        // Derzeitigen Status zurückgeben
        alert("xmlHttpObject.readyState = " + xmlHttpObject.readyState + (xmlHttpObject.readyState >= 3 ? "\n\r HTTP-Status = " + xmlHttpObject.status : ''));
        if (xmlHttpObject.readyState==4){
      		alert("Ergebnis Text: " + xmlHttpObject.responseText);
    		  if (xmlHttpObject.status==200 || window.location.href.indexOf("http")==-1){
        		document.getElementById("result").innerHTML=xmlHttpObject.responseText;
        	}
        	else{
        	   alert("An error has occured making the request");
        	}
        }
    }   
    
    // Wenn Dokument geladen ausführen
 //   window.onload = function() {
        // Anfrage vorbereiten, ruft gewünschte Seite auf
        xmlHttpObject.open('POST', server_url, true);

        // Request Header erstellen
        xmlHttpObject.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
        
        // Anfrage abschicken
        xmlHttpObject.send(parameters);
        
        // Handler hinterlegen
        xmlHttpObject.onreadystatechange = handleStateChange;
//    }
}

