<?php


// 数据库配置
$config['db']['host'] = '120.79.211.61';            //服务器120.79.211.61
$config['db']['username'] = 'root';
$config['db']['password'] = '890ccf33a9a4';         //服务器 890ccf33a9a4
$config['db']['dbname'] = 'government';

//百度云ai接口调用参数
$config['ai']['app_id'] = 11530307;
$config['ai']['app_key'] = "7rBfOj6vFhb7OZ8ZFwtm82iU";
$config['ai']['secret_key'] = "OzqkeFdiLUp9Nzsey31wbGDxvDLxDQoK";


//生成简报的优化排名的配置参数
$config['con']['title'][] = "国务院常务会";
$config['con']['title'][] = "李克强：";
$config['con']['keyword'][0] = "经济";
$config['con']['keyword'][1] = "医疗";
$config['con']['keyword'][2] = "养老";
$config['con']['keyword'][3] = "教育";
$config['con']['keyword'][4] = "住房";
$config['con']['keyword'][5] = "环境";
$config['con']['keyword'][6] = "办事难";
$config['con']['keyword'][7] = "扶贫";
$config['con']['keyword'][8] = "三农";
$config['con']['keyword'][9] = "文化活动";


return $config;