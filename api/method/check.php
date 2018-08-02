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
                if(empty($param)) $r_msg['errMsg'][] = "参数为空";
                if(!is_string($param)) $r_msg['errMsg'][] = "不是字符串类型";
                if($maxlen==0 || !is_numeric($maxlen)){
                    break;
                }else{
                    if(mb_strlen($param)>$maxlen) $r_msg['errMsg'][] = "参数过长";
                }
                break;

            case "number":
                if(empty($param) && $param!=0) $r_msg['errMsg'][] = "参数为空";
                if(!is_numeric($param)) $r_msg['errMsg'][] = "不是数字类型";
                if($maxlen==0 || !is_numeric($maxlen)){
                    break;
                }else{
                    if($param > $maxlen) $r_msg['errMsg'][] = "参数过大";
                }
                break;

            case "array":
                if(empty($param)) $r_msg['errMsg'][] = "参数为空";
                if(!is_array($param)) $r_msg['errMsg'][] = "不是数组类型";
                break;

            case "json":
                json_decode($param);
                if(json_last_error() != JSON_ERROR_NONE) $r_msg['errMsg'][] = "json数据格式不对";
                break;

            default:
                if(empty($param)) $r_msg['errMsg'][] = "参数为空";
        }
        return $r_msg;
    }

    /**
     * 检查获取个人信息方法参数情况
     * @param $id user表id
     * @param $openid
     * @return 不规范返回errmsg
     */
    public function checkParam_prmsg($id,$openid){
        $r_checkmsg = array();

        //检查id
        if(is_numeric($id)){

        }else{
            $r_checkmsg['errMsg'][0] = 'id格式不正确';
        }

        //检测openid参数
        if(empty($openid)){
            $r_checkmsg['errMsg'][6] = 'openid参数不能为空';
        }

        return $r_checkmsg;

    }
}