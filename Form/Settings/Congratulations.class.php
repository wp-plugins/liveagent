<?php
/**
 *   @copyright Copyright (c) 2011 Quality Unit s.r.o.
 *   @author Juraj Simon
 *   @package WpLiveAgentPlugin
 *   @version 1.0.0
 *
 *   Licensed under GPL2
 */

class liveagent_Form_Settings_Congratulations extends liveagent_Form_Settings_CanLoginToPanel {
    public function __construct(liveagent_Settings $settings) {
        $this->settings = $settings;
        parent::__construct(liveagent_Settings::SIGNUP_WAIT_SETTINGS_PAGE_NAME);
    }

    protected function getTemplateFile() {
        return $this->getTemplatesPath() . 'Congratulations.xtpl';
    }

    protected function getType() {
        return liveagent_Form_Base::TYPE_TEMPLATE;
    }

    protected function initForm() {
        $this->addSignupButton();
    }
}

?>