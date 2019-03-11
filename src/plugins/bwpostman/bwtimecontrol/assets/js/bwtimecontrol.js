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
//	var server_url	= 'https://www.boldt-webservice.de/index.php?option=com_bwkeygen&amp;controller=licence&amp;task=checkLicence';
	var server_url	= 'https://www.dev3.nil/index.php?option=com_bwkeygen&amp;controller=licence&amp;task=checkLicence';

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

