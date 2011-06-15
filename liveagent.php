<?php
/*
 Plugin Name: Live Agent
 Plugin URI: http://www.qualityunit.com/liveagent
 Description: Plugin that enable integration with Live Agent
 Author: QualityUnit
 Version: 1.0.5
 Author URI: http://www.qualityunit.com
 License: GPL2
 */

define('LIVEAGENT_PLUGIN_NAME', 'liveagent');

$loadErrorMessage = null;

function liveagent_PluginLoadError() {
	global $loadErrorMessage;
	echo '<div class="error"><p>'.$loadErrorMessage.'</p></div>';
}

try {
	include WP_PLUGIN_DIR . '/liveagent/Loader.class.php';
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

		public function __construct() {
			$this->buttonHelper = new liveagent_helper_Buttons();
			$this->settings = new liveagent_Settings();
			$this->initPlugin();
		}

		private function getLaIconURL() {
			return $this->getImgUrl() . 'menu-icon.png';
		}


		private function initPlugin() {
			$this->auth = new liveagent_Auth();
			add_action('admin_init', array($this->settings, 'initSettings'));
			add_action('admin_menu', array($this, 'addPrimaryConfigMenu'));			
			add_filter ('wp_head', array($this, 'initHeader'), 99);
			add_filter ('admin_head', array($this, 'initHeader'), 99);
			if (!$this->settings->settingsDefinedForConnection()) {
				return;
			}
			if (function_exists('wp_enqueue_script')) {
				wp_enqueue_script('liveagent-main', $this->getJsUrl() . 'main.js', array(), '1.0');
			} else {
				$this->showAdminError(__('Live Agent plugin error: Unable to load required javascript files! Wordpress function wp_enqueue_script is missing.', LIVEAGENT_PLUGIN_NAME));
				return;
			}
			add_filter ('wp_footer', array($this, 'initFooter'), 99);
			try {
				$this->settings->getOwnerSessionId();
			} catch (liveagent_Exception_ConnectProblem $e) {
				return;
			}
			add_filter ('sidebars_widgets', array($this, 'insertActiveWidgets'), 99);
			$this->initWidgets();
		}

		private function widgetActive($elem, $array) {
			if( is_array( $array )) {
				if( is_array( $array ) && in_array( $elem, $array ) ) {
					return true;
				}
				foreach( $array as $array_element )	{
					if( ( is_array( $array_element ) ) && $this->widgetActive( $elem, $array_element ) ) {
						return true;
					}
				}
			}
			return false;
		}

		public function insertActiveWidgets($widgets) {
			$buttons = $this->settings->getButtonsGridRecordset();
			foreach ($buttons as $button) {
				if ($this->settings->buttonIsEnabled($button->get('id')) && !$this->widgetActive(liveagent_widget_Button::WIDGET_PREFIX . $button->get('id'), $widgets)) {
					$widgets['primary-widget-area'][] = liveagent_widget_Button::WIDGET_PREFIX . $button->get('id');
				} else if (!$this->settings->buttonIsEnabled($button->get('id'))) {
					wp_unregister_sidebar_widget(liveagent_widget_Button::WIDGET_PREFIX . $button->get('id'));
				}
			}
			return $widgets;
		}

		private function initWidgets() {
			$buttons = $this->settings->getButtonsGridRecordset();
			foreach ($buttons as $button) {
				if ($button->get('contenttype') != 'F') {
					$id = $button->get('id');
					$widget = new liveagent_widget_Button($id, $button->get('onlinecode'), $button->get('offlinecode'));
					wp_register_sidebar_widget(liveagent_widget_Button::WIDGET_PREFIX . $id, __('Live Agent Button', LIVEAGENT_PLUGIN_NAME) . ' (' . $id . ')', array($widget, 'showWidget'),
					array(
    					'description' => 'Live agent chat button ' . '(' . $this->buttonHelper->getTypeHumanReadable($button->get('contenttype')) . ')'
    					), $id
    					);
				}
			}
		}

		public function initHeader($content) {			
			if(!is_feed()) {
				echo '<link href="http://fonts.googleapis.com/css?family=PT+Sans:regular,italic,bold,italicbold" rel="stylesheet" type="text/css" />' . "\n";
				echo '<link type="text/css" rel="stylesheet" href="' . $this->getCssUrl() . 'styles.css' . '" \>' . "\n" . $content;
				if (!$this->settings->settingsDefinedForConnection()) {
					return;
				}
				echo '<script type="text/javascript" id="la_x2s6df8d" src="'.$this->getRemoteTrackJsUrl().'"></script>';
			}
		}

		public function initFooter() {
			if(!is_feed()) {
				try {
					echo $this->buttonHelper->getIntegrationCodeForEnabledFloatButtons();
				} catch (liveagent_Exception_ConnectProblem $e) {
					$this->showConnectionError();
				} catch (La_Exception $e) {
					$this->_log(__(sprintf('Unable to insert button in footer %s', $e->getMessage()), LIVEAGENT_PLUGIN_NAME));
				}
			}
		}

		public function addPrimaryConfigMenu() {
			add_menu_page(__('Live Agent', LIVEAGENT_PLUGIN_NAME), __('Live Agent',LIVEAGENT_PLUGIN_NAME), 'manage_options', 'la-top-level-options-handle', array($this, 'printGeneralConfigPage'), $this->getLaIconURL());
			add_submenu_page('la-top-level-options-handle', __('Buttons',LIVEAGENT_PLUGIN_NAME), __('Buttons',LIVEAGENT_PLUGIN_NAME), 'manage_options', 'buttons-config-page', array($this, 'printButtonsConfigPage'));
		}

		public function printGeneralConfigPage() {
			$form = new liveagent_Form_Settings_Account($this->settings, $this->auth);
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
