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
        //do not init anything
    }
}

?>