/// Static variables

/// Preferences

function init(){
	initBrowserAction();
}
init();

/// Browser action
/// Neat URL code
function initBrowserAction(){
	var version = browser.runtime.getManifest().version;
	browser.browserAction.setTitle({title: "Mycroft Project Search " + version});
	
	browser.browserAction.onClicked.addListener((tab) => {
		const url = "http://mycroftproject.com/search-engines.html";
		if(tab.url.indexOf(url) == -1){
			notify("Navigating to " + url + ". Please search for a search engine and click the toolbar button again.");
			browser.tabs.create({url: url});
			return;
		}
		browser.tabs.executeScript({file: "./mycroftproject-search.js"});
	});
}

/// Translate Now code
function notify(message){
	browser.notifications.create(message.substring(0, 20),
	{
		type: "basic",
		iconUrl: browser.extension.getURL("icons/mycroftproject-search-64.png"),
		title: "Mycroft Project Search",
		message: message
	});
}
