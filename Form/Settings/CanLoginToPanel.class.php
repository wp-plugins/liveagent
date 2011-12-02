<?php
/**
 *   @copyright Copyright (c) 2011 Quality Unit s.r.o.
 *   @author Juraj Simon
 *   @package WpLiveAgentPlugin
 *   @version 1.0.0
 *
 *   Licensed under GPL2
 */

abstract class liveagent_Form_Settings_CanLoginToPanel extends liveagent_Form_Base {
    /**
     * @var liveagent_Settings
     */    
    protected $settings;
    
    protected function addSignupButton() {
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
    }
}

?>