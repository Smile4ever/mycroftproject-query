searchengines-data-api
==========

searchengines-data-api is a searchengines API written in PHP. It fetches the searchengines data from Mycroft Project. The data this api returns can be used in any application by sending a HTTP request to searchengines-data-api.

searchengines-data-api supports reading a subset of the OpenSearchDescription format. Compatiblity table:

| JSON property  | OpenSearchDescription                            | Notes                                                                      |
| -------------- |--------------------------------------------------| ---------------------------------------------------------------------------|
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

All <lt;Param name= properties are supported. They are added to SearchUrl automatically.

Examples
========

searchengines-data-api can be used like this from the (Linux) command line:

	NAME=google LIMIT=25 php index.php
	
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

A key API parameter is optional when at least one other key API parameter is used. Otherwise, it is required. This means at least one of these "key" API parameters is required: shortname/name, language, category or country.

* limit (optional, can be any positive number) (default = no limit)
* onlyvalid (optional, can be true or false) (default is false)

If onlyvalid=true, it skips engines that could not be parsed properly. This is unrelated to the verified status on Mycroft Project. Engines that could not be parsed do not contain useful properties except for XmlViewUrl.

| API Parameter    | Type                  | Example values                                |
| ---------------- |-----------------------| ----------------------------------------------|
| shortname / name | Key parameter         | "DuckDuckGo", "Google", "GitHub"              |
| language         | Key parameter         | "af", "nl", "en"                              |
| category         | Key parameter         | A lowercase string value, see below           |
| country          | Key parameter         | "BE" (2 letter country code)                  |
| limit            | Optional parameter    | "25"                                          |
| onlyvalid        | Optional parameter    | "true" or "false"                             |

Possibilities for "category":

| Category    			     		 | Category          			   | Category                             |
| -----------------------------------|---------------------------------|--------------------------------------|
| "shopping/auction"				 | "shopping/classifieds"   	   | "computer" 			 		   	  |
| "health"				    		 | "reference"			    	   | "internet/mozilla"	 		   	      | 
| "arts & media" 		    		 | "news" 				    	   | "computer/programming" 		   	  |
| "shopping" 			    		 | "news/technology"		       | "major engines"		 		  	  |
| "file sharing" 		    		 | "education"			    	   | "society/religion"	 		   	      | 
| "kids & teens" 		    		 | "arts & media/music"	   		   | "business"				   	          |
| "society" 			 			 | "undefined"			   	  	   | "dictionaries"				  	      |
| "recreation" 						 | "recreation/travel"	   	  	   | "games/roleplaying"			   	  |
| "arts & media/literature" 		 | "society/blogs"		   	  	   | "directories - address,phone..." 	  |
| "internet/tools" 		 			 | "games/video games"	    	   | "arts & media/film, tv & video"  	  |
| "news/weather"		 			 | "arts & media/animation"  	   | "arts & media/images"  	   	      |
| "business/jobs"		    		 | "libraries"			    	   | "shopping/books"	       	   		  |
| "shopping/music"]		 			 | "shopping/electronics"    	   | "shopping/dvd & video"	       	      |
| "education/universities"  		 | "shopping/computer"	    	   | "reference/maps"		       	      |
| "education/journals"				 | "shopping/home & diy"	       | "shopping/price comparison"	   	  |
| "dictionaries/translation"		 | "games"		   		   	  	   | "society/social network"	       	  |
| "recreation/sport" 			     | "recreation/food & drink" 	   | "internet"	       	                  |
| "computer/linux"				     | "internet/programming"  	  	   | "computer/software"			   	  |
| "other general engines"		     | "reference/encyclopedias"	   | "other general engines/custom(ised)" |
| "other general engines/metasearch" | "other general engines/charity" | "society/government"			      |

Attention:
* Use CAPITAL LETTERS for the parameter names when calling searchengines-data-api from the command line, e.g. "NAME" or "LIMIT"