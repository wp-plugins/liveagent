<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Juraj Simon
 *   @package WpLiveAgentPlugin
 *   @version 1.0.0
 *
 *   Licensed under GPL2
 */

require_once(WP_PLUGIN_DIR . '/' . LIVEAGENT_PLUGIN_NAME . '/LoadClassException.class.php');

if (!class_exists('liveagent_Loader')) {
    class liveagent_Loader {
        const API_FILE = 'PhpApi.class.php';
        public function load() {
            $this->loadBaseClasses();
            $this->loadThirdPartyLibraries();
            $this->loadForms();
            $this->loadApi();
            $this->loadHelpers();
            $this->loadWidgets();
        }

        private function loadWidgets() {
            $this->loadClass('liveagent_widget_Button');
            $this->loadClass('liveagent_widget_Visits');
        }

        private function loadHelpers() {
            $this->loadClass('liveagent_helper_Grid');
            $this->loadClass('liveagent_helper_Buttons');
            $this->loadClass('liveagent_helper_Visits');
            $this->loadClass('liveagent_AjaxHandler');
            $this->loadClass('liveagent_helper_Signup');
            $this->loadClass('liveagent_helper_CompactTracker');
        }

        private function loadForms() {
            $this->loadClass('liveagent_Form_Base');
            $this->loadClass('liveagent_Form_Settings_CanLoginToPanel');
            $this->loadClass('liveagent_Form_Settings_Account');
            $this->loadClass('liveagent_Form_Settings_ButtonsTableRow');
            $this->loadClass('liveagent_Form_Settings_Buttons');
            $this->loadClass('liveagent_Form_Grid_Visits');
            $this->loadClass('liveagent_Form_Grid_VisitsTableRow');
            $this->loadClass('liveagent_Form_Settings_Signup');
            $this->loadClass('liveagent_Form_Settings_SignupWait');
            $this->loadClass('liveagent_Form_Settings_Congratulations');
        }

        private function loadThirdPartyLibraries() {
            $this->requireClass(WP_PLUGIN_DIR . '/' . LIVEAGENT_PLUGIN_NAME . '/lib/forms/class.htmlform.php');
        }

        private function loadApi() {
            $this->requireClass(WP_PLUGIN_DIR . '/' . LIVEAGENT_PLUGIN_NAME . '/' . self::API_FILE);
        }

        private function loadBaseClasses() {
            $this->loadClass('liveagent_Base');
            $this->loadClass('liveagent_Settings');
            $this->loadClass('liveagent_Auth');
            $this->loadClass('liveagent_Exception_SettingNotValid');
            $this->loadClass('liveagent_Exception_ConnectProblem');
            $this->loadClass('liveagent_WidgetIntegrator');
        }

        protected function loadClass($className) {
            if (!class_exists($className, false)) {
                $path = str_replace('_', "/", $className);
                $path = WP_PLUGIN_DIR . '/' . $path . '.class.php';
                $this->requireClass($path);
            }
        }

        protected function requireClass($pathToFile) {
            if (!file_exists($pathToFile)) {
                throw new liveagent_LoadClassException('File ' . $pathToFile . ' do NOT exist! Can not continue.');
            }
            require_once $pathToFile;
        }
    }
}
?>