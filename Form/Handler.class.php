<?php
/**
 *   @copyright Copyright (c) 2012 Quality Unit s.r.o.
 *   @author Juraj Simon
 *   @package WpLiveAgentPlugin
 *   @version 1.0.0
 *
 *   Licensed under GPL2
 */

class liveagent_Form_Handler extends liveagent_Base {
    private $settings;
    private $auth;
    
    public function __construct(liveagent_Settings $settings, liveagent_Auth $auth) {
        $this->auth = $auth;
        $this->settings = $settings;
    }
    
    public function printGeneralConfigPage() {
        $form = null;
        if ($this->getAccountStatus() == self::ACCOUNT_STATUS_NOTSET) {
            $form = new liveagent_Form_Settings_Signup($this->settings, $this->auth);
        } else {
            $form = new liveagent_Form_Settings_Account($this->settings, $this->auth);
        }
        $form->render();
    }
        
    public function printButtonsConfigPage() {
        $form = new liveagent_Form_Settings_ButtonCode($this->settings, $this->auth);
        $form->render();
    }
}
?>