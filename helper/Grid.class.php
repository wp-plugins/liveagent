<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Juraj Simon
 *   @package WpLiveAgentPlugin
 *   @version 1.0.0
 *
 *   Licensed under GPL2
 */

class liveagent_helper_Grid extends liveagent_Base {
    protected $settings;
    protected $className;
    protected $from;
    protected $to;

    public function __construct($className, $from = null, $to = null) {
        $this->settings = new liveagent_Settings();
        $this->from = $from;
        $this->to = $to;
        $this->className = $className;
    }

    private function internalGetData($url) {
        $request = new La_Rpc_Request($this->className, 'getRows');
        try {
            $request->setUrl($url);
        } catch (liveagent_Exception_ConnectProblem $e) {
            $this->_log(__('Unable to connect and get session id'));
            if ($this->isDebugMode()) {
                $this->_log($e->getMessage());
            }
            return new La_Data_RecordSet();
        }
        if ($this->from !== null) {
            $request->addParam('from', $this->from);
        }
        if ($this->to !== null) {
            $request->addParam('to', $this->to);
        }

        try {
            $request->sendNow();
        } catch (Exception $e) {
            $this->_log(__(sprintf('Unable to get data for %s', $this->className)));
            if (defined('LIVEAGENT_DEBUG_MODE') && LIVEAGENT_DEBUG_MODE === true) {
                $this->_log(__($e->getMessage()));
            }
            return new La_Data_RecordSet();
        }
        $grid = new La_Data_Grid();
        $grid->loadFromObject($request->getStdResponse());
        return $grid->getRecordset();
    }

    public function getData() {
        $url = $this->getRemoteApiUrl() . '?S=' . $this->settings->getOwnerSessionId();
        if (strpos($this->getRemoteApiUrl(), '.ladesk.com') === false) {
            return $this->internalGetData($url);
        }
        $secondUrl = preg_replace('/http:\/\//', 'http://www.', $url);
        try {            
            $rows = $this->internalGetData($url);
        } catch (Exception $e) {
            return $this->internalGetData($secondUrl);
        }
        if ($rows->getSize() == 0) {
            return $this->internalGetData($secondUrl);
        }
        return $rows;
    }

}
?>