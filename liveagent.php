<?php
/*
 Plugin Name: Live Agent
 Plugin URI: http://www.qualityunit.com/liveagent
 Description: Plugin that enable integration with Live Agent
 Author: QualityUnit
 Version: 1.2.3
 Author URI: http://www.qualityunit.com
 License: GPL2
 */

define('LIVEAGENT_PLUGIN_VERSION', '1.2.3');
define('LIVEAGENT_PLUGIN_NAME', 'liveagent');
require_once WP_PLUGIN_DIR . '/' . LIVEAGENT_PLUGIN_NAME . '/Config.class.php';

$loadErrorMessage = null;

function liveagent_PluginLoadError() {
    global $loadErrorMessage;
    echo '<div class="error"><p>'.$loadErrorMessage.'</p></div>';
}

try {
    include WP_PLUGIN_DIR . '/'.LIVEAGENT_PLUGIN_NAME.'/Loader.class.php';
    $liveagentLoader = new liveagent_Loader();
    $liveagentLoader->load();
} catch (Exception $e) {
    $loadErrorMessage = __(sprintf('Critical error during liveagent plugin %s', $e->getMessage()), LIVEAGENT_PLUGIN_NAME);
    add_action( 'admin_notices', 'liveagent_PluginLoadError');
    return;
}

if (!class_exists('liveagent')) {
    class liveagent extends liveagent_Base {
        /**
         * @var liveagent_Settings
         */
        private $settings;
        /**
         * @var liveagent_helper_Buttons
         */
        private $buttonHelper;

        /**
         * @var liveagent_Auth
         */
        private $auth;

        /**
         *  @var liveagent_AjaxHandler
         */
        private $ajaxHandler;

        public function __construct() {
            $this->buttonHelper = new liveagent_helper_Buttons();
            $this->settings = new liveagent_Settings();
            $this->initPlugin();

            /*$session = new Gpf_Api_Session('http://www.qualityunit.com/affiliate/scripts/track.php');
             $tracker = new Pap_Api_Tracker();
             $tracker->track();*/
        }

        private function getLaIconURL() {
            return $this->getImgUrl() . 'menu-icon.png';
        }

        private function initPlugin() {
            $this->auth = new liveagent_Auth();
            $this->ajaxHandler = new liveagent_AjaxHandler($this->settings, $this->auth);
            add_filter ('init', array($this->ajaxHandler, 'handle'), 99);
            add_action('admin_init', array($this->settings, 'initSettings'));
            add_action('admin_menu', array($this, 'addPrimaryConfigMenu'));
            add_filter ('wp_head', array($this, 'initHeader'), 99);
            add_filter ('admin_head', array($this, 'initAdminHeader'), 99);
            if (!$this->includeJavascript('liveagent-jquery', 'jQuery.js')) {
                return;
            }
            if (!$this->includeJavascript('liveagent-main', 'main.js')) {
                return;
            }
            if (!$this->settings->settingsDefinedForConnection()) {
                return;
            }
            add_filter ('wp_footer', array($this, 'initFooter'), 99);
            try {
                $this->settings->getOwnerSessionId();
            } catch (liveagent_Exception_ConnectProblem $e) {
                return;
            }
            $widgetIntegrator = new liveagent_WidgetIntegrator($this->settings, $this->buttonHelper);
            $widgetIntegrator->initWidgets();
        }

        private function includeJavascript($handle, $jsName) {
            if (function_exists('wp_enqueue_script')) {
                wp_enqueue_script($handle, $this->getJsUrl() . $jsName, array(), LIVEAGENT_PLUGIN_VERSION);
                return true;
            } else {
                $this->showAdminError(__('Live Agent plugin error: Unable to load required javascript files! Wordpress function wp_enqueue_script is missing.', LIVEAGENT_PLUGIN_NAME));
                return false;
            }
        }

        private function initCommonHeaders() {
            echo '<link type="text/css" rel="stylesheet" href="' . $this->getCssUrl() . 'styles.css?ver=' . LIVEAGENT_PLUGIN_VERSION . '" \>' . "\n";
        }

        public function initAdminHeader($content) {
            if(!is_feed()) {
                $this->initCommonHeaders();
                echo '<link href="http://fonts.googleapis.com/css?family=PT+Sans:regular,italic,bold,italicbold" rel="stylesheet" type="text/css" />' . "\n";
            }
            echo $content;
        }

        public function initHeader($content) {
            if(!is_feed()) {
                $this->initCommonHeaders();
                if (!$this->settings->settingsDefinedForConnection()) {
                    return;
                }
                echo '<script type="text/javascript" id="la_x2s6df8d" src="'.$this->getRemoteTrackJsUrl().'"></script>';
            }
            echo $content;
        }

        public function initFooter() {
            if(!is_feed()) {
                try {
                    echo $this->buttonHelper->getIntegrationCodeForEnabledFloatButtons();
                } catch (liveagent_Exception_ConnectProblem $e) {
                    $this->showConnectionError();
                } catch (Exception $e) {
                    $this->_log(__(sprintf('Unable to insert button in footer %s', $e->getMessage()), LIVEAGENT_PLUGIN_NAME));
                }
            }
        }

        public function addPrimaryConfigMenu() {
            add_menu_page(__('Live Agent', LIVEAGENT_PLUGIN_NAME), __('Live Agent',LIVEAGENT_PLUGIN_NAME), 'manage_options', 'la-top-level-options-handle', array($this, 'printGeneralConfigPage'), $this->getLaIconURL());
            if (!strlen(trim($this->settings->getLiveAgentUrl())) || !strlen(trim($this->settings->getOwnerEmail()))) {
                return;                
            }
            add_submenu_page('la-top-level-options-handle', __('Buttons',LIVEAGENT_PLUGIN_NAME), __('Buttons',LIVEAGENT_PLUGIN_NAME), 'manage_options', 'buttons-config-page', array($this, 'printButtonsConfigPage'));
        }

        public function printGeneralConfigPage() {
            if ($this->getAccountStatus() == self::ACCOUNT_STATUS_NOTSET) {
                $form = new liveagent_Form_Settings_Signup($this->settings, $this->auth);

            } else {
                $form = new liveagent_Form_Settings_Account($this->settings, $this->auth);
            }
            $form->render();
        }

        public function printButtonsConfigPage() {
            $form = new liveagent_Form_Settings_Buttons($this->settings);
            $form->render();
        }
    }
}

$liveagent = new liveagent();
?>
