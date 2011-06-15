<?php
/**
 *   @copyright Copyright (c) 2011 Quality Unit s.r.o.
 *   @author Juraj Simon
 *   @package WpLiveAgentPlugin
 *   @version 1.0.0
 *
 *   Licensed under GPL2
 */

class liveagent_Form_Settings_Buttons extends liveagent_Form_Base {
	private $buttonHelper;
	private $settings;
	private $auth;

	public function __construct(liveagent_Settings $settings) {
		$this->buttonHelper = new liveagent_helper_Buttons();
		$this->settings = $settings;
		$this->auth = new liveagent_Auth();
		parent::__construct(liveagent_Settings::BUTTONS_SETTINGS_PAGE_NAME, 'options.php');
	}

	protected function getTemplateFile() {
		if (!$this->settings->settingsDefinedForConnection() || !$this->connectionSucc) {
			return $this->getTemplatesPath() . 'ButtonsNoAccount.xtpl';
		}
		return $this->getTemplatesPath() . 'Buttons.xtpl';
	}

	protected function getType() {
		return liveagent_Form_Base::TYPE_FORM;
	}

	protected function initForm() {
		parent::initForm();
		if ($this->connectionSucc) {
			$this->parseBlock('login_check_ok', array('connection-ok' => __('Your WordPress installation is succesfully connected with Live Agent', LIVEAGENT_PLUGIN_NAME)));
		} else if (!$this->connectionSucc && $this->settings->settingsDefinedForConnection()) {
			$this->onConnectionFailed();
			return;
		} else {
			return;
		}
		$loginToPanel = __('Login to Admin panel', LIVEAGENT_PLUGIN_NAME);
		try {
			$this->addHtml('la-signup-button', '<a href="'.$this->settings->getLiveAgentUrl() . '/agent?S='.$this->settings->getOwnerSessionId().'" target="_blank" class="nlBigButton">'.$loginToPanel.'</a>');
		} catch (liveagent_Exception_ConnectProblem $e) {
			$this->addHtml('la-signup-button', '<a href="'.$this->settings->getLiveAgentUrl() . '/agent" target="_blank" class="nlBigButton">'.$loginToPanel.'</a>');
		}
		$buttons = $this->settings->getButtonsGridRecordset();
		$this->addHtml('onlinepreview-header', __('Online preview', LIVEAGENT_PLUGIN_NAME));
		$this->addHtml('offlinepreview-header', __('Offline preview', LIVEAGENT_PLUGIN_NAME));
		$this->addHtml('type-header', __('Type', LIVEAGENT_PLUGIN_NAME));
		$this->addHtml('enabled-header', __('Enabled', LIVEAGENT_PLUGIN_NAME));

		$content = '';
		foreach($buttons as $row) {
			$form = new liveagent_Form_Settings_ButtonsTableRow($row->get('id'), $this->buttonHelper->getTypeHumanReadable($row->get('contenttype')));
			$content .= $form->render();
		}
		$this->addHtml('buttons-table', $content);
		$this->form->add('html', 'submit', __('Save', LIVEAGENT_PLUGIN_NAME));
	}

	public function render($toVar = false) {
		parent::render($toVar);
		$this->renderFrames();
	}

	private function renderFrames() {
		if (!$this->connectionSucc) {
			return;
		}
		$out = '<script type="text/javascript"><!--//--><![CDATA[//><!--' . "\n";
		$buttons = $this->settings->getButtonsGridRecordset();
		foreach ($buttons as $row) {
			$out.=$this->buttonHelper->getPreviewCode($row->get('onlinecode'), $row->get('id'), 'ON');
			$out.=$this->buttonHelper->getPreviewCode($row->get('offlinecode'), $row->get('id'), 'OF');
		}
		$out .= '//--><!]]></script>' . "\n";
		echo $out;
	}
}

?>