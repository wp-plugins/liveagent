<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Juraj Simon
 *   @package WpLiveAgentPlugin
 *   @version 1.0.0
 *
 *   Licensed under GPL2
 */

class liveagent_helper_Buttons extends liveagent_helper_Grid {

    public function __construct() {
        parent::__construct('La_Button_ButtonTable');
    }

    public function getTypeHumanReadable($type) {
        switch ($type) {
            case 'F': return 'Float';
            case 'H': return 'HTML';
            case 'I': return 'Image';
            default: return 'Unknown';
        }
    }
    
    private function enableButton($buttonId) {
        $settings = new liveagent_Settings();
        if ($settings->buttonIsEnabled($buttonId)) {
            return;    
        }
        $value = get_option(liveagent_Settings::BUTTONS_CONFIGURATION_SETTING_NAME);
        if ($value == '' || $value === null) {
            $value = array($buttonId => 'true');
        }
        if (!array_key_exists($buttonId, $value)) {
            $value[$buttonId] = 'true';
        }
        update_option(liveagent_Settings::BUTTONS_CONFIGURATION_SETTING_NAME, $value);
    }
    
    public function enableDefaultButton(){
        //b2222222 was id of old default button. from v. 2.0.53.2+ it will onlu button1 
        $this->enableButton('b2222222');
        $this->enableButton('button1');
    }

    public function isSomeButtonEnabled() {
        $buttons = get_option(liveagent_Settings::BUTTONS_CONFIGURATION_SETTING_NAME);
        if ($buttons == '' || $buttons === null || (is_array($buttons) && count($buttons) == 0)) {
            return false;
        }
        foreach ($buttons as $buttonid => $enabled) {
            if ($enabled === true || $enabled=='true') {
                return true;
            }
        }
        return false;
    }

    private function escapeCode($code) {
        return str_replace(array('"', "\n"), array("\\\"", "\\\n"),$code);
    }

    public function getPreviewCode($code, $id, $postfix) {
        return 'setHtml(document.getElementById("'.$id.'_'.$postfix.'"), "'.$this->escapeCode($code).'");' . "\n";
    }

    private function getField($buttonId, $code) {
        foreach ($this->getData() as $row) {
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
        return '<img src="'.$this->getRemotePixUrl().'" onLoad="LiveAgentTracker.createButton(\''.$buttonId.'\', this);"/>' . "\n";
    }

    public function getIntegrationCodeForEnabledFloatButtons() {
        $config = get_option(liveagent_Settings::BUTTONS_CONFIGURATION_SETTING_NAME);
        $code = '';
        if (!is_array($config)) {
            return '';
        }
        foreach ($config as $key => $value) {
            if ($value == 'true' && $this->getType($key) == 'F') {
                echo $this->getIntegrationCode($key);
            }
        }
        return $code;
    }
}
?>