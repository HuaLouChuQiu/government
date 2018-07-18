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
}