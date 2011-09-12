<?php
/**
 *   @copyright Copyright (c) 2011 Quality Unit s.r.o.
 *   @author Juraj Simon
 *   @package WpLiveAgentPlugin
 *   @version 1.0.0
 *
 *   Licensed under GPL2
 */
class liveagent_AjaxHandler extends liveagent_Base {
    /**
     * @var liveagent_Settings
     */
    private $settings;

    /**
     * @var liveagent_Auth
     */
    private $auth;

    public function __construct(liveagent_Settings $settings, liveagent_Auth $auth) {
        $this->settings = $settings;
        $this->auth = $auth;
    }

    public function handle() {
        if ( isset( $_POST['action'])&&isset($_POST['data']) ) {
            $this->includeWordpressBase();
            if ($_POST['action'] == 'liveagent_signup_action' && $_POST['data'] == 'cancel') {
                $this->liveagentSignupCancel();
            }
            if ($_POST['action'] == 'liveagent_signup_action' && $_POST['data'] == 'restart') {
                $this->liveagentSignupRestart();
            }
            if ($_POST['action'] == 'liveagent_signup_action' && $_POST['data'] == 'submit') {
                $this->liveagentSignupSubmit($_POST['att']);
            }
            if ($_POST['action'] == 'liveagent_signup_action' && $_POST['data'] == 'ping') {
                $this->liveagentPing($_POST['att']);
            }
            echo json_encode(array());
            die();
        }
    }

    public function liveagentPing($attributes) {
        try {
            $this->auth->ping();
        } catch (liveagent_Exception_ConnectProblem $e) {
            echo json_encode(array('result'=>0));
            die();
        }

        $form = new liveagent_Form_Settings_Congratulations($this->settings);
        $html = $form->render(true);
        echo json_encode(array('result'=>1,
            'runfunction'=>'onPingSuccessfull',
            'dialog'=>$html, 
            'replaceform'=>liveagent_Settings::SIGNUP_WAIT_SETTINGS_PAGE_NAME));
        die();
    }

    private function includeWordpressBase() {
        require_once '../wp-config.php';
        require_once 'includes/admin.php';
    }

    public function liveagentSignupCancel() {
        if ($this->getAccountStatus()==self::ACCOUNT_STATUS_NOTSET) {
            update_option(liveagent_Settings::ACCOUNT_STATUS, self::ACCOUNT_STATUS_SET);
        }
        $form = new liveagent_Form_Settings_Account($this->settings, $this->auth);
        $html = $form->render(true);
        echo json_encode(array('result'=>1, 'dialog'=>$html, 'replaceform'=>liveagent_Settings::SIGNUP_SETTINGS_PAGE_NAME));
        die();
    }

    private function sendSignupRequest($name, $email, $domain, $password, $papvisitorId) {
        $signupHelper = new liveagent_helper_Signup();
        try {
            $response = $signupHelper->signup($name, $email, $domain, $password, $papvisitorId);
        } catch (La_Exception $e) {
            echo json_encode(array('result'=>0, 'message'=>$e->getMessage(), 'runonfailure'=>'onSignupFail'));
            die();
        }
        if ($response->success != "Y") {
            echo json_encode(array('result'=>0, 'message'=>$response->errorMessage, 'runonfailure'=>'onSignupFail'));
            die();
        }
    }

    private function getVisitorId() {
        $tracker = new liveagent_helper_CompactTracker();
        return $tracker->getVisitorId();
    }

    public function liveagentSignupSubmit($attributes) {
        $name = $attributes['name'];
        $domain = $attributes['domain'];
        $email = $attributes['email'];
        $password = substr(md5(microtime()),0,8);

        $tracker = new liveagent_helper_CompactTracker();
        $this->setTrackingData($tracker, $domain);
        $papvisitorId = $tracker->getCookie();

        $this->sendSignupRequest($name, $email, $domain, $password, $papvisitorId);
        
        $this->saveOptionsAndActivateDefaultButton($domain, $email, $password);

        $form = new liveagent_Form_Settings_SignupWait();
        $html = $form->render(true);
        echo json_encode(array(
            'result'=>1, 
            'dialog'=>$html, 
            'runafter'=>true,
            'domain'=>$domain.'.ladesk.com',
            'replaceform'=>liveagent_Settings::SIGNUP_SETTINGS_PAGE_NAME, 'runfunction'=>'onSignupWait'
            ));
            die();
    }

    private function saveOptionsAndActivateDefaultButton($domain, $email, $password) {
        update_option(liveagent_Settings::ACCOUNT_STATUS, self::ACCOUNT_STATUS_SET);
        
        update_option(liveagent_Settings::LA_URL_SETTING_NAME, 'http://' . $domain . '.ladesk.com');
        update_option(liveagent_Settings::LA_OWNER_EMAIL_SETTING_NAME, $email);
        update_option(liveagent_Settings::LA_OWNER_PASSWORD_SETTING_NAME, $password);

        $buttonHelper = new liveagent_helper_Buttons();
        $buttonHelper->enableDefaultButton();
    }

    private function setTrackingData(liveagent_helper_CompactTracker $tracker, $domain) {
        $tracker->setData1('Wordpress ver.: ' . get_bloginfo('version') . ', domain: ' . $domain);
    }

    public function liveagentSignupRestart() {
        update_option(liveagent_Settings::ACCOUNT_STATUS, self::ACCOUNT_STATUS_NOTSET);
        $form = new liveagent_Form_Settings_Signup();
        $html = $form->render(true);
        echo json_encode(array('result'=>1, 'dialog'=>$html, 'replaceform'=>liveagent_Settings::GENERAL_SETTINGS_PAGE_NAME));
        die();
    }
}
?>