<?php 
defined('IN_TS') or die('Access Denied.'); 

function signuser(){
	$arrUser = aac('user')->getHotUser(20);
	echo '<div class="panel panel-default">';
	echo '<div class="panel-heading">最新签到用户</div>';
	echo '<div class="panel-body"><div class="facelist"><ul>';
	foreach($arrUser as $key=>$item){
		echo '<li><a href="'.tsUrl('user','space',array('id'=>$item['userid'])).'"><img class="img-circle" src="'.$item['face'].'" alt="'.$item['username'].'" width="48" height="48" /></a><div><a href="'.tsUrl('user','space',array('id'=>$item['userid'])).'">'.cututf8(t($item['username']),0,5,false).'</a></div></li>';
	}
	echo '</ul></div></div></div>';
}

addAction('home_index_left','signuser');