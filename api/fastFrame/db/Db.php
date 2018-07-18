<?php
namespace fastFrame\db;

use PDO;
use PDOException;

/**
 * 数据库操作类。
 * 其$pdo属性为静态属性，所以在页面执行周期内，
 * 只要一次赋值，以后的获取还是首次赋值的内容。
 * 这里就是PDO对象，这样可以确保运行期间只有一个
 * 数据库连接对象，这是一种简单的单例模式
 * Class Db
 */
class Db
{
    public $dbms = "mysql";
    public $host = DB_HOST;       //数据库主机名
    //private $dbName = "flight_line";
    public $user = DB_USER;
    public $pwd = DB_PASS;
    public $pdoObject;

    public function __set($dbName=DB_NAME,$charset="utf8"){
        try{
            $setcharset = array(PDO::MYSQL_ATTR_INIT_COMMAND=>"set names ".$charset);
            
            $dsn = $this->dbms.":host=".$this->host.";dbname=".$dbName;       //构造pdo dsn
            $this->pdoObject = new PDO($dsn,$this->user,$this->pwd,$setcharset);
            $this->pdoObject->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);       //设置错误处理模式为抛出异常
            return $this->pdoObject;
        }catch(PDOException $e){
            exit($e->getMessage());;      //输出错误信息
        }
    }
}