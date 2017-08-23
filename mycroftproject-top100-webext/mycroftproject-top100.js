// Start point of application
/*fetch("http://mycroftproject.com/dlstats.html").then(htmlText => {
	parser = new DOMParser();
	xmlDoc = parser.parseFromString(htmlText,"text/html");
     return processDataInWorker(v); // returns a promise
});*/

getHtmlDoc("http://mycroftproject.com/dlstats.html").then(responseText => {
	console.log("Hi from the getHTMLDoc function");
	//console.log(responseText);
	
	parser = new DOMParser();
	htmlDoc = parser.parseFromString(responseText,"text/html");
	
	var xmlUrls = getSearchEngineLines(htmlDoc).map(function(item) {
		return item.xml;
	});
	
	// separate function to make code more clear
	const grabContent = url => fetch(url)
		.then(res => res.text())
		.then(xml => ( addToArray(xml, url)))

	Promise
		.all(xmlUrls.map(grabContent))
		.then(() => done())
}).catch(console.error);

//;

// More beautiful, but doesn't work if we're not on the right domain
/*async function getHtmlDoc(pageUrl) {
    try {
		console.log("hi!!!");
        let response = await fetch(pageUrl);
        let responseText = await response.text();
        return responseText;
    } catch(error) {
        throw error.message;
    }
}*/

/*async function getHtmlDoc(pageUrl){
	console.log("hi!!!");
	var request = new XMLHttpRequest();
    request.onreadystatechange = function()
    {
        if (request.readyState == 4 && request.status == 200)
        {
            callback(request.responseText); // Another callback here
        }
    }; 
    request.open('GET', url);
    request.send();	
}*/

function getHtmlDoc(url) {
	console.log("hi..");
    return new Promise(function (resolve, reject) {
        var request = new XMLHttpRequest();
		request.onreadystatechange = function()
		{
			if (request.readyState == 4){
				if(request.status == 200){
					console.log("resolving..");
					resolve(request.responseText);
				}else{
					console.log("could not complete request.." + request.readyState + " - " + request.status);
					reject("Could not complete request");
				}
			}
		};
		
		request.open('GET', url);
		request.send();	      
    });
}


function getSearchEngineLines(htmlDoc){
	var lines = [];
	if(htmlDoc.getElementById("maincontent") == null){
		console.error("Try running this script on a valid page, such as http://mycroftproject.com/dlstats.html");
		return null;
	}
	var tds = htmlDoc.getElementById("maincontent").getElementsByTagName("td");
	
	for(let td of tds){
		var ahrefs = td.getElementsByTagName("a");
		var counter = 0;
		var line = {};

		for(let a of ahrefs){
			counter++;
			if(counter == 1){
				line.description = "";
				line.searchengine = a.innerHTML;

				if(line.searchengine.indexOf("(") > -1){
					line.description = line.searchengine.substring(line.searchengine.indexOf("(") + 1).replace(")", "").trim();
					line.searchengine = line.searchengine.substring(0, line.searchengine.indexOf("(")).trim();
				}
			}

			if(counter == 2)
				 line.website = a.innerHTML;

			if(counter == 3){
				counter = 0;
				line.id = parseId(a.getAttribute("onclick"));
				line.xml = "http://mycroftproject.com/installos.php/" + line.id + "/" + line.searchengine.replace(" ", "%20") + ".xml";
				lines.push(line);
			}
		}
	}

	function parseId(onclick){
		return onclick.replace("judgePopUp('", "").replace("','1');return false", "");
	}

	return lines;

}

var searchEnginesJSON = [];
function addToArray(xml, url){
	//console.log(xml);
	parser = new DOMParser();
	xmlDoc = parser.parseFromString(xml,"text/xml");
		
	let searchEngineJSON = {};
	searchEngineJSON.ShortName = getProperty(xmlDoc, "ShortName");
	searchEngineJSON.Description = getProperty(xmlDoc, "Description");
	searchEngineJSON.Contact = getProperty(xmlDoc, "Contact");
	searchEngineJSON.Image = getProperty(xmlDoc, "Image");
	searchEngineJSON.Developer = getProperty(xmlDoc, "Developer");
	searchEngineJSON.InputEncoding = getProperty(xmlDoc, "InputEncoding");
	searchEngineJSON.SearchUrl = getPropertyUrlAttribute(xmlDoc, "text/html");
	searchEngineJSON.SuggestionsUrl = getPropertyUrlAttribute(xmlDoc, "application/x-suggestions+json");
	searchEngineJSON.XmlViewUrl = url;
	searchEngineJSON.XmlDownloadUrl = getPropertyUrlAttribute(xmlDoc, "application/opensearchdescription+xml");
	searchEngineJSON.mozSearchForm = getProperty(xmlDoc, "moz:SearchForm");

	searchEnginesJSON.push(searchEngineJSON);
	
	function getProperty(xmlDoc, propertyName){
	try{
		var propertyElements = xmlDoc.documentElement.getElementsByTagName(propertyName);
		if(propertyElements.length == 0) return "";
		return propertyElements[0].textContent;
	}catch(ex){
		console.error("failed to parse " + propertyName + " for " + url);
		console.log(ex);
	}
	}
	
	function getPropertyUrlAttribute(xmlDoc, propertyAttributeType){
		//console.log("hi from getPropertyUrlAttribute");
		
		var urlTags = xmlDoc.getElementsByTagName("Url");
		if(urlTags.length == 0){
			return "";
		}
		for(let urlTag of urlTags){		
			if(urlTag.getAttribute("type") == propertyAttributeType){
				let attribute = urlTag.getAttribute("template");
				if(attribute != null) return attribute;
			}
		}
		return "";
	}
}
//text/html
function done(){
	var jsonStrings = searchEnginesJSON.map(function(se) {
		return JSON.stringify(se);
	});
	var text = "[" + jsonStrings.join(",") + "]";
	
	var data = text;
	myWindow = window.open("data:application/json," + encodeURIComponent(data),
						   "_blank");
	myWindow.focus();
}

