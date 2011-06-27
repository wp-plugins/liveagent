<?php
/**
 *   @copyright Copyright (c) 2011 Quality Unit s.r.o.
 *   @author Juraj Simon
 *   @package WpLiveAgentPlugin
 *   @version 1.0.0
 *
 *   Licensed under GPL2
 */

class liveagent_Form_Grid_VisitsTableRow extends liveagent_Form_Base {
    private $row;
    private $visitsHelper;

    public function __construct($row) {
        $this->visitsHelper = new liveagent_helper_Visits();
        $this->row = $row;
        parent::__construct();
    }

    protected function getTemplateFile() {
        return $this->getTemplatesPath() . 'VisitsTableRow.xtpl';
    }

    protected function getType() {
        return liveagent_Form_Base::TYPE_TEMPLATE;
    }

    

    private function getUrl($url) {
        if (strlen($url)>50) {
            $urlName = substr($url,0,50) . '...';
        } else {
            $urlName = $url;
        }
        return '<a href="' . $url . '" target="_blank">'.$urlName.'</a>';
    }

    private function getVisitor() {
         
    }

    protected function initForm() {
        /* ======possible values:
         * date_first_visit
         * date_last_visit
         * url
         * referrerurl
         * ip
         * firstname
         * lastname
         * system_name
         * email
         * countrycode
         * city
         */
        $this->addHtml('url', $this->getUrl('http://dadadada')/*$this->row->get('url')*/);
        $this->addHtml('refererurl', $this->getUrl('http://dasdasdadasdass jkh sdfjkhklgh sdkgh sdkhg dkshg ksdh gsdklhg kshgsdklgh sdkhg sdfkgh sdkhg dkshg ksdh')/*$this->row->get('referrerurl')*/);
        $this->addHtml('visitorName', $this->visitsHelper->getVisitorName($this->row));
        $this->addHtml('visitorLocation', $this->visitsHelper->getVisitorLocation($this->row));
        $this->addHtml('timeFirst', $this->row->get('date_first_visit'));
        $this->addHtml('timeLast', $this->row->get('date_last_visit'));
    }

    public function render() {
        return parent::render(true);
    }
}

?>