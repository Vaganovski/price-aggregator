<?php
/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class Version75 extends Doctrine_Migration_Base
{
    public function up()
    {
//        $conn = Doctrine_Manager::connection();
//        $filename = realpath(APPLICATION_PATH . '/../library/Zend/Queue/Adapter/Db/mysql.sql');
//        $conn->getDbh()->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
//        if ($filename) {
//            $query = file_get_contents($filename);
//            $stmt = $conn->getDbh()->query($query);
////            $stmt->execute();
//        }

        echo  'Импортировать в БД файл: ' . realpath(APPLICATION_PATH . '/../library/Zend/Queue/Adapter/Db/mysql.sql') . "\n";
        
    }

    public function down()
    {
    }
}