<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Juraj Simon
 *   @package WpLiveAgentPlugin
 *   @version 1.0.0
 *
 *   Licensed under GPL2
 */

class liveagent_helper_Visits extends liveagent_Base {
    public function getVisitorLocation($visit) {
        if (!$this->isEmpty($visit->get('countrycode'))) {
            if (!$this->isEmpty($visit->get('city'))) {
                return $visit->get('countrycode') . ', ' . $visit->get('city');
            }
            return $visit->get('countrycode');
        }
        return '';
    }

    public function getVisitorName($visit) {
        $userName = '';
        if (!$this->isEmpty($visit->get('firstname')) || !$this->isEmpty($visit->get('lastname'))) {
            $userName = $visit->get('firstname') . " " . $visit->get('lastname');
            if (!$this->isEmpty($visit->get('email'))) {
                return trim($userName) . " (" . $visit->get('email') . ")";
            }
        }
        if (!$this->isEmpty($visit->get('email'))) {
            return $visit->get('email');
        }
        return $visit->get('ip');
    }

}
?>