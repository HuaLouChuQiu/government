<?php
include_once('datasource/lib/crawling/reptile.php');
include_once('datasource/lib/phpQuery/phpQuery.php');

$cur_obj = new curlcity('http://cksp.eol.cn/tutor_search.php?page=12&do=search&dstype=1');
$data = $cur_obj->curlMethod();

$pquery_obg = new phpqueryGet($data);
$strs = $pquery_obg->getDetailedmess("table.tab_01 tr[bgcolor]", "td > nobr > a:first");
$links = $pquery_obg->getTabAttributes('table.tab_01 tr[bgcolor] td > input ~ nobr > a', "href");
var_dump($strs);
var_dump($links);
/* foreach($strs as $str){
    $str = mb_convert_encoding($str,'ISO-8859-1','utf-8');
    $str = str_replace(array("\n","\t"," ","\n\t"),"",$str);
    var_dump($str);
    for($i=0; $i< strlen($str);$i++){
        echo ord($str[$i]);
    }
    $len = strlen($str);
    $str_code = '';
    for($i=0; $i< $len; $i++){
        $str_code .= ord($str[$i])>127? ord($str[$i]).ord($str[++$i]).ord($str[++$i]) : '';
    }

    var_dump(chr((int)$str_code));
    echo '<br>';
   
    //$str_code = utf8_substr($str,30);
    //var_dump($str_code);
} */