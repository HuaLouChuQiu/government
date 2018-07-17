<?php
/**
 * 获取链接自动运行文件，
 * 先加载需要的文件
 */
require_once("../lib/phpQuery/phpQuery.php");
require_once("../lib/crawling/reptile.php");
require_once("../lib/DB/pdosql.php");
$config = require_once("../lib/config.php");
define('DB_HOST', $config['db']['host']);
define('DB_NAME', $config['db']['dbname']);
define('DB_USER', $config['db']['username']);
define('DB_PASS', $config['db']['password']);


require_once("linkPolicy_way.php");
require_once("linkNew_way.php");
require_once("textNews_way.php");

//$test_obj = new link_way;   //国家政策
//$test_obj->updatelink();

//$state_news_obj = new linkNew_way;             //国家新闻链接
//$state_news_obj->savenewsLink();echo "操作完成";

$state_newsText_obj = new textNews_way;
$state_newsText_obj->saveNewstext();