<?php
/**
 *   @copyright Copyright (c) 2011 Quality Unit s.r.o.
 *   @author Juraj Simon
 *   @package WpLiveAgentPlugin
 *   @version 1.0.0
 *
 *   Licensed under GPL2
 */

class liveagent_Form_Settings_ButtonCode extends liveagent_Form_Settings_CanLoginToPanel {
	private $auth;

	public function __construct(liveagent_Settings $settings, liveagent_Auth $auth) {
		$this->settings = $settings;
		$this->auth = $auth;
		parent::__construct(liveagent_Settings::BUTTONS_SETTINGS_PAGE_NAME, 'options.php');
	}

	protected function getTemplateFile() {
		return $this->getTemplatesPath() . 'ButtonCode.xtpl';
	}

	protected function getType() {
		return liveagent_Form_Base::TYPE_FORM;
	}
	
	protected function getOption($name) {
	    if ($name == liveagent_Settings::BUTTON_CODE) {
	        return $this->settings->getButtonCode();
	    }
	    return parent::getOption($name);
	}	

	protected function initForm() {
		parent::initForm();
		if ($this->connectionSucc) {
			$this->parseBlock('login_check_ok', array(
			        'connection-ok' => __('Your WordPress installation is succesfully connected with Live Agent', LIVEAGENT_PLUGIN_NAME),
			        'ok-icon' => '<div style="display:inline;"><img class="InfoIcon" src="'.$this->getImgUrl().'ok.png" /></div>' ));
		} else if (!$this->connectionSucc && $this->settings->settingsDefinedForConnection()) {
			$this->onConnectionFailed();
		} else {
			return;
		}
		$loginToPanel = __('Login to Admin panel', LIVEAGENT_PLUGIN_NAME);
		try {
			$authToken = $this->settings->getOwnerAuthToken();
            if ($authToken == liveagent_Settings::NO_AUTH_TOKEN) {
                $this->addHtml('la-signup-button', '<a href="'.$this->settings->getLiveAgentUrl() . '/agent?S='.$this->settings->getOwnerSessionId().'" target="_blank" class="nlBigButton">'.$loginToPanel.'</a>');
            } else {
                $this->addHtml('la-signup-button', '<a href="'.$this->settings->getLiveAgentUrl() . '/agent?AuthToken='.$authToken.'" target="_blank" class="nlBigButton">'.$loginToPanel.'</a>');
            }
		} catch (liveagent_Exception_ConnectProblem $e) {
			$this->addHtml('la-signup-button', '<a href="'.$this->settings->getLiveAgentUrl() . '/agent" target="_blank" class="nlBigButton">'.$loginToPanel.'</a>');
		}

		$this->addTextArea(liveagent_Settings::BUTTON_CODE, 100, 4,  'nlTextArea ButtonCode text');
		$this->addHtml('bulb-icon', '<div style="display:inline;"><img class="InfoIcon" src="'.$this->getImgUrl().'bulb.png" /></div>');
		$this->addSignupButton();
		$this->form->add('html', 'submit', __('Save', LIVEAGENT_PLUGIN_NAME));
	}
}

?>