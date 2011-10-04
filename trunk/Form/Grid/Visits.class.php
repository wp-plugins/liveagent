<?php
/**
 *   @copyright Copyright (c) 2011 Quality Unit s.r.o.
 *   @author Juraj Simon
 *   @package WpLiveAgentPlugin
 *   @version 1.0.0
 *
 *   Licensed under GPL2
 */

class liveagent_Form_Grid_Visits extends liveagent_Form_Base {
    private $gridData;

    public function __construct($gridData) {
        $this->gridData = $gridData;
        parent::__construct();
    }

    protected function getTemplateFile() {
        if ($this->gridData->getSize()==0) {
            return $this->getTemplatesPath() . 'VisitsGridEmpty.xtpl';
        }
        return $this->getTemplatesPath() . 'VisitsGrid.xtpl';
    }

    protected function getType() {
        return liveagent_Form_Base::TYPE_TEMPLATE;
    }

    protected function initForm() {
        if ($this->gridData->getSize()==0) {
            $this->addHtml('message', __('There are no actual visitors on page', LIVEAGENT_PLUGIN_NAME));
        }
        $this->addHtml('refererurl', __('Current page/Referer url', LIVEAGENT_PLUGIN_NAME));
        $this->addHtml('visitor', __('Visitor', LIVEAGENT_PLUGIN_NAME));
        $this->addHtml('time', __('Last/First', LIVEAGENT_PLUGIN_NAME));
        $html = '';
        foreach ($this->gridData as $gridRow) {            
            $row = new liveagent_Form_Grid_VisitsTableRow($gridRow);
            $html .= $row->render();
        }
        $this->addHtml('visitsrows', $html);
    }

    public function render() {
        return parent::render(true);
    }
}

?>