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
$text = "扶贫";

$title = "时政";

$r_msg = $test_text->simnet($title,$text);
var_dump($r_msg);