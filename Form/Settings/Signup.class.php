<?php
/**
 *   @copyright Copyright (c) 2011 Quality Unit s.r.o.
 *   @author Juraj Simon
 *   @package WpLiveAgentPlugin
 *   @version 1.0.0
 *
 *   Licensed under GPL2
 */

class liveagent_Form_Settings_Signup extends liveagent_Form_Base {
    private $userName = '';
    
    public function __construct() {
        global $current_user;
        get_currentuserinfo();
        $this->userName = $current_user->display_name;
        if ($this->userName == '') {
            $this->userName = 'name';
        }
        parent::__construct(liveagent_Settings::SIGNUP_SETTINGS_PAGE_NAME);        
    }

    protected function getTemplateFile() {
        return $this->getTemplatesPath() . 'AccountSignup.xtpl';
    }

    protected function getType() {
        return liveagent_Form_Base::TYPE_TEMPLATE;
    }

    private function getdomainOnly() {
        return preg_replace('/^(.*\.)?([^.]*\..*)$/', '$2', @$_SERVER['HTTP_HOST']);
    }

    protected function getOption($name) {
        switch ($name) {
            case liveagent_Settings::LA_OWNER_EMAIL_SETTING_NAME:
                return get_bloginfo('admin_email');
            case 'la-full-name':
                return $this->userName;
            case liveagent_Settings::LA_URL_SETTING_NAME:
                $domain = substr($this->getdomainOnly(), 0, strpos($this->getdomainOnly(), '.'));
                $domain = str_replace(array('http://', 'https://'), '', $domain);
                $domain = preg_replace('/[^A-Za-z0-9]/', '', $domain);
                if ($domain == '') {
                    $domain = 'support' . time();
                }
                return $domain;
        }
    }

    protected function initForm() {
        $this->addTextBox('la-full-name', null, 'nlInput text');
        $this->addTextBox(liveagent_Settings::LA_OWNER_EMAIL_SETTING_NAME, null, 'nlInput text');
        $this->addTextBox(liveagent_Settings::LA_URL_SETTING_NAME, 5, 'nlInput text');
        $this->form->add('html', 'submit', __('Create your account', LIVEAGENT_PLUGIN_NAME));
    }
}

?>