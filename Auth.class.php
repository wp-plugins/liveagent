<?php
/**
 *   @copyright Copyright (c) 2011 Quality Unit s.r.o.
 *   @author Juraj Simon
 *   @package WpLiveAgentPlugin
 *   @version 1.0.0
 *
 *   Licensed under GPL2
 */

if (!class_exists('liveagent_Auth')) {
    class liveagent_Auth extends liveagent_Base {
        public function ping() {
            if (strpos($this->getRemoteApiUrl(), '.ladesk.com') === false) {
                $this->internalPing($this->getRemoteApiUrl());
                return;
            }
            //hack to fix dns problem - should be romved in the future myla.ladesk.com is not accessible, but www.myla.ladesk.com is - so try both
            try {
                $this->internalPing($this->getRemoteApiUrl());
            } catch (liveagent_Exception_ConnectProblem $e) {
                $url = $this->getRemoteApiUrl();
                $url = preg_replace('/http:\/\//', 'http://www.', $url);
                $this->internalPing($url);
            }
        }

        private function internalPing($url) {
            $request = new La_Rpc_DataRequest("Gpf_Common_ConnectionUtil", "ping");
            $request->setUrl($url);
            try {
                $request->sendNow();
            } catch (Exception $e) {
                if ($this->isDebugMode()) {
                    $this->_log(__('Unable to ping Live Agent remotelly: ' . $e->getMessage(), LIVEAGENT_PLUGIN_NAME));
                }
                throw new liveagent_Exception_ConnectProblem();
            }
            $data = $request->getData();
            if ($data->getParam('status') != 'OK') {
                throw new liveagent_Exception_ConnectProblem();
            }
        }

        private function InternalLoginAndGetLoginData($url) {
            $settings = new liveagent_Settings();
             
            $request = new La_Rpc_DataRequest("Gpf_Api_AuthService", "authenticate");

            $request->setField('username' ,$settings->getOwnerEmail());
            $request->setField('password' ,$settings->getOwnerPassword());
            $request->setUrl($url);
            try {
                $request->sendNow();
            } catch (Exception $e) {
                if ($this->isDebugMode()) {
                    $this->_log(__('Unable to login.', LIVEAGENT_PLUGIN_NAME));
                    $this->_log($e->getMessage());
                }
                throw new liveagent_Exception_ConnectProblem();
            }
            if ($request->getData()->getParam('error')!=null) {
                $this->_log(__('Answer from server: ' . print_r($request->getResponseObject()->toObject(), true), LIVEAGENT_PLUGIN_NAME));
                throw new liveagent_Exception_ConnectProblem();
            }
            return $request->getData();
        }

        /**
         * @return La_Rpc_Data
         */
        public function LoginAndGetLoginData() {
            if (strpos($this->getRemoteApiUrl(), '.ladesk.com') === false) {
                return $this->InternalLoginAndGetLoginData($this->getRemoteApiUrl());
            }
            //hack to fix dns problem - should be romved in the future myla.ladesk.com is not accessible, but www.myla.ladesk.com is - so try both
            try {
                return $this->InternalLoginAndGetLoginData($this->getRemoteApiUrl());
            } catch (liveagent_Exception_ConnectProblem $e) {
                $url = $this->getRemoteApiUrl();
                $url = preg_replace('/http:\/\//', 'http://www.', $url);
                return $this->InternalLoginAndGetLoginData($url);
            }
        }
    }
}
?>