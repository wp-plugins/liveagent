/*
 Plugin Name: Live Agent
 Plugin URI: http://www.qualityunit.com/liveagent
 Description: Plugin that enable integration with Live Agent
 Author: QualityUnit
 Version: 1.0.0
 Author URI: http://www.qualityunit.com
 License: GPL2
 */

function setHtml(iframe,html) {
	var doc = iframe.document;
    if (iframe.contentDocument) {
    	doc = iframe.contentDocument; // For NS6
    } else if(iframe.contentWindow) {
    	doc = iframe.contentWindow.document; // For IE5.5 and IE6
    }
    // Put the content in the iframe
    doc.open();
    doc.writeln(html);
    doc.close();
}