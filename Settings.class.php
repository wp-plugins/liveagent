<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Juraj Simon
 *   @package WpLiveAgentPlugin
 *   @version 1.0.0
 *
 *   Licensed under GPL2
 */

class liveagent_Settings {
    const CACHE_VALIDITY = 600;

    //internal settings
    const INTERNAL_SETTINGS = 'la-settings_internal-settings';
    const OWNER_SESSIONID = 'la-settings_owner-sessionid';
    const OWNER_AUTHTOKEN = 'la-settings_owner-authtoken';
    const BUTTONS_DATA = 'la-settings_buttonsdata';    
    const ACCOUNT_STATUS = 'la-settings_accountstatus';

    //general page
    const GENERAL_SETTINGS_PAGE_NAME = 'la-config-general-page';
    const SIGNUP_SETTINGS_PAGE_NAME = 'la-config-signup-page';
    const SIGNUP_WAIT_SETTINGS_PAGE_NAME = 'la-config-signup-wait-page';

    const LA_URL_SETTING_NAME = 'la-url';
    const LA_OWNER_EMAIL_SETTING_NAME = 'la-owner-email';
    const LA_OWNER_PASSWORD_SETTING_NAME = 'la-owner-password';
    const GENERAL_SETTINGS_PAGE_STATE_SETTING_NAME = 'general-settings-state';

    //buttons options
    const BUTTONS_SETTINGS_PAGE_NAME = 'la-config-buttons-page';
    const BUTTONS_CONFIGURATION_SETTING_NAME = 'la-buttons-configuration';
    const BUTTON_CODE = 'la-buttons_buttoncode';

    const NO_AUTH_TOKEN = 'no_auth_token';

    public function initSettingsForAdminPanel() {
        register_setting(self::GENERAL_SETTINGS_PAGE_NAME, self::LA_URL_SETTING_NAME, array($this, 'sanitizeUrl'));
        register_setting(self::GENERAL_SETTINGS_PAGE_NAME, self::LA_OWNER_EMAIL_SETTING_NAME);
        register_setting(self::GENERAL_SETTINGS_PAGE_NAME, self::LA_OWNER_PASSWORD_SETTING_NAME);
        register_setting(self::BUTTONS_SETTINGS_PAGE_NAME, self::BUTTONS_CONFIGURATION_SETTING_NAME);
        register_setting(self::BUTTONS_SETTINGS_PAGE_NAME, self::BUTTON_CODE);
        register_setting(self::INTERNAL_SETTINGS, self::OWNER_SESSIONID);
        register_setting(self::INTERNAL_SETTINGS, self::OWNER_AUTHTOKEN);
        register_setting(self::INTERNAL_SETTINGS, self::BUTTONS_DATA);        
        register_setting(self::INTERNAL_SETTINGS, self::ACCOUNT_STATUS);
    }

    public function sanitizeUrl($url) {
        if (stripos($url, 'http://')!==false || stripos($url, 'https://')!==false) {
            return $url;
        }
        return 'http://' . $url;
    }    

    public function clearCache() {
        update_option(self::OWNER_SESSIONID, '');
        update_option(self::OWNER_AUTHTOKEN, '');
        update_option(self::BUTTONS_DATA, '');
    }
    
    private function setSetting($code, $settingValue) {
        $settings = get_option($code);
        if ($settings != '') {
            update_option($code, $settingValue);
        } else {
            add_option($code, $settingValue);
            update_option($code, $settingValue);
        }
    }

    private function setCachedSetting($code, $value) {
        $settings = get_option($code);
        $settingValue = $value . "||" . time();
        if ($settings != '') {
            update_option($code, $settingValue);
        } else {            
            add_option($code, $settingValue);
            update_option($code, $settingValue);
        }
    }

    private function getCachedSetting($code) {
        $settings = get_option($code);
        if ($settings == null || trim($settings) == '') {
            throw new liveagent_Exception_SettingNotValid(__(sprintf('Setting %s not defined yet.', $code)));
        }
        $settings = explode("||", $settings, 2);
        $validTo = $settings[1] + self::CACHE_VALIDITY + 0;
        if ($validTo > time()) {
            return $settings[0];
        } else {
            if (array_key_exists('time', $settings)) {
                $message = __(sprintf('Setting\'s %s validity exceeded: %s', $code, $settings['time']));
            } else {
                $message = __(sprintf('Setting\'s %s validity exceeded: unknown', $code));
            }
                throw new liveagent_Exception_SettingNotValid($message);
        }
    }

    public function getOwnerSessionId() {
        try {
            return $this->getCachedSetting(self::OWNER_SESSIONID);
        } catch (liveagent_Exception_SettingNotValid $e) {            
            return $this->login();
        }
    }

    public function getOwnerAuthToken() {
        try {
            return $this->getCachedSetting(self::OWNER_AUTHTOKEN);
        } catch (liveagent_Exception_SettingNotValid $e) {
            $this->login();
        }
        try {
            return $this->getCachedSetting(self::OWNER_AUTHTOKEN);
        } catch (liveagent_Exception_SettingNotValid $e) {
            $this->setCachedSetting(self::OWNER_AUTHTOKEN, self::NO_AUTH_TOKEN);
            return self::NO_AUTH_TOKEN;
        }
    }

    private function login() {
        $auth = new liveagent_Auth();
        $loginData = $auth->LoginAndGetLoginData();
        try {
            $sessionId = $loginData->getValue('session');
            $this->setCachedSetting(self::OWNER_SESSIONID, $sessionId);
        } catch (La_Data_RecordSetNoRowException $e) {
            throw new liveagent_Exception_ConnectProblem();
        }
        try {
            $this->setCachedSetting(self::OWNER_AUTHTOKEN, $loginData->getValue('authtoken'));
        } catch (La_Data_RecordSetNoRowException $e) {
            // we are communicating with older LA that does not send auth token
            $this->setCachedSetting(self::OWNER_AUTHTOKEN, self::NO_AUTH_TOKEN);
        }
        return $sessionId;
    }

    public function settingsDefinedForConnection() {
        return strlen(trim($this->getLiveAgentUrl())) && strlen(trim($this->getOwnerEmail()));
    }
    
    public function setButtonCode($buttonCode) {
        $this->setSetting(self::BUTTON_CODE, $buttonCode);
    }

    public function getLiveAgentUrl() {
        $url = get_option(self::LA_URL_SETTING_NAME);
        if ($url == null) {
            return $url;
        }
        if (strpos($url, 'http://') === false && strpos($url, 'https://') === false) {
            $url = 'http://' . $url;
        }
        if (strrpos($url, '/') == (strlen($url) - 1)) {
            $url = substr($url, 0, -1);
        }
        return $url; 
    }

    public function getOwnerEmail() {
        return get_option(self::LA_OWNER_EMAIL_SETTING_NAME);
    }

    public function getOwnerPassword() {
        return get_option(self::LA_OWNER_PASSWORD_SETTING_NAME);
    }
    
    public function getButtonCode() {
        $code = get_option(self::BUTTON_CODE);
        if ($code != '') {
            return $code;
        }
        $enabledButtons = get_option(liveagent_Settings::BUTTONS_CONFIGURATION_SETTING_NAME);
        if ($enabledButtons == null) {
            return $code;
        }
        //for compatibility reasons from older versions (1.2.X) 
        foreach ($enabledButtons as $buttonid => $value) {
            $url = $this->getLiveAgentUrl();
            update_option(self::BUTTONS_CONFIGURATION_SETTING_NAME, null);
            $integrationCode = $this->getIntegrationCode($url, $buttonid);
            $this->setButtonCode($integrationCode);
            return $integrationCode;
        }
    }

    public function getIntegrationCode($url, $buttonid) {
        return '<script type="text/javascript" id="la_x2s6df8d" src="'.$url.'/scripts/trackjs.php"></script>' . "\n" .
                '<img src="'.$url.'/scripts/pix.gif" onLoad="LiveAgentTracker.createButton(\''.$buttonid.'\', this);"/>';
    }

    public function buttonIsEnabled($buttonId) {
        $value = get_option(liveagent_Settings::BUTTONS_CONFIGURATION_SETTING_NAME);
        if ($value == '' || $value === null) {
            return false;
        }
        if (array_key_exists($buttonId, $value) && $value[$buttonId] == 'true') {
            return true;
        }
        return false;
    }
}

?>