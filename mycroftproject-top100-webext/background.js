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
	browser.browserAction.setTitle({title: "Mycroft Project Top 100 " + version});
	
	browser.browserAction.onClicked.addListener((tab) => {
		const url = "http://mycroftproject.com/dlstats.html";
		if(tab.url != url){
			notify("Navigating to " + url + ". Please click the toolbar button again.");
			browser.tabs.create({url: url});
			return;
		}
		browser.tabs.executeScript({file: "./mycroftproject-top100.js"});
	});
}

/// Translate Now code
function notify(message){
	browser.notifications.create(message.substring(0, 20),
	{
		type: "basic",
		iconUrl: browser.extension.getURL("icons/mycroftproject-top100-64.png"),
		title: "Mycroft Project Top 100",
		message: message
	});
}
