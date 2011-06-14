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
	const BUTTONS_DATA = 'la-settings_buttonsdata';

	//general page
	const GENERAL_SETTINGS_PAGE_NAME = 'la-config-general-page';

	const LA_URL_SETTING_NAME = 'la-url';
	const LA_OWNER_EMAIL_SETTING_NAME = 'la-owner-email';
	const LA_OWNER_PASSWORD_SETTING_NAME = 'la-owner-password';
	const GENERAL_SETTINGS_PAGE_STATE_SETTING_NAME = 'general-settings-state';

	//buttons options
	const BUTTONS_SETTINGS_PAGE_NAME = 'la-config-buttons-page';

	const BUTTONS_CONFIGURATION_SETTING_NAME = 'la-buttons-configuration';

	public function initSettings() {
		register_setting(self::GENERAL_SETTINGS_PAGE_NAME, self::LA_URL_SETTING_NAME);
		register_setting(self::GENERAL_SETTINGS_PAGE_NAME, self::LA_OWNER_EMAIL_SETTING_NAME);
		register_setting(self::GENERAL_SETTINGS_PAGE_NAME, self::LA_OWNER_PASSWORD_SETTING_NAME);
		register_setting(self::BUTTONS_SETTINGS_PAGE_NAME, self::BUTTONS_CONFIGURATION_SETTING_NAME);
		register_setting(self::INTERNAL_SETTINGS, self::OWNER_SESSIONID);
		register_setting(self::INTERNAL_SETTINGS, self::BUTTONS_DATA);
	}

	private function setCachedSetting($code, $value) {
		$settings = get_option($code);
		if ($settings!='') {
			update_option($code, serialize(array('value' => $value, 'time' => time())));
		} else {
			add_option($code, serialize(array('value' => $value, 'time' => time())));
		}
	}

	private function getCachedSetting($code) {
		$settings = get_option($code);
		if (is_string($settings)) {
			$settings = unserialize($settings);
		}
		if ($settings == '' || $settings == null) {
			throw new liveagent_Exception_SettingNotValid(__(sprintf('Setting %s not defined yet.', $code)));
		}
		$validTo = $settings['time'] + self::CACHE_VALIDITY + 0;
		if ($validTo > time()) {
			return $settings['value'];
		} else {
			throw new liveagent_Exception_SettingNotValid(__(sprintf('Setting\'s %s validity exceeded: %s', $code, $settings['time'])));
		}
	}

	public function getOwnerSessionId() {
		try {
			return $this->getCachedSetting(self::OWNER_SESSIONID);
		} catch (liveagent_Exception_SettingNotValid $e) {
			$auth = new liveagent_Auth();
			$sessionid = $auth->LoginAndGetSessionId();
			$this->setCachedSetting(self::OWNER_SESSIONID, $sessionid);
		}
		return $sessionid;
	}

	public function settingsDefinedForConnection() {
		return strlen(trim($this->getLiveAgentUrl())) && strlen(trim($this->getOwnerEmail()));
	}

	public function getButtonsGridRecordset() {
		try {
			$data = unserialize($this->getCachedSetting(self::BUTTONS_DATA));
			return $data;
		} catch (liveagent_Exception_SettingNotValid $e) {
			$buttonsHelper = new liveagent_helper_Buttons();
			$data = $buttonsHelper->getButtonsGridData();
			$this->setCachedSetting(self::BUTTONS_DATA, serialize($data));
		}
		return $data;
	}

	public function getLiveAgentUrl() {
		return get_option(self::LA_URL_SETTING_NAME);
	}

	public function getOwnerEmail() {
		return get_option(self::LA_OWNER_EMAIL_SETTING_NAME);
	}

	public function getOwnerPassword() {
		return get_option(self::LA_OWNER_PASSWORD_SETTING_NAME);
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