<?php
/**
 *   @copyright Copyright (c) 2011 Quality Unit s.r.o.
 *   @author Juraj Simon
 *   @package WpLiveAgentPlugin
 *   @version 1.0.0
 *
 *   Licensed under GPL2
 */

class liveagent_Form_Validator_Signup extends liveagent_Form_Validator_Base {
    public function isValid() {
        if (!isset($this->fields[liveagent_Settings::LA_FULL_NAME]) ||
                trim($this->fields[liveagent_Settings::LA_FULL_NAME]) == null) {
            $this->addError(__('You must enter valid full name', LIVEAGENT_PLUGIN_NAME));
        }
        if (!isset($this->fields[liveagent_Settings::LA_OWNER_EMAIL_SETTING_NAME]) || 
                trim($this->fields[liveagent_Settings::LA_OWNER_EMAIL_SETTING_NAME]) == null ||
                !is_email($this->fields[liveagent_Settings::LA_OWNER_EMAIL_SETTING_NAME])) {
            $this->addError(__('You must enter valid email address', LIVEAGENT_PLUGIN_NAME));
        }
        if (!isset($this->fields[liveagent_Settings::LA_URL_SETTING_NAME]) || 
                trim($this->fields[liveagent_Settings::LA_URL_SETTING_NAME]) == null) {
            $this->addError(__('You must enter valid domain name', LIVEAGENT_PLUGIN_NAME));
        }
        return $this->valid;
    }
}

?>