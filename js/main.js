/*
 Plugin Name: Live Agent
 Plugin URI: http://www.qualityunit.com/liveagent
 Description: Plugin that enable integration with Live Agent
 Author: QualityUnit
 Version: 1.0.0
 Author URI: http://www.qualityunit.com
 License: GPL2
 */

var liveagent_timerId = null;

//function setHtml(iframe, html) {
//	var doc = iframe.document;
//	if (iframe.contentDocument) {
//		doc = iframe.contentDocument; // For NS6
//	} else if (iframe.contentWindow) {
//		doc = iframe.contentWindow.document; // For IE5.5 and IE6
//	}
//	// Put the content in the iframe
//	doc.open();
//	doc.writeln(html);
//	doc.close();
//}

// include our jQuery in non-conflict mode
// var jQuery = jQuery.noConflict();

//function runAction(name, postdata, attributes) {
//	if (!attributes) {
//		var attributes;
//	}
//	var link = jQuery(location);
//	jQuery
//			.post(
//					link.attr("href"),
//					{
//						action : name,
//						data : postdata,
//						att : attributes
//					},
//					function(data) {
//						data = jQuery.parseJSON(data);
//						if (data.result != '1') {
//							if (data.message != undefined) {
//								alert(data.message);
//							}
//							if (data.runonfailure != undefined) {
//								window[data.runonfailure]();
//							}
//							return data.result;
//						}
//						if ((data.runfunction != undefined
//								&& data.replaceform != undefined && data.runafter == undefined)
//								|| (data.runfunction != undefined && data.replaceform == undefined)) {
//							window[data.runfunction](data);
//						}
//						if (data.replaceform == undefined) {
//							return;
//						}
//						var div = jQuery('form[name=' + data.replaceform + ']');
//						if (div.length == 0) {
//							div = jQuery('div[name=' + data.replaceform + ']');
//						}
//						div.fadeOut('fast', function() {
//							var parentDiv = div.parent();
//							div.remove();
//							odv = jQuery('<div>' + data.dialog + '</div>');
//							odv.appendTo(parentDiv);
//							parentDiv.fadeIn('fast');
//						});
//						if (data.runfunction != undefined
//								&& data.runafter != undefined) {
//							window[data.runfunction](data);
//						}
//
//					});
//	return false;
//}

function onSignupFail() {
	jQuery('a[name=liveagent_create_account_button]').unbind('click');
	jQuery('a[name=liveagent_create_account_button]').click(
			function() {
				onSignupSubmit(document.getElementById('la-full-name').value,
						document.getElementById('la-owner-email').value,
						document.getElementById('la-url').value);
			});
	jQuery('a[name=liveagent_create_account_button]').html(
			"Create your account");
}

function onResponseRecieved(data) {
	data = jQuery.parseJSON(data);
	if (data.result != '1') {
		if (data.message != undefined) {
			alert(data.message);
		}
		if (data.runonfailure != undefined) {
			window[data.runonfailure]();
		}
		return data.result;
	}
	if ((data.runfunction != undefined && data.replaceform != undefined && data.runafter == undefined)
			|| (data.runfunction != undefined && data.replaceform == undefined)) {
		window[data.runfunction](data);
	}
	if (data.replaceform == undefined) {
		return;
	}
	var div = jQuery('form[name=' + data.replaceform + ']');
	if (div.length == 0) {
		div = jQuery('div[name=' + data.replaceform + ']');
	}
	div.fadeOut('fast', function() {
		var parentDiv = div.parent();
		div.remove();
		odv = jQuery('<div>' + data.dialog + '</div>');
		odv.appendTo(parentDiv);
		parentDiv.fadeIn('fast');
	});
	if (data.runfunction != undefined && data.runafter != undefined) {
		window[data.runfunction](data);
	}
}

function onSignupCancel() {
	var data = {
		action : 'lasignupcancel',
		data : ''
	};
	jQuery.post(ajaxurl, data, function(response) {
		onResponseRecieved(response);
	});
}

function onPingSuccessfull(data) {
	if (liveagent_timerId == null) {
		return;
	}
	clearTimeout(liveagent_timerId);
	liveagent_timerId = null;
	setTimeout("window.location.reload()", 5000);
}

function onSignupWait(data) {
	doPing(data.domain, true);
	runWaitingStatusChanger();
}

function doPing(domain, postpondPing) {
	var attributes = {
		"domain" : domain
	};
	if (!postpondPing) {		
		var data = {
				action : 'laping',
				'domain' : domain
			};
			jQuery.post(ajaxurl, data, function(response) {
				onResponseRecieved(response);
			});
	}
	liveagent_timerId = setTimeout("doPing('" + domain + "',false)", 8000);
}

function runWaitingStatusChanger() {
	setTimeout(
			"jQuery('div[name=liveagent_wait_status]').html('Initializing...');",
			10);
	var timer = 3000;
	var percentage = 2;
	for (i = 0; i < 49; i++) {
		setTimeout(
				"jQuery('div[name=liveagent_wait_status]').html('Installing "
						+ percentage + "% ...');", timer);
		timer += 1000;
		percentage += 2;
	}
	setTimeout(
			"jQuery('div[name=liveagent_wait_status]').html('Just few more seconds...');",
			timer);
}

function checkSignupParams(name, email, domain) {
	if (name == '') {
		alert('Name can not be empty. Please enter your full name.')
		return false;
	}
	if (email == '') {
		alert('Email can not be empty. Please enter your email address.')
		return false;
	}
	if (domain == '') {
		alert('Domain can not be empty. Please enter some domain name.')
		return false;
	}
	return true;
}

function onSignupSubmit(name, email, domain) {
	if (!checkSignupParams(name, email, domain)) {
		return;
	}
//	var attributes = {
//		"name" : name,
//		"email" : email,
//		"domain" : domain
//	};

	var data = {
		'action' : 'lasignupsubmit',
		'name' : name,
		"email" : email,
		"domain" : domain
	};
	jQuery.post(ajaxurl, data, function(response) {
		onResponseRecieved(response);
	});

	jQuery('a[name=liveagent_create_account_button]').html("Please wait...");
	jQuery('a[name=liveagent_create_account_button]').removeAttr('onclick');
	jQuery('a[name=liveagent_create_account_button]').unbind('click');
	jQuery('a[name=liveagent_create_account_button]').click(function(e) {
		e.preventDefault()
	});
}

function onSignupRestart() {
	var data = {
		action : 'lasignuprestart',
		data : ''
	};
	jQuery.post(ajaxurl, data, function(response) {
		onResponseRecieved(response);
	});
}