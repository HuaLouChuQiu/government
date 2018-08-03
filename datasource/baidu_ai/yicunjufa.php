<?php
require_once("../lib/aip-php-sdk-2.2.5/lib/AipBase.php");
require_once("../lib/aip-php-sdk-2.2.5/lib/AipBCEUtil.php");
require_once("../lib/aip-php-sdk-2.2.5/lib/AipHttpClient.php");
require_once("../lib/aip-php-sdk-2.2.5/AipNlp.php");

$config = require_once("../lib/config.php");
define('APP_ID', $config['ai']['app_id']);
define('APP_KEY',$config['ai']['app_key']);
define('SECRET_KEY',$config['ai']['secret_key']);

$test_text = new AipNlp(APP_ID, APP_KEY, SECRET_KEY);
$data[3][0] = " ";
$data[0]['title'] = "李克强主持召开国务院常务会议 部署持续优化营商环境等";
$data[3][1] = " ";

//$r_msg = $test_text->depParser($title);
$belong = array(1,2,3,4,5); $belong_1 = array(1,2,3);
var_dump(array_splice($belong,0,count($belong_1)));