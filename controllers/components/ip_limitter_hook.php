<?php
/* SVN FILE: $Id$ */
/**
 * [IpLimitter] フックコンポーネント
 *
 * PHP version 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2012, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2011 - 2012, Catchup, Inc.
 * @link			http://www.e-catchup.jp Catchup, Inc.
 * @package			ip_limitter.controllers.components
 * @since			Baser v 2.0.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			MIT lincense
 */
class IpLimitterHookComponent extends Object {

	var $registerHooks = array('startup');

	function startup(&$controller) {
		$IpLimitterConfig = ClassRegistry::init('IpLimitter.IpLimitterConfig');
		$datas = $IpLimitterConfig->findExpanded();
		if($datas) {
			if(empty($datas['allowed_ip'])) {
				return;
			}
			$allowedIp = preg_quote($datas['allowed_ip']);
			$patterns = str_replace("\*", '.+?', $allowedIp);
			$patterns = explode(',', $patterns);
			foreach($patterns as $pattern) {
				if(preg_match('/'.$pattern.'/', $controller->RequestHandler->getClientIp())) {
					return;
				}
			}
			if(empty($datas['limit_folders'])) {
				$this->notFound();
			} else {
				$limitFolders = explode(',', $datas['limit_folders']);
				if(!empty($controller->params['url']['url'])) {

					$folder = explode('/', $controller->params['url']['url']);
					if(!empty($folder[0])) {
						$folder = $folder[0];
						if(in_array($folder, $limitFolders)) {
							if(empty($datas['redirect_url'])) {
								$controller->notFound();
							} else {
								$controller->redirect($datas['redirect_url']);
							}
						}
					}

				}
			}
		}
	}

}
