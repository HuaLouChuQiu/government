<?php
namespace app\Controllers;

use fastFrame\base\Controller;
use app\Models\Wechat_loginModel;
use method\pubway;
use method\check;

/**
 * 微信用户登录的控制器
 */
class Wechat_loginController extends Controller {
    /**
     * 微信用户登录注册
     * @param $code 微信登录返回的code
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

    /**
     * 更改兴趣爱好接口
     * @param $id 用户标签
     * @param $openid openid
     * @param $caseword 偏好数组
     */
    public function chang_hobby($id="",$openid="",$caseword=""){
        $chghobM_obj = new Wechat_loginModel;

        $c_checkmsg = (new check)->checkParam_prmsg($id,$openid);       //检测参数格式
        $this->check_err($c_checkmsg);

        $is_user = $chghobM_obj->check_user($id,$openid);               //检查用户id和openid是不是对应
        $this->check_err($is_user);

        $caseword = json_decode($caseword);
        if(!is_array($caseword)){
            $rerr_msg = array("errMsg"=>"偏好参数格式不对");
            $this->check_err($rerr_msg);
        }else{
            $casejson = json_encode($caseword,JSON_UNESCAPED_UNICODE);
            $chghobM_obj->up_case($id,$casejson);
            $this->output(array("isok"=>true));
        }
    }

}