<?php
namespace method;

/**
 * 通用的检查方法，
 */
class check {
    /**
     * 检查参数
     * @param $param 参数代码
     * @param $type 参数类型
     * @param $maxlen 参数最大长度
     * @return 符合和求就为空数组
     */
    public function check_param($param,$type,$maxlen=0){
        $r_msg = array();
        switch($type){
            case "string":
                if(empty($param)) $r_msg[] = "参数为空";
                if(!is_string($param)) $r_msg[] = "不是字符串类型";
                if($maxlen=0 || !is_numeric($maxlen)){
                    break;
                }else{
                    if(mb_strlen($param)>$maxlen) $r_msg[] = "参数过长";
                }
                break;

            case "number":
                if(empty($param) && $param!=0) $r_msg[] = "参数为空";
                if(!is_numeric($param)) $r_msg[] = "不是数字类型";
                if($maxlen==0 || !is_numeric($maxlen)){
                    break;
                }else{
                    if($param > $maxlen) $r_msg[] = "参数过大";
                }
                break;

            case "array":
                if(empty($param)) $r_msg[] = "参数为空";
                if(!is_array($param)) $r_msg[] = "不是数组类型";
                break;

            default:
                if(empty($param)) $r_msg[] = "参数为空";
        }
        return $r_msg;
    }
}