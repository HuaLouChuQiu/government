<?php
namespace app\Models;

use fastFrame\base\Model;
use fastFrmae\db\Db;

/**
 * 处理主页国家新闻的视图 state_news
 */
class Index_newsModel extends Model {

    /**
     * 自定义当前模型操作的数据库表名称，
     * 如果不指定，默认为类名称的小写字符串，
     * 这里就是 item 表
     * @var string
     */
    protected $table = 'state_news';

    /**
     * 分页查找视图
     * @param $id_max   id最小的范围
     * @param $id_min   id最大的范围
     * @return $newsdata 返回的新闻信息
     */
    public function sl_state_news($id_max,$id_min){
        if($id_max==0){
            $sql = "SELECT * FROM state_news where state_news.status=1 order by id desc limit $id_min";
        }else{
            $sql = "SELECT * FROM state_news where state_news.status=1 and id between $id_max and $id_min order by id desc";
        }
        $newsdata = $this->select_sql($sql);
        return $newsdata;
    }


    /**
     * 查出文章内容和关键字
     * @param $id 对应文章的id
     */
    public function sl_news_keyword($id){
        $text_content = $this->select_all("state_news",array("*"),array("id"=>$id,"status"=>1));
        $keyword = $this->select_all("keyword_news",array("*"),array("id"=>$id,"status"=>1));
        $r_msg = array_merge($text_content,$keyword);
        return $r_msg;
    }

    /**
     * 找出用户喜爱的偏好
     * @param $id 用户的主键
     * @return 用户喜爱组成json类型的数组
     */
    public function sl_user_caseword($id){
        $user_case = $this->select_all("user_wechat",array("case_word"),array("id"=>$id,"status"=>1));
        if(!empty($user_case)){
            $r_msg = $user_case[0]['case_word'];
        }else{
            $r_msg = array();
        }
        return $r_msg;
    }

    /**
     * 根据用户的喜爱偏好给用户推荐文章摘要
     * 
     * @param $caseWord 用户偏好数组
     * @param $israndom 是否随机生成首页
     * @param $p_id 文章主键
     * @param $num 数量
     * @return 用户喜欢的的标签表的id组成的数组
     */
    public function sl_user_casepolicy($caseWord,$israndom,$p_id,$num){
        $slecwall = "";

        if(empty($caseWord)){     //避免调用array_unique参数出错
            $caseWord = array();
        }else{
            $caseWord = json_decode($caseWord,true);     //json数据转转换成数组
        }

        $is_allsmae = $caseWord;
        $is_allsmae = array_unique($is_allsmae);          //数组把相同的东西去掉,如果最后长度为一就证明数组全相同
        //如果数组全为fale 就重新赋值全为true
        if(count($is_allsmae)==1 || count($is_allsmae)==0){
            $caseWord = array(true,true,true,true,true,true,true,true,true);
        }
    
        foreach($caseWord as $key=>$case_v){        
            switch($key){
                case 0:
                $slecw = "s_econoimc > 0";
                break;

                case 1:
                $slecw = "s_medical > 0";
                break;

                case 2:
                $slecw = "s_pension > 0";
                break;

                case 3:
                $slecw = "s_education > 0";
                break;

                case 4:
                $slecw = "s_housing > 0";
                break;
                
                case 5:
                $slecw = "s_environment > 0";
                break;
                
                case 6:
                $slecw = "s_hardword > 0";
                break;
                
                case 7:
                $slecw = "s_poverty > 0";
                break;
                
                case 8:
                $slecw = "s_sannong > 0";
                break;

                default:
                break;
            }
            if($case_v){
                $slecwall = $slecwall." or ".$slecw;
            }
        }

        if($p_id==0){
            if($israndom==1){
                $sql_sourid = "SELECT rank_score.p_id FROM rank_score,policy_link where DATE_SUB(CURDATE(), INTERVAL 3 DAY) <= date(rank_score.createtime) and rank_score.p_id=policy_link.id and policy_link.status=1";
                $p_idarry = $this->select_sql($sql_sourid);
                $p_idarrykey = array_rand($p_idarry,1);                     //随机获取数组键值
                $p_id = $p_idarry[$p_idarrykey]['p_id'];

                $pid_max = $p_id - 1;
                $pid_min = $p_id - $num;
                $sql_pid = "SELECT id from (SELECT * FROM policy_score where id between $pid_min and $pid_max and policy_score.status=1) as tmp where s_title > 0 or s_culture > 0 $slecwall order by id desc";
            }else{
                $sql_pid = "SELECT id FROM (SELECT * FROM policy_score where policy_score.status=1) as tmp where s_title > 0 or s_culture > 0 $slecwall order by id desc limit $num";        //获取最新的东西
            }
        }else{
            if($num<0){
                $pid_max = $p_id -$num;
                $pid_min = $p_id + 1;
            }else{
                $pid_max = $p_id - 1;
                $pid_min = $p_id - $num;
            }
            //通过pid获取指定数量相连的数据
            $sql_pid = "SELECT id from (SELECT * FROM policy_score where id between $pid_min and $pid_max and policy_score.status=1) as tmp where s_title > 0 or s_culture > 0 $slecwall order by id desc";       
        }
        $r_msg = $this->select_sql($sql_pid);
        return $r_msg;
    }

    /**
     * 通过获取的文章id得到内容
     * @param $caseWord 用户偏好数组
     * @param $israndom 是否随机生成首页
     * @param $p_id 文章主键
     * @param $num 数量
     * @return 用户喜欢的文章内容
     */
    public function sl_user_casenews($caseWord,$israndom,$p_id,$num){
        $pidAry = $this->sl_user_casepolicy($caseWord,$israndom,$p_id,$num);

        if(empty($pidAry)){
            $sql = "SELECT * from state_news where state_news.status=1 and id=0";
        }else{
            $p_idAry = array();
            foreach($pidAry as $pid){
                $p_idAry[] = "id=".$pid['id'];
            }
            $sw_state = implode(" or ",$p_idAry);

            $sql = "SELECT * FROM (SELECT * from state_news where state_news.status=1) as tmp where $sw_state";
        }
        $newsdata = $this->select_sql($sql);
        return $newsdata;
    }

    /**
     * 找出文章的内容和对应的分数
     */
    public function sl_news_score($id){
        $text_content = $this->select_all("state_news",array("*"),array("id"=>$id,"status"=>1));
        $score = $this->select_all("policy_score",array("*"),array("id"=>$id,"status"=>1));
        $titlejson = $this->select_all("policy_link",array("titlejson"),array("id"=>$id,"status"=>1));
        $text_content[0]["titlejson"] = $titlejson[0]['titlejson'];
        $r_msg = array_merge($text_content,$score);
        return $r_msg;
    }
}