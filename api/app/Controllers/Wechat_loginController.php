<?php
namespace app\Controllers;

use fastFrame\base\Controller;
use app\Models\Wechat_loginModel;
use method\pubway;
/**
 * 微信用户登录的控制器
 */
class Wechat_loginController extends Controller {
    /**
     * 微信用户登录注册
     */
    public function login($code=""){
        $r_data = array();

        if(!empty($code)){
            
            if(empty($_POST['avatar'])){
                $r_data['id'] = null;
                $r_data['errMsg'] = '请输入头像链接参数';
                $r_data['isok'] = false;

                $this->output($r_data);
                die;
            }

            $openidMessage = (new pubway)->getopenid($code);

            //判断是否获取了正确格式的openID数据
            if(array_key_exists('openid',$openidMessage)){
                //把头像写入数组
                $openidMessage['avatar'] = $_POST['avatar'];

                //格式正确就插入到数据库中
                $openid_id = (new Wechat_loginModel)->in_user($openidMessage);

                //判断是不是正确插入到数据库中
                if(is_numeric($openid_id)){

                    $r_data['id'] = $openid_id;
                    $r_data['openid'] = $openidMessage['openid'];
                    $r_data['isok'] = true;

                }else{
                    $r_data['id'] = null;
                    $r_data['errMsg'] = '数据插入错误';
                    $r_data['isok'] = false;
                }


            }else{
                //获取失败就返回失败及其原因
                $r_data['id'] = null;
                $r_data['errMsg'] = $openidMessage['errmsg'];
                $r_data['isok'] = false;
            }
        }else{
            //提示要输入参数
            $r_data['id'] = null;
            $r_data['errMsg'] = '请输入参数';
            $r_data['isok'] = false;
        }

        $this->output($r_data);
    }
}