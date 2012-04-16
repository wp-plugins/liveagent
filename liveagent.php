<?php
/*
Plugin Name: Live Agent
Plugin URI: http://www.qualityunit.com/liveagent
Description: Plugin enable integration of Wordpress with Live Agent
Author: QualityUnit
Version: 2.0.0
Author URI: http://www.qualityunit.com
License: GPL2
*/

if (!defined('LIVEAGENT_PLUGIN_VERSION')) {
    define('LIVEAGENT_PLUGIN_VERSION', '2.0.0');
}
if (!defined('LIVEAGENT_PLUGIN_NAME')) {
    define('LIVEAGENT_PLUGIN_NAME', 'liveagent');
}

include_once WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . LIVEAGENT_PLUGIN_NAME . DIRECTORY_SEPARATOR . 'Config.class.php';

$liveagentloadErrorMessage = null;

if (!function_exists('liveagent_PluginLoadError')) {
    function liveagent_PluginLoadError() {
        global $liveagentloadErrorMessage;
        if (current_user_can( 'install_plugins') && current_user_can('manage_options') ) {
            echo '<div class="error"><p>'.$loadErrorMessage.'</p></div>';
        }
    }
}

try {
    include_once WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . LIVEAGENT_PLUGIN_NAME . DIRECTORY_SEPARATOR . 'Loader.class.php';
    $liveagentLoader = liveagent_Loader::getInstance();
    $liveagentLoader->load();
} catch (Exception $e) {
    $loadErrorMessage = __(sprintf('Critical error during liveagent plugin load %s', $e->getMessage()), LIVEAGENT_PLUGIN_NAME);
    add_action( 'admin_notices', 'liveagent_PluginLoadError');
    return;
}

if (!class_exists('liveagent')) {
    class liveagent extends liveagent_Base {

        /**
         * @var liveagent
         */
        private static $instance = null;

        /**
         * @var liveagent_Settings
         */
        private $settings;

        /**
         * @var liveagent_Auth
         */
        private $auth;

        /**
         *  @var liveagent_AjaxHandler
         */
        private $ajaxHandler;

        /**
         * @var boolean
         */
        private $active = false;

        public function activate() {
            if ($this->isActive()) {
                return;
            }
            $this->settings = new liveagent_Settings();
            $this->auth = new liveagent_Auth();
            $this->ajaxHandler = new liveagent_AjaxHandler($this->settings, $this->auth);

            $this->initPlugin();
            $this->active = true;
        }

        public function isActive() {
            return $this->active;
        }

        public static function getInstance() {
            if (self::$instance == null) {
                self::$instance = new self;
            }
            return self::$instance;
        }

        /**
         * calledby add_filter ('admin_head', array($this, 'initAdminHeader'), 99);
         */
        public function adminInit() {
            $this->settings->initSettingsForAdminPanel();
            add_action('wp_ajax_lasignupcancel', array($this->ajaxHandler,'liveagentSignupCancel'));
            add_action('wp_ajax_lasignuprestart', array($this->ajaxHandler,'liveagentSignupRestart'));
            add_action('wp_ajax_lasignupsubmit', array($this->ajaxHandler,'liveagentSignupSubmit'));
            add_action('wp_ajax_laping', array($this->ajaxHandler,'liveagentPing'));
        }

        private function initPlugin() {
            add_action('admin_init', array($this, 'adminInit'));
            add_action('admin_menu', array($this, 'addPrimaryConfigMenu'));
            add_filter ('admin_head', array($this, 'initAdminHeader'), 99);
            add_action ('wp_enqueue_scripts', array($this, 'includeJavascripts'));
            add_action ('admin_enqueue_scripts', array($this, 'includeJavascripts'));

            if (!$this->settings->settingsDefinedForConnection()) {
                return;
            }
            add_filter ('wp_footer', array($this, 'initFooter'), 99);
            
            if ($this->settings->getButtonCode() == null) {
                add_action( 'admin_notices', array($this, 'showButtonCodeEmptyError'));
            }
            
            try {
                $this->settings->getOwnerSessionId();
            } catch (liveagent_Exception_ConnectProblem $e) {
                return;
            }            
        }
        
        public function showButtonCodeEmptyError() {
            if (current_user_can('manage_options')) {
                echo('<div class="error" style="margin-left:0px;"><p>' . __('No chat buttons enabled on your page. Enable one of them in your <b>Live Agnet/Button code</b> configuration.') . '</p></div>');
            }
        }

        public function includeJavascripts() {
            wp_enqueue_script('liveagent-main', $this->getJsUrl() . 'main.js', array(), LIVEAGENT_PLUGIN_VERSION);
        }

        public function initAdminHeader($content) {
            if(!is_feed()) {
                echo '<link type="text/css" rel="stylesheet" href="' . $this->getCssUrl() . 'styles.css?ver=' . LIVEAGENT_PLUGIN_VERSION . '" \>' . "\n";
                echo '<link href="http://fonts.googleapis.com/css?family=PT+Sans:regular,italic,bold,italicbold" rel="stylesheet" type="text/css" />' . "\n";
            }
            echo $content;
        }

        public function initFooter() {
            $footer = new liveagent_wordpress_Footer($this->settings);
            $footer->initVisitorFooter();
        }

        public function addPrimaryConfigMenu() {
            $formHandler = new liveagent_Form_Handler($this->settings, $this->auth);
            add_menu_page(__('Live Agent', LIVEAGENT_PLUGIN_NAME),
                    __('Live Agent',LIVEAGENT_PLUGIN_NAME),
                    'manage_options',
                    'la-top-level-options-handle',
                    array($formHandler, 'printGeneralConfigPage'),
                    $this->getImgUrl() . 'menu-icon.png');

            add_submenu_page('la-top-level-options-handle',
                    __('Button code',LIVEAGENT_PLUGIN_NAME),
                    __('Button code',LIVEAGENT_PLUGIN_NAME),
                    'manage_options',
                    'buttons-config-page',
                    array($formHandler, 'printButtonsConfigPage'));
        }
    }
}

$liveagent = liveagent::getInstance();
$liveagent->activate();
?>
