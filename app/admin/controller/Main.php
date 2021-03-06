<?php
// +----------------------------------------------------------------------
// | Copyright (c) 2017 https://nikm.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 傍晚升起的太阳 < wuruiwm@qq.com >
// +----------------------------------------------------------------------


namespace app\admin\controller;

use \think\Db;
use \think\Cookie;
use app\admin\controller\Permissions;
class Main extends Permissions
{
    public function index()
    {
        //tplay版本号
        $info['tplay'] = TPLAY_VERSION;
        //tp版本号
        $info['tp'] = THINK_VERSION;
        //php版本
        $info['php'] = PHP_VERSION;
        //操作系统
        $info['win'] = PHP_OS;
        //最大上传限制
        $info['upload_size'] = ini_get('upload_max_filesize');
        //脚本执行时间限制
        $info['execution_time'] = ini_get('max_execution_time').'S';
        //环境
        $sapi = php_sapi_name();
        if($sapi = 'apache2handler') {
        	$info['environment'] = 'apache';
        } elseif($sapi = 'cgi-fcgi') {
        	$info['environment'] = 'cgi';
        } else {
        	$info['environment'] = 'cli';
        }
        //剩余空间大小
        //$info['disk'] = round(disk_free_space("/")/1024/1024,1).'M';
        $this->assign('info',$info);


        /**
         *网站信息
         */
        $web['user_num'] = Db::name('admin')->count();
        $web['admin_cate'] = Db::name('admin_cate')->count();
        $web['ip_ban'] = 0;
        $web['article_num'] = '0';
        $web['status_article'] = '0';
        $web['top_article'] = '0';
        $web['file_num'] = Db::name('attachment')->count();
        $web['status_file'] = Db::name('attachment')->where('status',0)->count();
        $web['ref_file'] = Db::name('attachment')->where('status',-1)->count();
        $web['message_num'] = '0';
        $web['look_message'] = '0';


        //登陆次数和下载次数
        $today = date('Y-m-d');

        //取当前时间的前十四天
        $date = [];
        $date_string = '';
        for ($i=9; $i >0 ; $i--) { 
            $date[] = date("Y-m-d",strtotime("-{$i} day"));
            $date_string.= date("Y-m-d",strtotime("-{$i} day")) . ',';
        }
        $date[] = $today;
        $date_string.= $today;
        $web['date_string'] = $date_string;

        $login_sum = '';
        foreach ($date as $k => $val) {
            $min_time = strtotime($val);
            $max_time = $min_time + 60*60*24;
            $where['create_time'] = [['>=',$min_time],['<=',$max_time]];
            $login_sum.= Db::name('admin_log')->where(['admin_menu_id'=>50])->where($where)->count() . ',';
        }
        $web['login_sum'] = $login_sum;

        $this->assign('web',$web);

        return $this->fetch();
    }
}
