<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class Member_Service_Member{
	private static $mStatus = array(
		0 => '未审核',
		1 => '正常',
		2 => '禁用',
	);
	private static $mLevel = array(
		0 => '初入牛犊',
		1 => '小试牛刀',
		2 => '开疆扩土',
		3 => '富甲一方',
		4 => '绝对土豪',
		5 => '人生巅峰',
	);
	private static $mLevelCondition = array(
		0 => array(
			'recommend' => 0,  //推荐人
			'totalMoney' => 2000, //排单金额
		) ,
		1 => array(
			'recommend' => 0,  //推荐人
			'totalMoney' => 10000,
		) ,
		2 => array(
			'recommend' => 2,  //推荐人
			'totalMoney' => 50000,
		) ,
		3 => array(
			'recommend' => 10,  //推荐人
			'totalMoney' => 200000,
		) ,
		4 => array(
			'recommend' => 30,  //推荐人
			'totalMoney' => 500000,
		) ,
		5 => array(
			'recommend' => 100,  //推荐人
			'totalMoney' => 1000000,
		) ,
	);



	public static function getList($page = 1, $limit = 10, $params = array() ,$orderBy = array()) {
		if ($page < 1) $page = 1; 
		$start = ($page - 1) * $limit;
		$ret = self::_getDao()->getList($start, $limit, $params,$orderBy);
		if($ret){
			self::rebuildMemberList($ret);
		}
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}
	public static function rebuildMemberList(&$list){
		foreach($list as &$user){
			if($user['father_id']){
				$fatherData = self::getFather($user['father_id']);
				$user['fatherName'] = $fatherData['username'];
			}else{
				$user['fatherName'] = '';
			}
			$user['statusTxt'] = self::$mStatus[$user['status']];
			$user['levelName'] = self::$mLevel[$user['level']];
			$user['sameIpCount'] = self::sameIpCount($user['reg_ip']);
		}
	}
	public static function sameIpCount($ip){
		$where['reg_ip'] = $ip;
		$total = self::_getDao()->count($where);
		return $total;
	}

	public static function getFather($fatherId){
		$where['id'] = $fatherId;
		$ret = self::getBy($where);
		return $ret ;
	}

	public static function getBy($where = array(),$order = array()){
		return self::_getDao()->getBy($where,$order);
	}



	public static function updateUser($data, $uid) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($uid));
	}
	

	public static function deleteUser($uid) {
		return self::_getDao()->delete(intval($uid));
	}
	

	public static function addUser($data) {
		if (!is_array($data)) return false;
		return self::_getDao()->insert($data);
	}
	public static function muiltAdd($data){
		self::_getDao()->mutiFieldInsert($data);
	}
	


	/**
	 * 
	 * @return Admin_Dao_User
	 */
	private static function _getDao() {
		return Common::getDao("Member_Dao_Member");
	}
}
