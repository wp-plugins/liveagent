<?php
/**
 *   @copyright Copyright (c) 2011 Quality Unit s.r.o.
 *   @author Juraj Simon
 *   @package WpLiveAgentPlugin
 *   @version 1.0.0
 *
 *   Licensed under GPL2
 */

class liveagent_Form_Settings_SignupWait extends liveagent_Form_Base {
    public function __construct() {
        parent::__construct(liveagent_Settings::SIGNUP_WAIT_SETTINGS_PAGE_NAME);
    }

    protected function getTemplateFile() {
        return $this->getTemplatesPath() . 'AccountWait.xtpl';
    }

    protected function getType() {
        return liveagent_Form_Base::TYPE_TEMPLATE;
    }

    protected function initForm() {
//        $this->addTextBox('la-full-name', null, 'nlInput text');        
//        $this->addTextBox(liveagent_Settings::LA_OWNER_EMAIL_SETTING_NAME, null, 'nlInput text');
//        $this->addTextBox(liveagent_Settings::LA_URL_SETTING_NAME, 5, 'nlInput text');
//        $this->form->add('html', 'submit', __('Create your account', LIVEAGENT_PLUGIN_NAME));
    }
}

?>