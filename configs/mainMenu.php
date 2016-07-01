<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
//菜单配置
$config['Admin_System'] = array(
    'name' => '系统',
    'items'=>array(
        array(
            'name' => '用户',
            'items' => array(
                'Admin_User',
                'Admin_Group',
                'Admin_User_passwd'
            ),
        )
    )
);

$config['Admin_Content'] = array(
    'name'=>'会员管理',
    'items'=>array(
        array(
            'name'=>'用户管理',
            'items'=>array(
                'Admin_Client_Member_Index',
                'Admin_Client_Member_GoldCoins',
                'Admin_Client_Member_Relation',
                'Admin_Client_Member_Withdrawals',
            )
        ),
    )
);

$config['Admin_YunYing'] = array(
    'name'=>'内容运营',
    'items' => array(
        array(
            'name'=>'激活码',
            'items'=>array(
                'Admin_ActiveCode_Index',
                'Admin_ActiveCode_Generate',
            )
        ),
        array(
            'name'=>'排单币',
            'items'=>array(
                'Admin_Schedule_Index',
                'Admin_Schedule_Generate',
                'Admin_Schedule_Config',
            )
        ),

        array(
            'name'=>'资讯管理',
            'items'=>array(
                'Admin_News_Index',
            )
        ),
        array(
            'name'=>'配置管理',
            'items'=>array(
                'Admin_Config_Reward',
                'Admin_Config_Withdrawals',
                'Admin_Client_Insert',
            )
        )
    )
);

$config['Admin_Store'] = array(
    'name'=>'匹配系统',
    'items' => array(
        array(
            'name'=>'索罗斯支出',
            'items' => array(
//                'Admin_Resource_Category',
//                'Admin_Resource_Attribute',
//                'Admin_Resource_Otherattr',
//                'Admin_Resource_Type',
//                'Admin_Resource_Pgroup',
//                'Admin_Resource_Label',
//                'Admin_Resource_Property',
//                'Admin_Resource_Upgrade',
            )
        ),
        array(
            'name'=>'索罗斯收入',
            'items' => array(
//                'Admin_Resource_Games_index',
//                'Admin_Resource_Games_entering',
//                'Admin_Freedl_Cugd',
//                'Admin_Resource_Sync_index',
            )
        )
    )
);


$entry = Yaf_Registry::get('config')->adminroot;
$view = array(
    //系统-用户
    'Admin_User'=>array('用户管理', $entry . '/Admin/User/index'),
    'Admin_Group'=>array('用户组管理',$entry . '/Admin/Group/index'),
    'Admin_User_passwd'=>array('修改密码',$entry . '/Admin/User/passwd'),

    //用户管理
    'Admin_Client_Member_Index'=>array('用户列表', $entry . '/Admin/Client_Member/index'),
    'Admin_Client_Member_GoldCoins' => array("金币赠送" , $entry , '/Admin/Client_Member/goldCoins'),
    'Admin_Client_Member_Relation' => array("会员关系网" , $entry , '/Admin/Client_Member/relation'),
    'Admin_Client_Member_Withdrawals' =>  array("提现管理" , $entry , '/Admin/Client_Member/withdrawals'),


    //内容运营-激活码
    'Admin_ActiveCode_Index' => array('激活码管理',$entry . '/Admin/ActiveCode/index'),
    'Admin_ActiveCode_Generate' => array('生成激活码',$entry . '/Admin/ActiveCode/generate'),

    //排单币
    'Admin_Schedule_Index' => array('排单币管理',$entry . '/Admin/Schedule/index'),
    'Admin_Schedule_Generate' => array('生成排单币',$entry . '/Admin/Schedule/generate'),
    'Admin_Schedule_Config' => array('排单币配置',$entry . '/Admin/Schedule/config'),

    //内容运营-资讯管理
    'Admin_News_Index' => array('新闻管理',$entry . '/Admin/News/index'),

    //内容运营-配置管理
    'Admin_Config_Reward' => array('奖金配置',$entry . '/Admin/Config/reward'),
    'Admin_Config_Withdrawals' => array('提现配置',$entry . '/Admin/Config/withdrawals'),
    'Admin_Client_Insert' => array('利息配置',$entry . '/Admin/Config/insert'),



);

$extends = array(
//    'Admin_Client_Ad_Recommendlist' => array(
//        'Admin_Client_Ad_Recommendnew',
//        'Admin_Client_Ad_Recommendday',
//        'Admin_Client_Ad_Recommendbanner',
//        'Admin_Client_Ad_Recommendtext',
//    ),
//    'Admin_Client_Ad_Recommendold' => array(
//        'Admin_Client_Ad_Recommend',
//        'Admin_Client_Ad_Turn',
//        'Admin_Client_Ad_Picture',
//        'Admin_Client_Ad_Recpic',
//        'Admin_Client_Ad_Subject',
//    ),
//    'Admin_Ad_Recommendlist' => array(
//        'Admin_Ad_Recommendnew',
//        'Admin_Ad_Recommendday',
//        'Admin_Ad_Recommendbanner',
//        'Admin_Ad_Recommendtext',
//    ),
//    'Admin_Game_Webgame' => array(
//        'Admin_Game_Gameopen',
//        'Admin_Client_Web',
//        'Admin_Client_Navigation',
//        'Admin_Game_Reservedactivity',
//        'Admin_Game_Testgame',
//        'Admin_Game_Sendlog'
//    ),
//    'Admin_Game_Singlegame' => array(
//        'Admin_Client_Single',
//    ),
//    'Admin_Mall_Goods' => array(
//        'Admin_Point_Prize',
//    ),
//    'Admin_Mall_Category' => array(
//        'Admin_Mall_Goods',
//        'Admin_Point_Prize',
//    ),
//    'Admin_Festival_Index' => array(
//        'Admin_Festival_Props'
//    ),
//    'Admin_Account_User' => array(
//        'Admin_Account_Vipicon'
//    ),
//    'Admin_Client_Internalcooperate_Desktop' => array(
//        'Admin_Client_Internalcooperate_Channel',
//    )
);

$noVerify = array(
    'Admin_Common'
);

return array($config, $view, $extends, $noVerify);
