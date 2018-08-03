<?php
/**
 * 获取链接自动运行文件，
 * 先加载需要的文件
 */
set_time_limit (0);
date_default_timezone_set("PRC");               //设置失去，避免php抛出警告
chdir(dirname(__FILE__));                        //解决crontab 默认/root路径问题
require_once("../lib/phpQuery/phpQuery.php");
require_once("../lib/crawling/reptile.php");
require_once("../lib/DB/pdosql.php");

require_once("../lib/aip-php-sdk-2.2.5/lib/AipBase.php");
require_once("../lib/aip-php-sdk-2.2.5/lib/AipBCEUtil.php");
require_once("../lib/aip-php-sdk-2.2.5/lib/AipHttpClient.php");
require_once("../lib/aip-php-sdk-2.2.5/AipNlp.php");


$config = require_once("../lib/config.php");
define('DB_HOST', $config['db']['host']);
define('DB_NAME', $config['db']['dbname']);
define('DB_USER', $config['db']['username']);
define('DB_PASS', $config['db']['password']);

define('APP_ID', $config['ai']['app_id']);
define('APP_KEY',$config['ai']['app_key']);
define('SECRET_KEY',$config['ai']['secret_key']);


require_once("linkPolicy_way.php");         //国家政策链接获取方法
require_once("linkNew_way.php");             //国家新闻链接获取方法
require_once("textNews_way.php");             //国家新闻文本获取方法
require_once("keywordNews.php");               //获取国家新闻关键字的方法
require_once("rank_source.php");                //根据人民关系事的相似度给新闻评分


$test_obj = new link_way;   //国家政策
$test_obj->updatelink();
//$test_obj->updatetitlejson();
echo "国家政策完成<br>\n";

$state_news_obj = new linkNew_way;             //国家新闻链接
$state_news_obj->savenewsLink();
echo "国家新闻完成<br>\n";

$state_newsText_obj = new textNews_way;        //新闻内容
$state_newsText_obj->saveNewstext();
echo "新闻内容操作完成<br>\n";

$state_newsKeyword_obj = new keywordNews();     //分析出关键字
$state_newsKeyword_obj->getkeyword();
echo "获取关键字完成<br>\n百度人工智能接口调用量：".$state_newsKeyword_obj->ai_num."\n";

$state_ranksource_obj = new rank_source();
$state_ranksource_obj->getrangksource($config['con']);
echo "排名分数配置获取成功<br>\n百度人工智能接口调用量-短文本相似度：".$state_ranksource_obj->ai_num."\n";