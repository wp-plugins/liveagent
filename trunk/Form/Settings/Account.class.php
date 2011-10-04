<?php
/**
 *   @copyright Copyright (c) 2011 Quality Unit s.r.o.
 *   @author Juraj Simon
 *   @package WpLiveAgentPlugin
 *   @version 1.0.0
 *
 *   Licensed under GPL2
 */

class liveagent_Form_Settings_Account extends liveagent_Form_Settings_CanLoginToPanel {
    /**
     * @var liveagent_Auth
     */
    private $auth;

    public function __construct(liveagent_Settings $settings, liveagent_Auth $auth) {
        $this->settings = $settings;
        $this->auth = $auth;

        parent::__construct(liveagent_Settings::GENERAL_SETTINGS_PAGE_NAME, 'options.php');
    }

    protected function getTemplateFile() {
        return $this->getTemplatesPath() . 'AccountSettings.xtpl';
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
        }
        $this->addTextBox(liveagent_Settings::LA_URL_SETTING_NAME, null, 'nlInput text');
        $this->addTextBox(liveagent_Settings::LA_OWNER_EMAIL_SETTING_NAME, null, 'nlInput text');
        $this->addPassword(liveagent_Settings::LA_OWNER_PASSWORD_SETTING_NAME, null, 'nlInput text');
        $this->addSignupButton();
        $this->form->add('html', 'submit', __('Save Account Settings', LIVEAGENT_PLUGIN_NAME));
    }
}

?>