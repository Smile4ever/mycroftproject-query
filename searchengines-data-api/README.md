searchengines-data-api
==========

searchengines-data-api is a searchengines API written in PHP. It fetches the searchengines data from Mycroft Project. The data this api returns can be used in any application by sending a HTTP request to searchengines-data-api.

searchengines-data-api supports reading a subset of the OpenSearchDescription format. Compatiblity table:

| JSON property  | OpenSearchDescription                            | Notes                                                                      |
| -------------- |:------------------------------------------------:| --------------------------------------------------------------------------:|
| ShortName      | ShortName                                        |                                                                            |
| Description    | Description                                      |                                                                            |
| (none)         | Tags                                             | Not implemented (supported on Mycroft Project?)                            |
| Contact        | Contact                                          |                                                                            |
| (none)         | Url                                              | Partially implemented using parameters ending in "Url"                     |
| (none)         | LongName                                         | Not implemented (supported on Mycroft Project?)                            |
| Image          | Image                                            | width and height properties not supported                                  |
| (none)         | Query                                            | Not implemented (supported on Mycroft Project?)                            |
| Developer      | Developer                                        |                                                                            |
| (none)         | Attribution                                      | Not implemented (supported on Mycroft Project?)                            |
| (none)         | SyndicationRight                                 | Not implemented (supported on Mycroft Project?)                            |
| (none)         | AdultContent                                     | Not implemented (supported on Mycroft Project?)                            |
| (none)         | Language                                         | Not implemented (supported on Mycroft Project?)                            |
| (none)         | OutputEncoding                                   | Not implemented (supported on Mycroft Project?)                            |
| InputEncoding  | InputEncoding                                    |                                                                            |
| SearchUrl      | Url type="text/html"                             | Example value: http://ecosia.org/maps.php?q={searchTerms}&addon=opensearch |
| SuggestionsUrl | Url type="application/x-suggestions+json"        | Partially implemented, see below                                           | 
| XmlViewUrl     | (none)                                           | Link to the XML file (constructed URL)                                     |
| XmlDownloadUrl | Url type="application/opensearchdescription+xml" | Link to the XML file (rel=self from XML)                                   |
| mozSearchForm  | (none)                                           | moz:SearchForm. Example value: http://ecosia.org/                          |

Every implemented property that is not present in the XML description, will return an empty string. 

Every unimplemented property is not available. Future implemented properties could be:
* &lt;Url type="application/atom+xml" template="http://example.com/?q={searchTerms}&amp;pw={startPage?}&amp;format=atom"/>
* &lt;Url type="application/rss+xml" template="http://example.com/?q={searchTerms}&amp;pw={startPage?}&amp;format=rss"/>
* &lt;Url type="application/json" rel="suggestions" template="http://example.com/suggest?q={searchTerms}" />

Feel free to open an issue to ask for a specific property. You will need to prove the property is actually used on Mycroft Project.

[Click here for more information about the OpenSearchDescription format](http://www.opensearch.org/Specifications/OpenSearch/1.1)

Examples
========

searchengines-data-api can be used like this from the (Linux) command line:

	SHORTNAME=google LIMIT=25 php index.php
	
searchengines-data-api can be used like this using the HTTP api interface:
	
	http://hugsmile.eu/api/searchengines-data/?shortname=google&limit=25

This request outputs an array of JSON objects:

	[
	  {
		"ShortName": "YouTube (Google Videos)",
		"Description": "Search Google Videos for YouTube Only videos",
		"Contact": "",
		"Image": "http://mycroftproject.com/updateos.php/id0/youtube_googlevideo.ico",
		"Developer": "Michael",
		"InputEncoding": "UTF-8",
		"SearchUrl": "https://www.google.com/search?tbm=vid&as_sitesearch=youtube.com&q={searchTerms}",
		"SuggestionsUrl": "https://suggestqueries.google.com/complete/search?output=firefox&client=firefox&hl=en&ds=v&q={searchTerms}",
		"XmlViewUrl": "http://mycroftproject.com/installos.php/65586/YouTube.xml",
		"XmlDownloadUrl": "http://mycroftproject.com/updateos.php/id0/youtube_googlevideo.xml",
		"mozSearchForm": "https://www.google.com/advanced_video_search?as_sitesearch=youtube.com"
	  },
	  {
		"ShortName": "UT Youtube & Wikipedia Search via Google",
		"Description": "Easy search results for UT website big information site",
		"Contact": "",
		"Image": "http://mycroftproject.com/updateos.php/id0/ut_search.png",
		"Developer": "Duman",
		"InputEncoding": "UTF-8",
		"SearchUrl": "http://www.google.com.tr/search?hl=tr&q={searchTerms}+site:unitedtowers.com",
		"SuggestionsUrl": "",
		"XmlViewUrl": "http://mycroftproject.com/installos.php/45667/UT%20Youtube &amp; Wikipedia Search via Google.xml",
		"XmlDownloadUrl": "http://mycroftproject.com/updateos.php/id0/ut_search.xml",
		"mozSearchForm": "http://www.unitedtowers.com"
	  },
	  {
		"ShortName": "TF Facebook & MySpace Blog via Google Search",
		"Description": "Talk fusion fast search system",
		"Contact": "",
		"Image": "http://mycroftproject.com/updateos.php/id0/tf_search.png",
		"Developer": "Dumanov",
		"InputEncoding": "UTF-8",
		"SearchUrl": "http://www.google.com/search?hl=en&q={searchTerms}+site:1229153.talkfusion.com",
		"SuggestionsUrl": "",
		"XmlViewUrl": "http://mycroftproject.com/installos.php/46923/TF%20Facebook &amp; MySpace Blog via Google Search.xml",
		"XmlDownloadUrl": "http://mycroftproject.com/updateos.php/id0/tf_search.xml",
		"mozSearchForm": "http://1229153.talkfusion.com/feedback/join/"
	  }
	]

Parameters and values
=====================

Passing input values:
* shortname (required)
* limit (optional, can be any positive number) (default = no limit)
* onlyvalid (optional, can be true or false) (default is false)

Example values for shortname:
* "DuckDuckGo"
* "Google"
* "GitHub"

Example values for limit:
* (none)
* 100

Attention:
* Use CAPITAL LETTERS for the parameter names when calling searchengines-data-api from the command line, e.g. "SHORTNAME" or "LIMIT"
