<?php
//编辑小组信息
defined('IN_TS') or die('Access Denied.');

//用户是否登录
$userid = aac('user')->isLogin();

$groupid = intval($_GET['groupid']);

$strGroup = $new['group']->find('group',array(
	'groupid'=>$groupid,
));

$strGroup['groupname'] = tsDecode($strGroup['groupname']);
$strGroup['groupdesc'] = tsDecode($strGroup['groupdesc']);

if($strGroup['userid']==$userid || $TS_USER['isadmin']==1){


	switch($ts){
		
		//编辑小组基本信息
		case "base":
		
			//小组标签
			$arrTags = aac ( 'tag' )->getObjTagByObjid ( 'group', 'groupid', $groupid );
			foreach ( $arrTags as $key => $item ) {
				$arrTag [] = $item ['tagname'];
			}
			$strGroup ['tag'] = arr2str ( $arrTag );
			
			$title = '编辑小组基本信息';
			include template("edit_base");
			
			break;
		
		//编辑小组头像
		case "icon":

			$title = '修改小组头像';
			include template("edit_icon");
			
			break;
		
		//修改访问权限
		case "privacy":

			$title = '编辑小组权限';
			include template("edit_privacy");
			
			break;




		//帖子分类
		case "type":
			//调出类型
			$arrGroupType = $new['group']->findAll('group_topic_type',array(
				'groupid'=>$strGroup['groupid'],
			));
			
			$title = '编辑帖子分类';
			include template("edit_type");
			
			break;


        //添加帖子分类
        case "typeadd":

            $typename = trim($_POST['typename']);
            if($typename){
                $new['group']->create('group_topic_type',array(
                    'groupid'=>$groupid,
                    'typename'=>$typename,
                ));
            }

            header("Location: ".tsUrl('group','edit',array('ts'=>'type','groupid'=>$groupid)));
            break;

        //修改帖子分类
        case "typeedit":
            $typeid = intval($_POST['typeid']);
            $typename = trim($_POST['typename']);
            if($typeid && $typename){
                $new['group']->update('group_topic_type',array(
                    'typeid'=>$typeid,
                    'groupid'=>$groupid,
                ),array(
                    'typename'=>$typename,
                ));
            }
            header("Location: ".tsUrl('group','edit',array('ts'=>'type','groupid'=>$groupid)));
            break;


        //删除帖子分类
        case "typedelete":

            $typeid = intval($_GET['typeid']);

            $new['group']->delete('group_topic_type',array(
                'typeid'=>$typeid,
                'groupid'=>$groupid,
            ));

            $new['group']->update('group_topic',array(
                'groupid'=>$groupid,
                'typeid'=>$typeid,
            ),array(
                'typeid'=>0,
            ));

            header("Location: ".tsUrl('group','edit',array('ts'=>'type','groupid'=>$groupid)));
            break;





			
		//小组分类
		case "cate":
			
			$arrCate = $new['group']->findAll('group_cate',array(
			
				'referid'=>0,
			
			));
			
			//一级分类
			$strCate = $new['group']->find('group_cate',array(
				'cateid'=>$strGroup['cateid'],
			));
			//二级分类
			$strCate2 = $new['group']->find('group_cate',array(
				'cateid'=>$strGroup['cateid2'],
			));
			//三级分类
			$strCate3 = $new['group']->find('group_cate',array(
				'cateid'=>$strGroup['cateid3'],
			));
			
			$title = '编辑小组分类';
			include template("edit_cate");
			
			break;
			
			
		//成员审核
		case "useraudit":
			
			$arrUserId = $new['group']->findAll('group_user_isaudit',array(
				'groupid'=>$groupid,
			));
			foreach($arrUserId as $key=>$item){
				$arrUser[] = aac('user')->getOneUser($item['userid']);
			}
			
			$title = '成员申请加入审核';
			include template('edit_useraudit');
			break;
		
		//成员审核执行
		case "userauditdo":
			
			$userid = intval($_GET['userid']);
			$status = intval($_GET['status']);
			
			
			//0加入1删除
			if($status==0 && $userid){
				
				$new['group']->create('group_user',array(
					'userid'=>$userid,
					'groupid'=>$groupid,
					'addtime'=>time(),
				));
				
				//更新
				$count_group = $new['group']->findCount('group_user',array(
					'userid'=>$userid,
				));
				$new['group']->update('user_info',array(
					'userid'=>$userid,
				),array(
					'count_group'=>$count_group,
				));
				
				//计算小组会员数
				$count_user = $new['group']->findCount('group_user',array(
					'groupid'=>$groupid,
				));
				
				//更新小组成员统计
				$new['group']->update('group',array(
					'groupid'=>$groupid,
				),array(
					'count_user'=>$count_user,
				));
				
			}
			
			$new['group']->delete('group_user_isaudit',array(
				'userid'=>$userid,
				'groupid'=>$groupid,
			));
			
			header('Location: '.tsUrl('group','edit',array('groupid'=>$groupid,'ts'=>'useraudit')));
			
			break;


        //小组转让
        case "transfer":


            $title = '小组转让';
            include template('edit_transfer');
            break;

        case "transferdo":


            $touserid = intval($_POST['touserid']);

            $strTouser = $new['group']->find('group_user',array(
                'userid'=>$touserid,
                'groupid'=>$groupid,
            ));

            if($strTouser==''){
                tsNotice('用户还没有加入本小组，只能转让给本小组成员');
            }

            $new['group']->update('group',array(
                'groupid'=>$groupid,
            ),array(
                'userid'=>$touserid,
            ));

            tsNotice('小组转让成功！');

            break;




			
		
	}


}else{

	tsNotice('非法操作！');

}