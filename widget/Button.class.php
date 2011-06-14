<?php
/*
 Plugin Name: Post Affiliate Pro
 Plugin URI: http://www.qualityunit.com/#
 Description: Plugin that enable user signup integration integration with Post Affiliate Pro
 Author: QualityUnit
 Version: 1.1.0
 Author URI: http://www.qualityunit.com
 License: GPL2
 */

class liveagent_widget_Button extends liveagent_Base {
	const WIDGET_PREFIX = 'live_agent_button_';
	
	private $buttonHelper;
	private $id;
	private $onlinecode;
	private $offlinecode;

	function __construct($id, $onlinecode, $offlinecode) {
		$this->id = $id;
		$this->onlinecode = $onlinecode;
		$this->offlinecode = $offlinecode;
		$this->buttonHelper = new liveagent_helper_Buttons();
	}

	public function showWidget($args) {
		extract($args);
		echo $this->buttonHelper->getIntegrationCode($this->id);
	}
}
?>