<?php

/*
 * To change this license header, choose License Headers in Project Properties. To change this template file, choose Tools | Templates and open the template in the editor.
*/
include_once (RUDRA . "/core/controller/AbstractController.php");

class NoController extends AbstractController {

	public function getHandlerPath() {
		return "";
	}

	public function invoke(User $user, $handlerName) {
		$className = ucfirst($handlerName );
		$user->validate();
		include_once(RUDRA . "/core/handler/AbstractHandler.php");
		include_once (HANDLER_PATH . "/" . $this->getHandlerPath() . $className . ".php");
		$tempClass = new ReflectionClass($className );
		global $temp;
		if ($tempClass->isInstantiable()) {
			$temp = $tempClass->newInstance();
		}
		if ($temp != NULL) {
			$isPage = is_subclass_of($temp, 'AbstractPageHandler');
			$isTemplate = is_subclass_of($temp, 'AbstractTemplateHandler');
			$isData = is_subclass_of($temp, 'AbstractDataHandler');
			
			if($isPage || $isTemplate){
				include_once (LIB_PATH . "/rudrax/smarty/Smarty.class.php");
				if($isPage){
					include_once (RUDRA . "/core/model/Header.php");
					include_once (RUDRA . "/core/model/Page.php");
				}
			}
			
			$temp->setUser($user);
			if($isPage){
				$tpl = new Smarty();
				self::setSmartyPaths($tpl);
				$tpl->debugging = Config::get('SMARTY_DEBUG');
				$temp->setTemplate($tpl );
				$header = new Header($tpl);
				$page = new Page();
				$view = RudraX::invokeMethodByReflectionClass($tempClass,$temp,'invokeHandler',array(
						'tpl' => $tpl,
						'viewModel' => $tpl,
						'user' => $user,
						'header' => $header,
						'page' => $page,
						'dataModel' => $page->data
				));
				//$view = $temp->invokeHandler($tpl );
				if (! isset($view )) {
					$view = $handlerName;
				}
				$header->minify();
				$tpl->assign('user',$user);
				$tpl->assign('page',$page);
				$tpl->assign('CONTEXT_PATH',CONTEXT_PATH);
				$tpl->assign('RESOURCE_PATH',Config::get('RESOURCE_PATH'));
				$tpl->assign('METAS',$header->metas);
				$tpl->assign('TITLE',$header->title);
				$tpl->assign('CSS_FILES',$header->css);
				$tpl->assign('SCRIPT_FILES',$header->scripts);
				$tpl->assign('BODY_FILES',$view . Config::get('TEMP_EXT'));
				$tpl->assign('page_json',json_encode($page->data->data));
				//echo get_include_path();
				//$tpl->display($this->getViewPath() . $view . Config::get('TEMP_EXT'));
				$tpl->display(get_include_path().RUDRA."/core/view/full.tpl");
				//$header->minified->logs();
				if(BROWSER_LOGS){
					Browser::printlogs();
				}
			} else if (is_subclass_of($temp, 'AbstractHandler')) {
				if ($tempClass->hasMethod("invokeHandler" )) {
					RudraX::invokeMethodByReflectionClass($tempClass,$temp,'invokeHandler',array(
					'user' => $user
					));
				}
			} else {
				
			}
		}
	}
}
