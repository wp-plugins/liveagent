<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Juraj Simon
 *   @package WpLiveAgentPlugin
 *   @version 1.0.0
 *
 *   Licensed under GPL2
 */

class liveagent_helper_Buttons extends liveagent_Base {
	private $settings;

	public function __construct() {
		$this->settings = new liveagent_Settings();
	}

	public function getTypeHumanReadable($type) {
		switch ($type) {
			case 'F': return 'Float';
			case 'H': return 'HTML';
			case 'I': return 'Image';
			default: return 'Unknown';
		}
	}

	private function escapeCode($code) {
		return str_replace(array('"', "\n"), array("\\\"", "\\\n"),$code);
	}

	public function getPreviewCode($code, $id, $postfix) {
		return 'setHtml(document.getElementById("'.$id.'_'.$postfix.'"), "'.$this->escapeCode($code).'");' . "\n";
	}

	public function getButtonsGridData() {
		$request = new La_Rpc_Request('La_Button_ButtonTable', 'getRows');
		$request->setUrl($this->getRemoteApiUrl() . '?S=' . $this->settings->getOwnerSessionId());

		try {
			$request->sendNow();
		} catch (Exception $e) {
			$this->_log(__('Unable to obtain button codes'));
			return array();
		}
		$grid = new La_Data_Grid();
		$grid->loadFromObject($request->getStdResponse());
		return $grid->getRecordset();
	}

	private function getField($buttonId, $code) {
		foreach ($this->getButtonsGridData() as $row) {
			if ($row->get('id') == $buttonId) {
				return $row->get($code);
			}
		}
		return '';
	}

	public function getType($buttonId) {
		return $this->getField($buttonId, 'contenttype');
	}

	public function getOnlineCode($buttonId) {
		return $this->getField($buttonId, 'onlinecode');
	}

	public function getOfflineCode($buttonId) {
		return $this->getField($buttonId, 'offlinecode');
	}

	public function getIntegrationCode($buttonId) {
		return '<img src="'.$this->getRemotePixUrl().'" onLoad="LiveAgentTracker.createButton(\''.$buttonId.'\', this);"/>
		';	
	}

	public function getIntegrationCodeForEnabledFloatButtons() {
		$config = get_option(liveagent_Settings::BUTTONS_CONFIGURATION_SETTING_NAME);
		$code = '';
		foreach ($config as $key => $value) {
			if ($value == 'true' && $this->getType($key) == 'F') {
				echo $this->getIntegrationCode($key);
			}
		}
		return $code;
	}
}
?>