<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Juraj Simon
 *   @package WpLiveAgentPlugin
 *   @version 1.0.0
 *
 *   Licensed under GPL2
 */

class liveagent_helper_Signup extends liveagent_Base {

    public function signup($name, $email, $domain, $password, $papVisitorId = '') {
        $request = new La_Rpc_ActionRequest('Dp_QualityUnit_La_Signup', 'createAccountRequest');
        $request->setUrl('http://members.qualityunit.com/scripts/server.php');
        $request->addParam('domain', $domain);
        $request->addParam('name', $name);
        $request->addParam('email', $email);
        $request->addParam('password', $password);
        $request->addParam('variationid', '00198b52');
        $request->addParam('PAPvisitorId', $papVisitorId);
        $request->addParam('source', 'wordpress');
        $request->sendNow();
        return $request->getStdResponse();
    }
}