<?php
header('Content-Type: application/json; charset=utf-8');

$debugmsgs = array();

// Supported internal query parameters (not visible to the user of the API)
$searchProperties = array();
$searchProperties[] = "name";
$searchProperties[] = "language";
$searchProperties[] = "category";
$searchProperties[] = "country";

// Input API parameters (visible to the user of the API)
$Name = get_parameter("shortname") . get_parameter("name");
$Language = get_parameter("language");
$Category = get_parameter("category");
$Country = get_parameter("country");
$Limit = get_parameter("limit");
$OnlyValid = strtolower(get_parameter("onlyvalid"));

$engines_name_id = get_engines_name_id($Name, $Language, $Category, $Country);

$searchEnginesJson = array();
$searchEnginesObj = array();
$counter = 0;

foreach($engines_name_id as $engine_name_id){
	//echo $engine_name_id -> name . "-" . $engine_name_id -> id;
	$counter++;
	if(isset($Limit) && $Limit != ""){
		if($counter > $Limit){
			debug_print("Reached the limit, no longer processing/reading other results.");
			break;
		}
	}
	$url = "http://mycroftproject.com/installos.php/" . trim($engine_name_id -> id) . "/" . trim($engine_name_id -> name) . ".xml";
	$engine = get_engine_xml_by_url($url);
	if($engine == null && $OnlyValid != "true"){
		$jsonObject = array(
			'ShortName' => "",
			'Description' => "",
			'Contact' => "",
			'Image' => "",
			'Developer' => "",
			'InputEncoding' => "",
			'SearchUrl' => "",
			'SuggestionsUrl' => "",
			'XmlViewUrl' => $url,
			'XmlDownloadUrl' => "",
			'mozSearchForm' => ""
		);
		$searchEnginesJson[] = $jsonObject;
		continue;
	}
	//$jsonObject = json_encode((array)$engine);
	
	$jsonObject = array(
		'ShortName' => $engine -> ShortName,
		'Description' => $engine -> Description,
		'Contact' => $engine -> Contact,
		'Image' => $engine -> Image,
		'Developer' => $engine -> Developer,
		'InputEncoding' => $engine -> InputEncoding,
		'SearchUrl' => $engine -> SearchUrl,
		'SuggestionsUrl' => $engine -> SuggestionsUrl,
		'XmlViewUrl' => $engine -> XmlViewUrl,
		'XmlDownloadUrl' => $engine -> XmlDownloadUrl,
		'mozSearchForm' => $engine -> mozSearchForm
	);
		
	debug_print($jsonObject);
	$searchEnginesJson[] = $jsonObject;
}

print json_encode((array)$searchEnginesJson);

//$engine = get_engine_xml_by_url("http://mycroftproject.com/installos.php/14897/test.xml");
//echo json_encode((array)$engine);

//$jsonObject = json_encode((array)$engine, JSON_PRETTY_PRINT);
//echo $data;

function get_parameter($parametername){
	$parameter = isset($_GET[$parametername]) ? $_GET[$parametername] : '';
	if($parameter == ""){
		$parameter = getenv(strtoupper($parametername));
	}
	return $parameter;
}

function debug_print($message){
	global $debugmsgs;
	//echo $message;
	$debugmsgs[] = $message;
}

function get_engines_name_id($Name,$Language,$Category,$Country){
	global $searchProperties;
	
	$engines = array();
	$searchQuery = "";
	
	foreach ($searchProperties as $searchProperty) {
		$paramName = $searchProperty;
		$paramValue = "";
		if($paramName == "name"){
			$paramValue = $Name;
		}
		if($paramName == "language"){
			$paramValue = $Language;
		}
		if($paramName == "category"){
			$paramValue = $Category;
		}
		if($paramName == "country"){
			$paramValue = $Country;
		}
		
		debug_print("$paramName has $paramValue\n");
		if($paramValue != ""){
			if($searchQuery == ""){
				$searchQuery = "?" . $paramName . "=" . $paramValue; 
			}else{
				$searchQuery = $searchQuery . "&" . $paramName . "=" . $paramValue; 
			}
		}
	}
	
	
	$url = "http://mycroftproject.com/search-engines.html$searchQuery";
	debug_print($url);
	$html = file_get_contents($url);
	
	$dom = new DOMDocument;
	// https://stackoverflow.com/questions/9149180/domdocumentloadhtml-error
	libxml_use_internal_errors(true);
	$dom->loadHTML($html);
	libxml_use_internal_errors(false);
	$lis = $dom->getElementsByTagName('li');
	foreach ($lis as $li) {
		$ahrefs = $li ->getElementsByTagName('a');
		foreach($ahrefs as $ahref){
			$title = $ahref -> getAttribute("title");
			if(strpos($title, "Ref:") > -1){
				$line = str_replace("Ref: ", "", $title) . "\n";
				$parts = preg_split('~\((?=[^\(]*$)~', $line);
				
				$engine = new stdClass();
				$engine->name = trim($parts[0]);
				$engine->id = str_replace(")", "", $parts[1]);
				$engines[] = $engine;
			}
		}
	}
	
	return $engines;
}

function get_engine_xml_by_url($url){
	debug_print($url);
	
	
	$xml = @file_get_contents($url);
	if(!isset($xml) || $xml == ""){
		return null;
	}
	
	$dom = new DOMDocument;
	// https://stackoverflow.com/questions/9149180/domdocumentloadhtml-error
	libxml_use_internal_errors(true);
	$dom->loadXML($xml);
	libxml_use_internal_errors(false);
	
	$engine = new stdClass();
	$engine -> ShortName = getProperty($dom, "ShortName");
	$engine -> Description = getProperty($dom, "Description");
	$engine -> Contact = getProperty($dom, "Contact");
	$engine -> Image = getProperty($dom, "Image");
	$engine -> Developer = getProperty($dom, "Developer");
	$engine -> InputEncoding = getProperty($dom, "InputEncoding");
	$engine -> SearchUrl = getPropertyUrlAttribute($dom, "text/html") . getPropertyParamAttributes($dom);
	$engine -> SuggestionsUrl = getPropertyUrlAttribute($dom, "application/x-suggestions+json");
	$engine -> XmlViewUrl = $url;
	$engine -> XmlDownloadUrl = getPropertyUrlAttribute($dom, "application/opensearchdescription+xml");
	$engine -> mozSearchForm = getNSProperty($dom, "SearchForm");
		
	return $engine;
}

function value1($value, $default = ""){
	if(isset($value)){
		return $value;
	}
	
	return $default;
}

function getProperty($dom, $propertyName){
	$tags = $dom -> getElementsByTagName($propertyName);
	$value = null;
	
	foreach ($tags as $tag) {
		$value = $tag -> nodeValue;
		debug_print("$propertyName has $value\n");
		break;
	}
	
	return value1($value);
}

function getNSProperty($dom, $propertyName){
	$tags = $dom->getElementsByTagNameNS("*", $propertyName);
	$value = null;
	
	foreach ($tags as $tag) {
		$value = $tag -> nodeValue;
		debug_print("$propertyName has $value\n");
		break;
	}
	
	return value1($value);
}

function getPropertyUrlAttribute($dom, $propertyAttributeValue){
	return getPropertyAttribute($dom, "Url", "type", $propertyAttributeValue, "template");
}

// http://mycroftproject.com/installos.php/41181/archlinux_ddg.xml
// http://mycroftproject.com/installos.php/43908/track-trace_int.xml
function getPropertyParamAttributes($dom){
	$tags = $dom -> getElementsByTagName("Param");
	$value = "";
	
	foreach ($tags as $tag) {
		$paramName = value1($tag -> getAttribute("name"));
		$paramValue = value1($tag -> getAttribute("value")); 
		debug_print("$paramName has $paramValue\n");
		if($value == ""){
			$value = "?" . $paramName . "=" . $paramValue; 
		}else{
			$value = $value . "&" . $paramName . "=" . $paramValue; 
		}
	}
		
	return $value;
}

function getPropertyAttribute($dom, $tagName, $propertyAttributeName, $propertyAttributeValue, $propertyAttributeToGet){
	$tags = $dom -> getElementsByTagName($tagName);
	$value = null;
	
	foreach ($tags as $tag) {
		$tempValue = $tag -> getAttribute($propertyAttributeName);
		if($tempValue == $propertyAttributeValue){
			$value = $tag -> getAttribute($propertyAttributeToGet);
			debug_print("$propertyAttributeToGet has $value\n");
			break;
		}
	}
	
	return value1($value);
}
