<?php
/**
 *   @copyright Copyright (c) 2011 Quality Unit s.r.o.
 *   @author Juraj Simon
 *   @package WpLiveAgentPlugin
 *   @version 1.0.0
 *
 *   Licensed under GPL2
 */

if (!class_exists('liveagent_Base')) {
	class liveagent_Base {
		const IMG_PATH = 'img/';
		const TEMPLATES_PATH = 'templates/';
		const JS_PATH = 'js/';
		const CSS_PATH = 'css/';
			
		protected function _log($message) {
			if( WP_DEBUG === true ){
				if( is_array( $message ) || is_object( $message ) ){
					$message = print_r( $message, true );
				}
				$message = 'LiveAgent plugin log: ' . $message;
				error_log($message);
				echo $message;
			}
		}

		protected function getTemplatesPath() {
			return WP_PLUGIN_DIR . '/' . LIVEAGENT_PLUGIN_NAME . '/' . self::TEMPLATES_PATH;
		}

		protected function getImgUrl() {
			return WP_PLUGIN_URL . '/' . LIVEAGENT_PLUGIN_NAME . '/' . self::IMG_PATH;
		}

		protected function getJsUrl() {
			return WP_PLUGIN_URL . '/' . LIVEAGENT_PLUGIN_NAME . '/' . self::JS_PATH;
		}

		protected function getCssUrl() {
			return WP_PLUGIN_URL . '/' . LIVEAGENT_PLUGIN_NAME . '/' . self::CSS_PATH;
		}

		protected function showAdminError($error) {
			add_action( 'admin_notices', eval("echo '<div class=\"error\"><p>".$error."</p></div>';"));
		}

		protected function showConnectionError() {
			$this->showAdminError(__('Unable to connect to Live Agent. please check your connection settings', LIVEAGENT_PLUGIN_NAME));
		}
		
		public function getRemoteTrackJsUrl() {
			return get_option(liveagent_Settings::LA_URL_SETTING_NAME) . '/scripts/trackjs.php';
		}
		
		public function getRemotePixUrl() {
			return get_option(liveagent_Settings::LA_URL_SETTING_NAME) . '/scripts/pix.gif';
		}

		public function getRemoteApiUrl() {
			return get_option(liveagent_Settings::LA_URL_SETTING_NAME) . '/api/index.php';
		}
	}
}

?>