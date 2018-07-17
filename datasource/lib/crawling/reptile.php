<?php

/**
 * curl通用类
 *用curl爬取网页信息
 */
class curlcity {
    private $urls;

    public function __construct($url){  //构造方法
        $this->urls = $url;
    }
    //curl获取网页信息
    //参数：是否代理ip
    public function curlMethod($isDaiIp=false){
        if(!is_array($this->urls)){
            $ch = curl_init($this->urls);
            //创建句柄
            
            //curl_setopt($ch,CURLOPT_URL,$this->urls);
                        //curl_setopt($ch, CURLOPT_BINARYTRANSFER, true); //是否返回原生的raw
            if(!empty($isDaiIp)){
                if(is_array($isDaiIp)){         //检测ip池的时候用的方法
                    $ipRand = $isDaiIp;
                }else{
                    $ipRand = getHttpIp();//获取随机ip
                }
                
                curl_setopt($ch, CURLOPT_HTTPHEADER, $ipRand[0]);//设置httpip信息
                curl_setopt($ch,CURLOPT_PROXY,$ipRand[1]);//http代理
                //curl_setopt($ch,CURLOPT_PROXYPORT,$ipArray[2]);//端口
                curl_setopt($ch,CURLOPT_HTTPPROXYTUNNEL,1);//启用http代理
                curl_setopt($ch,CURLOPT_PROXYTYPE,CURLPROXY_HTTP);
                curl_setopt($ch,CURLOPT_REFERER,"http://www.baidu.com/");//伪造来路地址
                curl_setopt($ch,CURLOPT_PROXYAUTH,CURLAUTH_BASIC);//代理认证模式
            }
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);//会将服务器返回的localhost放在head中
            curl_setopt($ch,CURLOPT_USERAGENT,"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36
            (KHTML, like Gecko) Chrome/56.0.2924.87 Safari/537.36"); //请求中包含一个"User-Agent: "头的字符串
            curl_setopt($ch,CURLOPT_HEADER,0);//是否需要头部信息
            curl_setopt($ch,CURLOPT_TIMEOUT,30);//最大秒数
            curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);//curl_exec()获取的信息是否以字符串返回
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);//FALSE 禁止 cURL 验证对等证书
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);//FALSE 不检查证书
            $datas = curl_exec($ch);
            curl_close($ch);
            sleep(2);
            return $datas;
        }else{  //如果传过来的是一个人URL数组
            $dataArray = [];
            $mh = curl_multi_init();
           
            foreach($this->urls as $i=>$url){
                
                $conn[$i] = curl_init();
                curl_setopt($conn[$i],CURLOPT_URL,$url);
                        //curl_setopt($conn[$i], CURLOPT_BINARYTRANSFER, true); //是否返回原生的raw
                if(!empty($isDaiIp)){
                    $ipRand = getHttpIp();//获取随机ip
                    
                    curl_setopt($conn[$i], CURLOPT_HTTPHEADER,$ipRand[0]);//设置httpip信息
                    curl_setopt($conn[$i],CURLOPT_PROXY,$ipRand[1]);//http代理
                    //curl_setopt($conn[$i],CURLOPT_PROXYPORT,$ipArray[2]);//端口
                    curl_setopt($conn[$i],CURLOPT_HTTPPROXYTUNNEL,1);//启用http代理
                    curl_setopt($conn[$i],CURLOPT_PROXYTYPE,CURLPROXY_HTTP);
                    curl_setopt($conn[$i],CURLOPT_REFERER,"http://www.baidu.com/");//伪造来路地址
                    curl_setopt($conn[$i],CURLOPT_PROXYAUTH,CURLAUTH_BASIC);//代理认证模式
                }
                curl_setopt($conn[$i], CURLOPT_FOLLOWLOCATION, 1);//会将服务器返回的localhost放在head中
                curl_setopt($conn[$i], CURLOPT_SSL_VERIFYPEER, false);//禁止curl对服务器进行验证
                curl_setopt($conn[$i], CURLOPT_SSL_VERIFYHOST, false);//检查服务器SSL证书中是否存在一个公用名(common name)

                curl_setopt($conn[$i],CURLOPT_USERAGENT,"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36
                (KHTML, like Gecko) Chrome/56.0.2924.87 Safari/537.36"); //请求中包含一个"User-Agent: "头的字符串
                curl_setopt($conn[$i],CURLOPT_HEADER,0);//是否需要头部信息
                curl_setopt($conn[$i],CURLOPT_TIMEOUT,30);//最大秒数
                curl_setopt($conn[$i],CURLOPT_RETURNTRANSFER,1);//curl_exec()获取的信息是否以字符串返回
                
                curl_multi_add_handle($mh, $conn[$i]); 
            }
            
            $active = null;
                // 执行批处理句柄
                do {
                    sleep(2);
                    $mrc = curl_multi_exec($mh, $active);
                } while ($active);


            //得到各个页面信息
            foreach($this->urls as $i=>$url){
            $datas = curl_multi_getcontent($conn[$i]);
           
            array_push($dataArray,$datas);
            }
            //关闭句柄组中的单个句柄
            foreach($this->urls as $i=>$url){
                curl_multi_remove_handle($mh, $conn[$i]); 
                curl_close($conn[$i]);
            }

            curl_multi_close($mh);//关闭句柄组
           
            return $dataArray;
        }

    }

}


/**
 * 从爬取到的数据用phpquery取出想要具体的数据
 * 
 */
class phpqueryGet {
    
    private $htmldata;
    /**
     * 构造涵数
     */
    public function __construct($htmldata){  //构造方法
        $this->htmldata = $htmldata;
        $document = phpQuery::newDocumentHTML($htmldata);
        //用PHPquery对对象进行解析
    }
    //第一个参数是匹配的文件
    //第一个参数你想要的框架的选择器
    //第二的参数这个框架里具体的信息选择器
    //获取文本信息
    function getDetailedmess($oneSelect,$twoSelect=""){
        $datas = [];
        
        

        if(!empty($twoSelect)){
            $doc = phpQuery::pq("");
            $text_box = $doc->find($oneSelect);
            
            foreach($text_box as $text){
                $data = pq($text)->find($twoSelect)->text();
                array_push($datas,$data);
            }
        }else{
            $doc = phpQuery::pq("");
            $text_box = $doc->find($oneSelect);
            
            foreach($text_box as $text){
                $data = pq($text)->text();
                
                array_push($datas,$data);
            }
        }
        return $datas;
    }
    //第一个参数是匹配的shuju
    //第一个参数你想要的标签的选择器
    //第二的参数标签属性名称
    //用来采集标签属性
    function getTabAttributes($oneSelect,$twoSelect){
        $datas = [];
        //$document = phpQuery::newDocumentHTML($htmldata);
        //用PHPquery对对象进行解析
        $doc = phpQuery::pq("");
        $text_box = $doc->find($oneSelect);
        foreach($text_box as $text){
            $data = $text->getAttribute($twoSelect);
            array_push($datas,$data);
        }
        return $datas;
    }

    /**
     * 匹配html内容及代码
     */
    public function getDetailedhtml($oneSelect){
        $datas = [];
        $doc = phpQuery::pq("");

        $text_box = $doc->find($oneSelect);
            
        foreach($text_box as $text){
            $data = pq($text)->html();
                
            array_push($datas,$data);
        }

        return $datas;
    }

}