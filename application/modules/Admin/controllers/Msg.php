<?php
if (!defined('BASE_PATH')) exit('Access Denied!');


class MsgController extends Admin_BaseController {
	
	public $actions = array(
		'listUrl'    => '/Admin/Msg/index',
		'editUrl'    => '/Admin/Msg/edit',		
		'deleteUrl'  => '/Admin/Msg/delete',
		'editPostUrl'=> '/Admin/Msg/editPost',
		'settingUrl' => '/Admin/Msg/config',
		'sysMsgUrl' => '/Admin/Msg/sysMsg',
		'settingPostUrl' => '/Admin/Msg/config_post',
		'exportUrl'=>'/admin/Msg/export',
		'changeStatusBatUrl' => '/Admin/Msg/changeStatusBat',
		'detailUrl'  => '/Admin/Msg/detail',
		'msgDetailUrl'  => '/Admin/Msg/msgDetail',
		'listPushUrl' => '/Admin/Msg/indexPush',
		'editPushUrl' => '/Admin/Msg/editPush',
		'editPushPostUrl' => '/Admin/Msg/editPushPost',
		'detailPushUrl' => '/Admin/Msg/detailPush',
		'batchUpdateUrl'=>'/Admin/Msg/batchUpdate',
	);
	
	public $perpage = 20;	
	public $msgType  = array(
			201  =>  array('key'=>'custom','txt'=>'游戏内容'),
			202  =>  array('key'=>'custom','txt'=>'专题'),
			203  =>  array('key'=>'custom','txt'=>'分类'),
			204  =>  array('key'=>'custom','txt'=>'活动'),
			205  =>  array('key'=>'custom','txt'=>'链接'),
			206  =>  array('key'=>'custom','txt'=>'礼包'),
			207  =>  array('key'=>'custom','txt'=>'文本信息')

	);
	
	public $reciver = array( 
			Game_Service_Msg::TARGET_SPECIFIED_ACCOUNT => '指定账号', //1
			Game_Service_Msg::TARGET_ALL_ACOUNT => '所有账号', //0
			Game_Service_Msg::TARGET_ALL_USER => '全部用户', //2
			Game_Service_Msg::TARGET_OTHER => '其他', //3
	);
	
	public $pushType = array(
			201 => Client_Service_PushMsg::PushMsg_TYPE_GAMEID,
			202 => Client_Service_PushMsg::PushMsg_TYPE_SUBJECT,
			203 => Client_Service_PushMsg::PushMsg_TYPE_CATEGOTY,
			204 => Client_Service_PushMsg::PushMsg_TYPE_ACTIVITY,
			205 => Client_Service_PushMsg::PushMsg_TYPE_LINK,
			206 => Client_Service_PushMsg::PushMsg_TYPE_GIFT,
			207 => Client_Service_PushMsg::PushMsg_TYPE_TEXT,
	);
	
	
	public function indexAction () {		
		$page = intval($this->getInput('page'));
		if ($page < 1) $page = 1;
		$s = $this->getInput(array('toptype','totype','last_author','start_time','end_time','title','type','operate_status','export'));
		$params = array();
		if ($s['type']){
			$params['type'] = $s['type'];
			$webroot = Common::getWebRoot();
			if($params['type'] == 901){
				$params['type'] = 205;
				$params['contentId'] = $webroot.'/client/prize/index';
			}
			if($params['type'] == 902){
				$params['type'] = 205;
				$params['contentId'] = $webroot.'/client/Mall/index';
			}
		}
		if ($s['toptype']) $params['toptype'] = $s['toptype'];
		if ($s['totype']) $params['totype'] = $s['totype'] - 1;
		if ($s['operate_status']) $params['operate_status'] = $s['operate_status'] - 1;
		if ($s['last_author']) $params['last_author'] = array('LIKE',$s['last_author']);
		if ($s['title']) $params['title'] = array('LIKE',$s['title']);
		if ($s['start_time']) $params['start_time'] = array('<=',strtotime($s['start_time']));
		if ($s['end_time']) $params['end_time'] = array('>=',strtotime($s['end_time']));
		if ($s['end_time'] && $s['start_time']) {
			$params['end_time'][0] = array('>=',strtotime($s['start_time']));
			$params['end_time'][1] = array('>=',strtotime($s['end_time']));
		}
		$params['top_type'] = 200;
		$swich = Game_Service_Config::getValue('msg_yunying');
		list($total, $result) = Game_Service_Msg::getList($page, $this->perpage, $params, array('id'=>'DESC'));
		foreach($result as $key => $value){
			if($value['type'] == 205){
				$webroot = Common::getWebRoot();
				if($value['contentId'] == $webroot.'/client/prize/index'){
					$result[$key]['type'] = 901;
				}
				if($value['contentId'] == $webroot.'/client/Mall/index'){
					$result[$key]['type'] = 902;
				}
			}
		}
		$msgType = $this->msgType;
		$msgType['901'] = array('key'=>'custom','txt'=>'积分抽奖');
		$msgType['902'] = array('key'=>'custom','txt'=>'积分商城');
		$this->assign('searchParam', $s);
		$this->assign('total',$total);	
		$this->assign('msgType',$msgType);
		$this->assign('toptype',$s['toptype']);	
		$this->assign('result', $result);
		$this->assign('swich',$swich);
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $this->actions['listUrl'].'?'.http_build_query($s).'&'));		
	}	
	
	/**
	 *　用户统计列表导出数据
	 * Get phrase list
	 */
	public function exportAction() {
		$page = intval($this->getInput('page'));
		if ($page < 1) $page = 1;
		$s = $this->getInput(array('toptype','totype','last_author','start_time','end_time','title','type','operate_status','export'));
		$params = array();
		if ($s['type']) $params['type'] = $s['type'];
		if ($s['toptype']) $params['totype'] = $s['toptype'];
		if ($s['totype']) $params['totype'] = $s['totype'] - 1;
		if ($s['operate_status']) $params['operate_status'] = $s['operate_status'] - 1;
		if ($s['last_author']) $params['last_author'] = array('LIKE',$s['last_author']);
		if ($s['title']) $params['title'] = array('LIKE',$s['title']);
		if ($s['start_time']) $params['start_time'] = array('>=',strtotime($s['start_time']));
		if ($s['end_time']) $params['end_time'] = array('>=',strtotime($s['end_time']));
		if ($s['end_time'] && $s['start_time']) {
			$params['end_time'][0] = array('>=',strtotime($s['start_time']));
			$params['end_time'][1] = array('>=',strtotime($s['end_time']));
		}
		$params['top_type'] = 200;
	
		//excel-head
		$filename = '消息导出_' . date('Ymdhis');
		Util_Csv::putHead($filename);
		$title = array(array('标题','ID','类型','内容','开始时间','结束时间','发送方式','状态','最后维护人'));
		Util_Csv::putData($title);
		//循环分页查询输出
		while(1){
			list($total, $result) = Game_Service_Msg::getList($page, $this->perpage, $params, array('start_time'=>'DESC','id'=>'DESC'));
			if (!$result) break;
	
			$tmp = array();
			foreach ($result as $key=>$value) {
				$tmp[] = array(
						$value['title'],
						$value['id'],
						Game_Service_Msg::$msgType[$value['type']]['txt'],
						html_entity_decode(preg_replace("/\,/", "，", $value['msg'])),
						date('Y-m-d H:i:s',$value['start_time']),
						date('Y-m-d H:i:s',$value['end_time']),
					    Game_Service_Msg::$reciver[$value['totype']],
					    Game_Service_Msg::$status[$value['operate_status']],
					    $value['last_author']."\t");
			}
			Util_Csv::putData($tmp);
			$page ++;
		}
		exit;
	}
	
	private function _export($list, $filename = '消息导出') {
		ini_set('memory_limit', '1024M');
		header('Content-Type: application/vnd.ms-excel;charset=GB2312');
		$filename .= $filename .date('Y-m-d'). '.csv';
		header('Content-Type: text/csv');
		header('Content-Disposition: attachment;filename=' . iconv('utf8', 'gb2312', $filename));
		$out = fopen('php://output', 'w');
		fputcsv($out, array(chr(0xEF) . chr(0xBB) . chr(0xBF)));
		foreach ($list as $v) {
			fputcsv($out, $v);
		}
		exit;
	}
	
	/**
	 * 导入
	 */
	public function importAction() {
		if(!empty($_FILES['msgReciverCsv']['tmp_name'])) {
echo <<<SCRIPT
			<script>
					window.parent.document.getElementById("sendInputTxt").value = "";
					function _addcsvRow(d) {
						if(window.parent.document.getElementById("sendInputTxt").value != "") {
							window.parent.document.getElementById("sendInputTxt").value += ",";
						}
						window.parent.document.getElementById("sendInputTxt").value += d;
					}
			</script>
SCRIPT;
			ob_flush();  
			flush();
			$file    = $_FILES['msgReciverCsv']['tmp_name'];
			$handle  = fopen($file,'r');
			if(!$handle) exit;
			$count = 0;
			$dataList = array();
			while(($row = fgetcsv($handle,1000,",")) !== false) {
				foreach($row as $k => &$v) {
					$v = mb_convert_encoding($v, 'utf-8', 'gbk');
				}
				$dataList[] = $row;
			}
			if(count($dataList) <= 1) exit;

			foreach($dataList as $data) {
				if(empty($data[0])) continue;
				echo '<script>_addcsvRow("'.$data[0].'");</script>';
				ob_flush();  
				flush();
				$count ++;
			}
			echo '<script>alert("已读取：'.$count.' 条");</script>';
		}
	}
	
	/*
	 * 系统消息
	 */
	public function sysMsgAction () {
		$curr_time = date('Y-m-d',Common::getTime());
    	$start_time = strtotime($curr_time.' 00:00:00');
    	$end_time = strtotime($curr_time.' 23:59:59');
    	$search['create_time'][0] = array('>=', $start_time);
    	$search['create_time'][1] = array('<=', $end_time);
    	$search['type'] = array('IN',array(103,107,111));
		$currSendAcoupTotal = Game_Service_Msg::getUnReadCount($search);
		$search['type'] = 111;
		$currSendGameTickTotal = Game_Service_Msg::getUnReadCount($search);
		$search['type'] = 102;
		$currExpireAcoupTotal = Game_Service_Msg::getUnReadCount($search);
		$search['type'] = 108;
		$currExpirePointTotal = Game_Service_Msg::getUnReadCount($search);
		$keys = array('msg_yunying','msg_atickexpire','msg_atickgive','msg_pointexpire');
		$config = array();
		foreach ($keys as $value){
			$config[$value] = Game_Service_Config::getValue($value);
		}
		$this->assign('config',$config);
		$this->assign('sendAcoupTotal', $currSendAcoupTotal);
		$this->assign('sendGameTickTotal', $currSendGameTickTotal);
		$this->assign('expireAcoupTotal', $currExpireAcoupTotal);
		$this->assign('expirePointTotal', $currExpirePointTotal);
	}
	
	/*
	 * 系统消息详情
	 */
    public function msgDetailAction () {
    	$type   = $this->getInput('type');
    	$page = intval($this->getInput('page'));
    	if ($page < 1) $page = 1;
    	$params = array();
    	if ($type) {
    		$params['type'] = $type;
    		if($type == 103){
    			$params['type'] = array('IN',array(103,107,111));
    		}
    	}
    	$params['top_type'] = 100;
    	list($total, $result) = Game_Service_Msg::getList($page, $this->perpage, $params, array('create_time'=>'DESC','id'=>'DESC'));
    	
    	$curr_time = date('Y-m-d',Common::getTime());
    	$start_time = strtotime($curr_time.' 00:00:00');
    	$end_time = strtotime($curr_time.' 23:59:59');
    	$search['create_time'][0] = array('>=', $start_time);
    	$search['create_time'][1] = array('<=', $end_time);
    	$search['type'] = $type;
    	if($type == 103){
    	    $search['type'] = array('IN',array(103,107,111));
    	}
    	$currTotal = Game_Service_Msg::getUnReadCount($search);
    	
    	$usersInfo = array();
    	foreach($result as $key=>$value){
    		$msgInfo = Game_Service_Msg::getMap(array('msgid'=>$value['id']));
    		$userInfo = Account_Service_User::getUserInfo(array('uuid'=>$msgInfo['uid']));
    		$usersInfo[$value['id']]['avatar'] = $userInfo['avatar'];
    		$usersInfo[$value['id']]['uname'] = $userInfo['uname'];
    		$usersInfo[$value['id']]['nickname'] = $userInfo['nickname'];
    		$usersInfo[$value['id']]['content'] = $value['title'];
    		$usersInfo[$value['id']]['read_time'] = $msgInfo['read_time'];
    		$usersInfo[$value['id']]['uid'] = $msgInfo['uid'];
    	}
    	
    	$this->assign('usersInfo',$usersInfo);
    	$this->assign('currTotal',$currTotal);
    	$this->assign('type',$type);
    	$this->assign('total',$total);
    	$this->assign('msgType',$this->msgType);
    	$this->assign('result', $result);
    	$this->assign('pager', Common::getPages($total, $page, $this->perpage, $this->actions['msgDetailUrl'].'?'.http_build_query($params).'&'));
		
	}
	
	/**
	 * 明细
	 */
	public function detailAction() {
	   $result    = array();
	   $id   = $this->getInput('id');
	   $page = intval($this->getInput('page'));
	   if ($page < 1) $page = 1;
	   $msgInfo = Game_Service_Msg::getMsg($id);
	   list($total,$maps) = Game_Service_Msg::getMapList($page, $this->perpage, array('msgid'=>$msgInfo['id']));
	   foreach($maps as $k => $v) {
			 $uInfo = Account_Service_User::getUserInfo(array('uuid'=>$v['uid']));
			 $result[] = array(
			 		        'id'     => $v['id'],
							'pic'     => $uInfo['avatar'],
							'nick'    => $uInfo['nickname'],
							'account' => $uInfo['uname'],
							'uuid' => $v['uid'],
							'time'    => !empty($v['read_time']) ? date('Y-m-d H:i:s',$v['read_time']) : ''
			 );						
		}
		$params['id'] = $id;
		$this->assign('msgTitle',$msgInfo['title']);
		$this->assign('total',$total);	
		$this->assign('id',$id);
		$this->assign('result', $result);
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $this->actions['detailUrl'].'?'.http_build_query($params).'&'));		
			
	}

	public function editAction() {
		$info = array();
		$id   = intval($this->getInput('id'));		
		if($id) {
			$sendInput = array();
			$info = Game_Service_Msg::getMsg($id);
		} else {
			$info['sort']       = 0;
			$info['start_time'] = time();
			$info['end_time']   = strtotime('+7 days');
		}
		$msgType = $this->msgType;
		$msgType['901'] = array('key'=>'custom','txt'=>'积分抽奖');
		$msgType['902'] = array('key'=>'custom','txt'=>'积分商城');
		if($info['type'] == 205){
			$webroot = Common::getWebRoot();
			if($info['contentId'] == $webroot.'/client/prize/index'){
				$info['type'] = 901;
			}
			if($info['contentId'] == $webroot.'/client/Mall/index'){
				$info['type'] = 902;
			}
		}
		$this->assign('msgType', $msgType);
		$this->assign('reciver', $this->reciver);
		$this->assign('info', $info);
	}	
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function editPostAction() {
		$msgBody = $this->getPost(array(
				'id',
				'toptype',
				'type',
				'contentId',
				'sendInput',
				'totype',
				'title',
				'msg',
				'start_time',
				'end_time',
				'operate_status',
				'install_attention',
		        'other'));	
		$msgBody = $this->_cookData($msgBody);
		if(empty($msgBody['id'])) {
			if($msgBody['totype'] == Game_Service_Msg::TARGET_ALL_ACOUNT ||
				$msgBody['totype'] == Game_Service_Msg::TARGET_SPECIFIED_ACCOUNT) {
				$ret = Game_Service_Msg::addMsg($msgBody);
			} else if ($msgBody['totype'] == Game_Service_Msg::TARGET_ALL_USER ||
				$msgBody['totype'] == Game_Service_Msg::TARGET_OTHER) {  //通知栏消息
				$currTime = floor(Common::getTime()/60)*60;
				$pushData = array(
					'id'=>'',
					'type'=>$msgBody['type'],
					'reciver_type'=>$this->checkReciverType($msgBody['install_attention']),
					'game_id'=>$this->getGameId($msgBody),
					'title'=>$msgBody['title'],
					'msg'=>$msgBody['msg'],
					'contentId'=>$msgBody['contentId'],
					'status'=>$msgBody['operate_status'],
					'last_author'=>$this->userInfo['username'],
					'start_time'=>$msgBody['start_time'],
					'end_time'=>$msgBody['end_time'],
					'create_time'=>$currTime,
					'update_time'=>$currTime,
				);
				$ret = Client_Service_PushMsg::add($pushData);
				if(!$pushData['reciver_type']){ //已经安装和以关注不发送金立push消息
				    Common::getQueue()->push('game_client_temporary_msg',$ret);
				}
				
			}
		} else {
			$ret = Game_Service_Msg::updateMsgInfo($msgBody, intval($msgBody['id']));
		}

		if (!$ret) $this->output(-1, '操作失败');
		$this->output(0, '操作成功.'); 		
	}
	
	private function getGameId($info) {
		if($info['type'] == 206){
			$giftInfo = Client_Service_Gift::getGift($info['contentId']);
			return $giftInfo['game_id'];
		} else if($info['type'] == 204){
			$hdInfo = Client_Service_Hd::getHd($info['contentId']);
			return $hdInfo['game_id'];
		}
	}
	
	
	private function checkReciverType($info) {
       if(count($info) == 2 ) {
       	  $reciveType = Client_Service_PushMsg::PushMsg_RECIVER_ATTENTION_INSTALL;
       } else if($info[0] == 1) {
       	   $reciveType = Client_Service_PushMsg::PushMsg_RECIVER_ATTENTION;
       } else if($info[0] == 2) {
       	   $reciveType = Client_Service_PushMsg::PushMsg_RECIVER_INSTALL;
       }
       return $reciveType;
	}

	/**
	 * 
	 * Enter description here ...
	 */
	private function _cookData($info) {
		if(empty($info['title'])) $this->output(-1, '消息标题不能为空');
		if(Util_String::strlenForUtf8($info['title']) > 16) {
			$this->output(-1, '消息标题长度不能大于16');
		}
		if(empty($info['msg'])) $this->output(-1, '消息内容不能为空');
		if(Util_String::strlenForUtf8($info['msg']) > 100) {
			$this->output(-1, '消息内容长度不能大于100');
		}
		if($info['type'] < 0 ) $this->output(-1,'请填写消息类型');
		$webroot = Yaf_Application::app()->getConfig()->webroot;
		if($info['type'] == 901){
			$info['type'] = 205;
			$info['contentId'] = $webroot.'/client/prize/index';
		}
		if($info['type'] == 902){
			$info['type'] = 205;
			$info['contentId'] = $webroot.'/client/Mall/index';
		}
		if($info['type']){
			if(!Game_Service_Msg::isNewMsgEnabled($info['type'])) {
				$this->output(-1,'运营消息已关闭');
			}	
		}
		if(in_array($info['type'], array(201,202,203,204,205,206,207))) {
			if(empty($info['contentId'])){
				$this->output(-1,'内容ID不能为空');
			}
			$this->checkMsg($info);
		}		
		if($info['totype'] < 0) $this->output(-1,'请选择发送方式');
		if($info['totype'] == 1 && empty($info['sendInput'])) $this->output(-1,'请填写账号');
		if($info['totype'] == 3 && empty($info['install_attention'])) $this->output(-1,'请选择接收对象');
		if(empty($info['start_time'])) $this->output(-1, '开始时间不能为空');
		if(empty($info['end_time'])) $this->output(-1, '结束时间不能为空');
		if($info['end_time'] <= $info['start_time']) $this->output(-1,'结束时间不能小于等于开始时间');	
		
		$info['update_time'] = time();		
		$info['start_time']  = strtotime($info['start_time']);
		$info['end_time']    = strtotime($info['end_time']);		
		$info['top_type']    = floor($info['type']/100)*100;
		$userInfo 			 = Admin_Service_User::isLogin();
		$info['last_author'] = $userInfo['username'];		
		
		return $info;
	}
		
	/**
	 * 
	 * Enter description here ...
	 */
	public function deleteAction() {
		$id = intval($this->getInput('id'));
		$result = Game_Service_Msg::deleteMsg($id);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
	
	/**
	 * 批量修改状态
	 */
	public function changeStatusBatAction() {
		$input  = $this->getInput(array('ids','action'));
		if (!count($input['ids'])) $this->output(-1, '没有可操作的项.');
		$status    = $input['action'] == 'open' ? 1 : 0;
		$res = Game_Service_Msg::updateMsgBat($input['ids'], $status);		
		if($res) {
			$this->output(0,'操作成功');
		} else {
			$this->output(-1,'操作失败');
		}		
	}
	
	/**
	 * 消息设置
	 */
	public function configAction() {
			$keys = array('msg_yunying','msg_atickexpire','msg_atickgive','msg_pointexpire','msg_gametickgive');
			$config = array();
			foreach ($keys as $value){
				$config[$value] = Game_Service_Config::getValue($value);
			}
			$this->assign('config',$config);
	}	
	
	/**
	 * 消息设置提交
	 */
	public function config_postAction(){
		$request  = $this->getInput(array('msg_yunying','msg_atickexpire','msg_atickgive','msg_pointexpire','msg_gametickgive'));
		foreach($request as $key=>$value) {
			Game_Service_Config::setValue($key, $value);
		}
		$this->output(0,'操作成功');
	}
	
	/**
	 * 通知栏消息列表
	 * Enter description here ...
	 */
	public function indexPushAction() {
		$page = intval($this->getInput('page'));
		if ($page < 1) $page = 1;
		$s = $this->getInput(array('status','last_author','start_time','end_time','title','type','status'));
		$params = array();
		if ($s['type']){
			$params['type'] = $s['type'];
			$webroot = Common::getWebRoot();
			if($params['type'] == 901){
				$params['type'] = 205;
				$params['contentId'] = $webroot.'/client/prize/index';
			}
			if($params['type'] == 902){
				$params['type'] = 205;
				$params['contentId'] = $webroot.'/client/Mall/index';
			}
		}
		if ($s['status']) $params['status'] = $s['status'] - 1;
		if ($s['operate_status']) $params['operate_status'] = $s['operate_status'] - 1;
		if ($s['last_author']) $params['last_author'] = array('LIKE',$s['last_author']);
		if ($s['title']) $params['title'] = array('LIKE',$s['title']);
		if ($s['start_time']) $params['start_time'] = array('<=',strtotime($s['start_time']));
		if ($s['end_time']) $params['end_time'] = array('>=',strtotime($s['end_time']));
		if ($s['end_time'] && $s['start_time']) {
			$params['end_time'][0] = array('>=',strtotime($s['start_time']));
			$params['end_time'][1] = array('>=',strtotime($s['end_time']));
		}
		list($total, $msgs) = Client_Service_PushMsg::getList($page, $this->perpage,$params ,array('start_time'=>'DESC','id'=>'DESC'));
		foreach($msgs as $key => $value){
			if($value['type'] == 205){
				$webroot = Common::getWebRoot();
				if($value['contentId'] == $webroot.'/client/prize/index'){
					$msgs[$key]['type'] = 901;
				}
				if($value['contentId'] == $webroot.'/client/Mall/index'){
					$msgs[$key]['type'] = 902;
				}
			}
		}
		$msgType = $this->msgType;
		$msgType['901'] = array('key'=>'custom','txt'=>'积分抽奖');
		$msgType['902'] = array('key'=>'custom','txt'=>'积分商城');
		
		$this->assign('s', $s);
		$this->assign('msgs', $msgs);
		$this->assign('msgType', $msgType);
		$this->assign('total', $total);
		$url = $this->actions['listPushUrl'].'/?' . http_build_query($s) . '&';
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));
	}
	
	/**
	 *
	 * edit an subject
	 */
	public function editPushAction() {
		$id = $this->getInput('id');
		$info = Client_Service_PushMsg::get(intval($id));
		$this->assign('info', $info);
		$this->assign('msgType', $this->msgType);
	}
	/**
	 *
	 * Enter description here ...
	 */
	public function editPushPostAction() {
		$info = $this->getPost(array('id','reciver_type', 'type', 'title', 'msg', 'contentId', 'status', 'start_time', 'end_time', 'update_time'));
		$info = $this->_cookPushData($info);
		$currTime = floor(Common::getTime()/60)*60;
		$info['update_time'] = $currTime;
		$ret = Client_Service_PushMsg::update($info, intval($info['id']));
		if (!$ret) $this->output(-1, '操作失败');
		$this->output(0, '操作成功.');
	}
	
	/*
	 * 通知栏消息详情
	*/
	public function detailPushAction () {
		$id   = $this->getInput('id');
		$page = intval($this->getInput('page'));
		if ($page < 1) $page = 1;
		$params = $s = array();
		$msgInfo = Client_Service_PushMsg::get($id);
		$params['msgid'] = $msgInfo['id'];
		$s['id'] = $id;
		list($total, $result) = Client_Service_PushMsgLog::getList($page, $this->perpage, $params, array('id'=>'DESC'));
		 
		$usersInfo = array();
		foreach($result as $key=>$value){
			$userInfo = Account_Service_User::getUserInfo(array('uuid'=>$value['uuid']));
			$usersInfo[$value['uuid']]['avatar'] = $userInfo['avatar'];
			$usersInfo[$value['uuid']]['uname'] = $userInfo['uname'];
			$usersInfo[$value['uuid']]['nickname'] = $userInfo['nickname'];
		}
		 
		$this->assign('usersInfo',$usersInfo);
		$this->assign('total',$total);
		$this->assign('msgInfo',$msgInfo);
		$this->assign('result', $result);
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $this->actions['detailPushUrl'].'?'.http_build_query($s).'&'));
	
	}
	
	//批量开启，关闭
	function batchUpdateAction() {
		$info = $this->getPost(array('action', 'ids'));
		if (!count($info['ids'])) $this->output(-1, '没有可操作的项.');
		if($info['action'] =='open'){
			$ret = Client_Service_PushMsg::updateStatus($info['ids'], 1);
		} else if($info['action'] =='close'){
			$ret = Client_Service_PushMsg::updateStatus($info['ids'], 0);
		}
	
		if (!$ret) $this->output('-1', '操作失败.');
		$this->output('0', '操作成功.');
	}
	
	/**
	 *
	 * Enter description here ...
	 * preg_match("/<[^>]*>/", $info['title'])
	 */
	private function _cookPushData($info) {
		if(!$info['title']) $this->output(-1, '消息标题不能为空.');
		$title = html_entity_decode($info['title']);
		if(preg_match("/<[^>]*>/", $title)) $this->output(-1, '活动名称不能包含特殊标记(< >).');
		if(!$info['msg']) $this->output(-1, '内容不能为空.');
		if(!$info['start_time']) $this->output(-1, '开始时间不能为空.');
		if(!$info['end_time']) $this->output(-1, '结束时间不能为空.');
		$info['start_time'] = strtotime($info['start_time']);
		$info['end_time'] = strtotime($info['end_time']);
		if($info['end_time'] <= $info['start_time']) $this->output(-1, '开始时间不能大于或者等于结束时间.');
		$this->checkMsg($info);
		return $info;
	}
	
	/**
	 *
	 * Enter description here ...
	 */
	public function checkMsg($info) {
			switch ($info['type'])
			{
				case 201 : //游戏内容
					$adInfo = Resource_Service_Games::getResourceByGames($info['contentId']);
					if(empty($adInfo['status'])) $this->output(-1, '游戏不存在或者未上线');
					break;
				case 202 : //专题
					$time = time();
					$adInfo = Client_Service_Subject::getSubject($info['contentId']);
					if(empty($adInfo['status'])) $this->output(-1, '专题不存在或未上线');
					break;
				case 203 : //分类
					$adInfo = Resource_Service_Attribute::getBy(array('id'=>$info['contentId'],'at_type'=>1));
					if(empty($adInfo['status'])) $this->output(-1, '分类不存在或未上线');
					break;
				case 204 : //活动
					$adInfo = Client_Service_Hd::getHd(intval($info['contentId']));
					if(empty($adInfo['status'])) $this->output(-1,'活动不存在或未上线');
					break;
				case 205 : //链接
					if(empty($info['contentId'])) $this->output(-1,'链接不能为空');
					break;
				case 206 : //礼包
					$adInfo = Client_Service_Gift::getGift(intval($info['contentId']));
					if(!$adInfo['status']) $this->output(-1,'礼包不存在或未上线');
					break;
				case 207 : //文本信息
					if(empty($info['contentId'])) $this->output(-1,'文本信息不能为空');
					break;
			}
	}
}