<?php
/**
 *@copyright Jec
 *@package Jec框架
 *@link jecelyin@gmail.com
 *@author jecelyin peng
 *@license 转载或修改请保留版权信息
 * Mysql数据库连接驱动
 */
class DB_Mysql extends mysqli
{
    private $count = 0;

    public function __construct($cfg)
    {
        parent::__construct($cfg['host'], $cfg['user'], $cfg['pwd'], $cfg['db_name'], (int)$cfg['port'], $cfg['socket']);
        if ( $this->connect_error )
            $this->_halt('Connect Error ('.$this->connect_errno.') '.$this->connect_error);

        //修正charset比较容易写错utf8为utf-8的问题
        //$cfg['charset'] = preg_replace('/utf\-(\d+)/i', 'utf\1', $cfg['charset']);
        if(!$this->set_charset($cfg['charset']))
            $this->_halt("Error loading character set utf8: $this->error");

        unset($cfg);
    }

    /**
     * 执行一个查询
     * @param string $sql
     * @return mysqli_result|int 返回删除与更新语句将返回影响行数
     *  或插入与替换语句将返回最后插入id
     *  或For successful SELECT, SHOW, DESCRIBE or EXPLAIN queries mysqli_query() will return a mysqli_result object.
     *  For other successful queries mysqli_query() will return TRUE.
     */
    public function query($sql)
    {
        $sql = trim($sql);
        $result = parent::query($sql);

        if(!$result)
            $this->_halt('QUERY STRING: ' . str_replace(array("\n", "\r"), '', $sql));
        //统计查询次数
        $this->count ++;

        //删除或更新时返回影响行数
        if(stripos($sql, 'delete')===0 || stripos($sql, 'update')===0 || stripos($sql, 'replace')===0)
            return $this->affected_rows;

            //插入或替换时返回最后影响的ID
        if(stripos($sql, 'insert')===0)
            return $this->insert_id;

        return $result;
    }

    /**
     * 获取一个查询结果数组
     * @param mysqli_result  $query_result resource For SELECT, SHOW, DESCRIBE, EXPLAIN and other statements returning resultset, mysqli_query
     * @param int $result_type 类型: MYSQLI_ASSOC, MYSQLI_NUM, MYSQLI_BOTH
     * @return array
     */
    public function fetchArray($query_result, $result_type = MYSQLI_ASSOC)
    {
        $result = $query_result->fetch_array($result_type);
        return $result;
    }

    /**
     * 获得查询结果的第一行数组
     * @param string $sql resource For SELECT, SHOW, DESCRIBE, EXPLAIN and other statements returning resultset, mysqli_query
     * @param int $result_type 类型: MYSQLI_ASSOC, MYSQLI_NUM, MYSQLI_BOTH
     * @return array
     */
    public function getRow($sql, $result_type = MYSQLI_ASSOC)
    {
        $query = $this->query($sql);
        $result = $query->fetch_array($result_type);
        $query->free();
        unset($query);
        return $result;
    }
    
    /**
     * 获取查询结果中的第一条第几列
     * @param string $sql 查询语句
     * @param int $offset 第几列
     * @return bool|string
     */
    public function getOne($sql, $offset = 0)
    {
        $query = $this->query($sql);
        $result = $query->fetch_row();
        $query->free();
        unset($query);
        return $result === false || !isset($result[$offset]) ? false : $result[$offset];
    }

    /**
     * 返回所有查询结果集
     * @param string $sql 查询语句
     * @param int $result_type 类型: MYSQLI_ASSOC, MYSQLI_NUM, MYSQLI_BOTH
     * @return array 二维数组
     */
    public function getAll($sql, $result_type = MYSQLI_ASSOC)
    {
        $query = $this->query($sql);
        if (method_exists($query, 'fetch_all')) # Compatibility layer with PHP < 5.3
            $res = $query->fetch_all($result_type);
        else
            for ($res = array(); $tmp = $query->fetch_array($result_type);) $res[] = $tmp;

        $query->free();
        unset($query);
        return $res;
    }

    /**
     * 返回最后插入ID
     * @return int
     */
    public function getInsertId()
    {
        return $this->insert_id;
    }

    /**
     * 根据数组组织成一条查询语句
     * @param string $action 操作动作名称：insert,replace,update
     * @param string $table 表名
     * @param array $data 数据内容 array(字段名=>值)
     * @param array $where 条件
     * @return string
     */
    public function getSql($action, $table, $data, $where = array())
    {
        switch (strtolower($action))
        {
            case 'insert':
            case 'replace':
                if(isset($data[0]) && is_array($data[0]))
                {
                    $fields = array_keys($data[0]);
                    $values = array();
                    foreach($data as $row)
                        $values[] = "('" . implode("','", $row) . "')";
                    $values = implode(', ', $values);
                }else{
                    $fields = array_keys($data);
                    $values = "('" . implode("','", $data) . "')";
                }

                return strtoupper($action) . " INTO `$table` (`" . implode('`,`', $fields) . "`) VALUES {$values}";
            case 'update':
            case 'delete':
                $sp = $set = $w = '';
                if($data)
                {
                    foreach ($data as $k => $v)
                    {
                        $set .= $sp . "`$k` = '{$v}'";
                        $sp = ', ';
                    }
                }

                if ($where)
                {
                    $sp = '';
                    if (is_array($where))
                    {
                        foreach ($where as $k => $v)
                        {
                            $w .= $sp . (is_array($v) ? "`$k` IN('".implode("','", $v)."')" : "`$k` = '$v'");
                            $sp = ' AND ';
                        }
                    }else{
                        $w = $where;
                    }
                }
                if($action == 'update')
                {
                    return strtoupper($action) . " `{$table}` SET $set WHERE $w";
                }else{
                    return strtoupper($action) . " FROM `{$table}` WHERE $w";
                }
        }
        return false;
    }

    /**
     * 插入一条数据
     * @param string $table 表名
     * @param array $data 数据内容 array(字段名=>值)
     * @return int 最后插入ID
     */
    public function insert($table, $data)
    {
        return $this->query($this->getSql('insert', $table, $data));
    }

    /**
     * 替换一条数据
     * @param string $table 表名
     * @param array 数据内容 array(字段名=>值)
     * @return int 最后插入ID
     */
    public function replace($table, $data)
    {
        return $this->query($this->getSql('replace', $table, $data));
    }

    /**
     * 更新数据
     * @param string $table 要更新的表名
     * @param string $data 数据内容 array(字段名=>值)
     * @param array $where 更新对象的数组或字符串
     * @return int 影响行数
     */
    public function update($table, $data, $where)
    {
        return $this->query($this->getSql('update', $table, $data, $where));
    }

    /**
     * 删除数据
     * @param string $table 表名
     * @param array $where Where条件，可以是数组或字符串
     * @return int 影响行数
     */
    public function delete($table, $where)
    {
        return $this->query($this->getSql('delete', $table, array(), $where));
    }

    /**
     * 返回上条语句执行影响行数
     * @return int
     */
    public function getAffectedRows()
    {
        return $this->affected_rows;
    }

    /**
     * 返回查询行数
     * @param string $sql 查询语句
     * @return int
     */
    public function getRowsNum($sql)
    {
        return $this->query($sql)->num_rows;
    }

    /**
     * 判断表名是否存在
     * @param string $table 表名
     * @return int 1 or 0
     */
    public function ifExists($table)
    {
        $rs = $this->query("show tables like '{$table}'");
        return($rs->num_rows);
    }

    private function _halt($msg)
    {
        $error = $this->error;
        $errno = $this->errno;
        throw new JecException("MySQL Error($msg):\n [{$errno}] {$error} \n", 999);
    }

    public function __destructor()
    {
        $this->close();
    }
}
