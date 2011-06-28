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

    public function getData() {
        $request = new La_Rpc_Request($this->className, 'getRows');
        try {
            $request->setUrl($this->getRemoteApiUrl() . '?S=' . $this->settings->getOwnerSessionId());
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
            $this->_log(__(sprintf('Unable to data for %s', $this->className)));
            return new La_Data_RecordSet();
        }
        $grid = new La_Data_Grid();
        $grid->loadFromObject($request->getStdResponse());
        return $grid->getRecordset();
    }

}
?>