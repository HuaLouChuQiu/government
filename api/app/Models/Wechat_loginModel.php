<?php
namespace app\Models;

use fastFrame\base\Model;
use fastFrmae\db\Db;

/**
 * 把微信账号和个人信息相互绑定插入
 * 
 * @method in_user完成注册（把数据插入数据库）的方法
 */
class Wechat_loginModel extends Model {

    /**
     * 自定义当前模型操作的数据库表名称，
     * 如果不指定，默认为类名称的小写字符串，
     * 这里就是 item 表
     * @var string
     */
    protected $table = 'user_wechat';


    /**
     * 插入功能，把登录过的用户数据存到数据库当中
     * 
     * @param 要插入的数据的数组形式
     * @return int 返回插入的状态和对应的id
     */
    public function in_user($userData){

        //查找这个openid在数据库里面是不是存在
        $s_data = array("openid"=>$userData['openid']);
        $is_insert = $this->select_all($this->table,array("*"),$s_data);

        if(empty($is_insert)){

            $in_data = array("openid"=>$userData['openid'],"session_key"=>$userData['session_key'],"avatar"=>$userData['avatar']);

            //调用继承了的插入方法
            return $this->insert($this->table,$in_data);
        }else{

            //不为空（数据已经在数据库里面）就更改session——key 和头像链接
            $u_arr = array("session_key"=>$userData['session_key'],"avatar"=>$userData['avatar']);
            $u_where = array("id"=>$is_insert[0]['id']);

            $this->update($this->table,$u_arr,$u_where);
            return $is_insert[0]['id'];
        }
    }

    /**
     * 更改功能，更改用户的喜爱偏好
     * 
     * @param 需要更改的偏好的json类型数据
     * @return 返回对应的
     */
    public function up_case($id,$casejson){
        $ucase_user = array("case_word"=>$casejson);
        $uw_user = array("`id`"=>$id);
        $this->update("user_wechat",$ucase_user,$uw_user);
        
    }
}