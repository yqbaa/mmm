<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class Admin_BaseController extends Common_BaseController {
	public $userInfo;
	public $actions = array();
	protected $pageSize = 20;
	/**
	 * 
	 * Enter description here ...
	 */
	public function init() {
		parent::init();
        Yaf_Registry::set('backEnd', true);
		$frontroot = Yaf_Application::app()->getConfig()->webroot;
		$adminroot = Yaf_Application::app()->getConfig()->adminroot;
		$staticroot = Yaf_Application::app()->getConfig()->staticroot;
		$this->assign("frontroot", $frontroot);
		$this->assign("adminroot", $adminroot);
		$this->assign("staticroot", $staticroot);
		$this->assign("staticPath", $staticroot . '/apps/admin');
		$this->checkRight();
		$this->checkToken();
		$this->checkCookieParams();

	}
   
	/**
	 * 检查token
	 */
	protected function checkToken() {
		if (!$this->getRequest()->isPost()) return true;
		$post = $this->getRequest()->getPost();
		$result = Common::checkToken($post['token']);
		if (Common::isError($result)) $this->output(-1, $result['msg']);
		return true;
	}
	

	

	

	
	/**
	 * 
	 * Enter description here ...
	 */
	public function checkRight() {
		$this->userInfo = Admin_Service_User::isLogin();
		if(!$this->userInfo && !$this->inLoginPage()){
            if($this->isAjax()){
               $this->output(-1,'用户登陆已过期，请刷新页面重新登陆。');
            } else {
                $this->redirect("/Admin/Login/index");
            }
		} else {
			$module = $this->getRequest()->getModuleName();
			$controller = $this->getRequest()->getControllerName();
			$action = $this->getRequest()->getActionName();

			$flag = false;
			if ($controller == 'React' || $controller == 'Sdk_Game_Feedback') {
			    $flag = true;
			}else{
			    $userlevels = $this->getUserLevels();
			    $mc = "_" . $module . "_" . $controller;
			    foreach($userlevels as $key=>$value) {
			        if (strstr($value, $mc)) {
			            $flag = true;
			            break;
			        }
			    }
			}
			if (!$flag) exit('没有权限');
		}
	}

	/**
	 * 
	 * Enter description here ...
	 */
	public function inLoginPage() {
		$module = $this->getRequest()->getModuleName();
		$controller = $this->getRequest()->getControllerName();
		$action = $this->getRequest()->getActionName();
		
		if ($module == 'Admin' && $controller == 'Login' && ($action == 'index' || $action == 'login')) {
			return true;
		}
		return false;
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function getUserMenu() {
		$userInfo = Admin_Service_User::getUser($this->userInfo['uid']);
		$groupInfo = array();
		if ($userInfo['groupid'] == 0) {
			$groupInfo = array('groupid'=>0);
		} else {
			$groupInfo = Admin_Service_Group::getGroup($userInfo['groupid']);
		}
		$menuService = new Common_Service_Menu(Common::getConfig("siteConfig", "mainMenu"), 0);
		list($usermenu, $mainview, $usersite, $userlevels) = $menuService->getUserMenu($groupInfo);
		array_push($userlevels, "_Admin_Initiator", "_Admin_Index", '_Admin_Login');
		return array($usermenu, $mainview, $usersite, $userlevels);
	}
	
	/**取用户系统权限*/
	public function getUserLevels() {
	    $userInfo = Admin_Service_User::getUser($this->userInfo['uid']);
	    $groupInfo = array();
	    if ($userInfo['groupid'] == 0) {
	        $groupInfo = array('groupid'=>0);
	    } else {
	        $groupInfo = Admin_Service_Group::getGroup($userInfo['groupid']);
	    }
	    $menuService = new Common_Service_Menu(Common::getConfig("siteConfig", "mainMenu"), 0);
	    $userlevels = $menuService->getUserLevels($groupInfo);
		array_push($userlevels, "_Admin_Initiator", "_Admin_Index", '_Admin_Login');
		return $userlevels;
	}
	
	public function cookieParams() {
		$module = $this->getRequest()->getModuleName();
		$controller = $this->getRequest()->getControllerName();
		$action = $this->getRequest()->getActionName();
		$name = sprintf('%s_%s_%s', $module, $controller, $action);
	
		$tmp = array();
		$not = array('token','s');
		foreach ($_REQUEST as $key=>$value) {
			if (!in_array($key, $not))$tmp[$key] = $this->getInput($key);
		}
		Util_Cookie::set($name, Common::encrypt(json_encode($tmp)), false, Common::getTime() + (5 * 3600));
	}
	
	/**
	 *
	 * @return boolean
	 */
	public function checkCookieParams() {
		$s = $this->getInput('s');
	
		$module = $this->getRequest()->getModuleName();
		$controller = $this->getRequest()->getControllerName();
		$action = $this->getRequest()->getActionName();
		$name = sprintf('%s_%s_%s', $module, $controller, $action);
	
		$params = json_decode(Common::encrypt(Util_Cookie::get($name), 'DECODE'), true);
	
		if (count($params) && $s) {
			$adminroot = Yaf_Application::app()->getConfig()->adminroot;
	
			$url = sprintf('%s/%s/%s/%s?%s', $adminroot, $module, $controller, $action, http_build_query($params));
			$this->redirect($url);
		}
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $code
	 * @param unknown_type $msg
	 * @param unknown_type $data
	 */
	public function output($status = 1 , $code = 0, $msg = '', $data = array()) {
		header("Content-type:text/json");
		exit(json_encode(array(
			'status' => $status ,
			'code' => $code,
			'msg' => $msg,
			'data' => $data
		)));
	}

	public function setLikeParam ( $name, $value, &$searchParams )
	{
		if ( $value ) {
			$searchParams[ $name ] = array ( 'LIKE', $value );
		}
	}

	public function setEqualParam ( $name, $value, &$searchParams )
	{
		if ( $value ) {
			$searchParams[ $name ] = array ( '=', $value );
		}
	}

	public function setParamBySelectInput ( $name, $value, $all, &$searchParams )
	{
		if ( !is_null ( $value ) && $value != $all ) {
			$searchParams[ $name ] = array ( '=', $value );
		}
	}

	public function setTimeRange ( $start, $emd, $name, &$searchParams )
	{
		if ( $start ) {
			$searchParams[ $name ][] = array ( '>=', strtotime ( $start ) );
		}
		if ( $emd ) {
			$searchParams[ $name ][] = array ( '<', strtotime ( $emd ) );
		}
	}

	public function getPage()
	{
		$page = intval($this->getInput('page'));
		if ($page < 1) $page = 1;
		return $page;
	}

	public function success($msg = '',$data = array()){
		$this->output(1,0,$msg,$data);
	}
	public function error($msg = '',$code = 0){
		$this->output(0,$code,$msg,[]);
	}
}
