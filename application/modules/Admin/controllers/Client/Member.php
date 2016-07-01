<?php
/**
 * Created by PhpStorm.
 * User: xiaogu
 * Date: 16-6-30
 * Time: 下午2:38
 */
if (!defined('BASE_PATH')) exit('Access Denied!');

class Client_MemberController extends Admin_BaseController {
    public $actions = [
        'listUrl' => '/Admin/Client_Member/index',
        'addUrl' =>  '/Admin/Client_Member/add',
        'addPostUrl' =>  '/Admin/Client_Member/addPost',
    ];

    public function indexAction(){
        $search = $this->getInput(array('id','username','start_time','end_time','phone','status','ip'));
        $searchParams = $this->search($search);
        $page = $this->getPage();
        list($total, $list) = Member_Service_Member::getList($page,$this->pageSize,$searchParams,array('id'=>'desc'));

        $this->assign("total",$total);
        $this->assign("list",$list);
        $this->assign("search",$search);
        $url = $this->actions['listUrl'].'/?' . http_build_query($search) . '&';
        $this->assign('pager', Common::getPages($total, $page, $this->pageSize, $url));
    }
    private function search($params){
        $searchParams = [];
        if($params['id']){
            $searchParams['id'] = $params['id'];
        }
        if($params['phone']){
            $searchParams['phone'] = $params['phone'];
        }
        if($params['status']){
            $searchParams['status'] = $params['status'];
        }
        if($params['ip']){
            $searchParams['ip'] = $params['ip'];
        }
        if($params['username']){
            $searchParams['username'] = $params['username'];
        }
        
        $this->setTimeRange($params['start_time'], $params['end_time'], 'create_time',$searchParams);

        return $searchParams;

    }

    function addMemberAction(){
        for($i=0;$i<100;$i++) {
            for ($j = 0; $j < 1000; $j++) {
                $data['username'] = $this->randStr(8);
                $data['phone'] = '1' . $this->randNum(9);
                $data['password'] = md5(123456);
                $data['create_time'] = time();
                $data['source'] = 0;
                $data['level'] = rand(0, 5);
                $data['status'] = rand(0, 2);
                $data['weixin'] = 'weixin';
                $insert[] = $data;
            }
            Member_Service_Member::muiltAdd($insert);
            $insert = [];
        }

        return ;
    }
    function rand($length,$str){
        $code = '';
        for($i=0;$i<$length;$i++){
            $rand = rand(0,9);
            $code.=$str[$rand];
        }
        return $code;
    }
    function randStr($length){
        $str = 'qwertyuioplkjhgfdsazxcvbnm,./=-*&^%$#@!`～|+——-？》《';
        return  $this->rand($length,$str);
    }
    function randNum($length){
        $number = '1234567890';
        return  $this->rand($length,$number);
    }

    function detail(){
        $id = $this->getInput('id');
        $where['id'] = $id;
        $userBasic = Member_Service_Member::getBy($where);
    }


}