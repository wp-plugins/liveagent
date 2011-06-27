<?php
/*
 Plugin Name: Post Affiliate Pro
 Plugin URI: http://www.qualityunit.com/#
 Description: Plugin that enable user signup integration integration with Post Affiliate Pro
 Author: QualityUnit
 Version: 1.1.0
 Author URI: http://www.qualityunit.com
 License: GPL2
 */

class liveagent_widget_Visits extends liveagent_Base {
    const WIDGET_PREFIX = 'live_agent_visits';

    private $gridHelper;

    function __construct() {
        $this->gridHelper = new liveagent_helper_Grid('La_Home_BrowserVisitTable', 0, 10);
    }

    public function showWidget() {
        try {
            $template = new liveagent_Form_Grid_Visits($this->gridHelper->getData());
        } catch (Exception $e) {
            $this->_log(__('Can not show Visits Table', LIVEAGENT_PLUGIN_NAME));
            if ($this->isDebugMode()) {
                $this->_log('Error: ' . $e->getMessage());
            }
            return;
        }
        echo $template->render();
    }
}
?>