<?php

namespace asinking\mongo;

use asinking\mongo\MongoDriver;

class Mongo extends MongoDriver
{
    public $connect = null;
    public $table = null;
    public $indexKey = null;
    public $isUniqueKey = false;

    /**
     * 查询分页数据
     * @param int $page
     * @param int $pageNum
     * @param array $where
     * @param array $projection
     * @param array $sort
     * @return mixed
     */
    protected function queryArrayPageList($page = 1, $pageNum = 10, $where = array(), $projection = array(), $sort = array())
    {
        $page += 0;
        $page = ($page < 1) ? 1 : $page;
        $start = ($page - 1) * $pageNum;
        return $this->mongo->query($this->table, $where, $projection, $sort, $start, $pageNum);
    }

    /**
     * 查询全部数据
     * @param array $where
     * @param array $projection
     * @param array $sort
     * @return array
     */
    protected function queryArray($where = array(), $projection = array(), $sort = array())
    {
        return $this->mongo->query($this->table, $where, $projection, $sort);
    }

    /**
     * 修改指日期数据
     * @param array $updatParams
     * @param $orderDate
     * @param bool $upsert
     * @return int
     */
    public function updateDateData($updatParams = array(), $orderDate, $upsert = true)
    {
        return $this->mongo->update($this->table, $filter = array('createDate' => $orderDate), $updatParams, $unsetParams = array(), $incParams = array(), $multi = true, $upsert);
    }

    /**
     * 修改数据
     * @param array $updatParams
     * @param array $filter
     * @param bool $upsert
     * @return int
     */
    public function updateData($updatParams = array(), $filter = array(), $upsert = true)
    {
        return $this->mongo->update($this->table, $filter, $updatParams, $unsetParams = array(), $incParams = array(), $multi = true, $upsert);
    }

    /**
     * 删除数据
     * @param array $filter
     * @param bool $multi
     * @return int
     */
    protected function deleteData($filter = array(), $multi = true)
    {
        return $this->mongo->delete($this->table, $filter, $multi);
    }

    /**
     * 插入数据
     * @param $datas
     * @param bool $more 是否插入多行
     * @return mixed
     */
    protected function insert($datas, $more = false)
    {
        if ($more)
            return $this->mongo->insertArray($this->table, $datas);
        else
            return $this->mongo->insertOne($this->table, $datas);
    }

    /**
     * 数据库约束索引
     * @param $colName
     * @return int
     */
    private function saddQueneEnsureTable($colName)
    {
        return Dao::predis()->sadd(Keys::queneEnsureIndex(), array($colName));
    }

    /**
     * 判断当前表是否已经执行约束索引
     * @param $colName
     * @return int
     */
    private function existQueneEnsureTable($colName)
    {
        return Dao::predis()->sismember(Keys::queneEnsureIndex(), $colName);
    }

    /**
     * 设置数据库索引，只能操作一次
     * @param $colName
     * @param $indexKeys
     * @param $mulit
     * @param bool $unique 是否增加唯一索引，默认普通索引
     */
    public function ensureIndex($colName, $indexKeys, $unique = false, $mulit = false)
    {
        if (!$this->existQueneEnsureTable($colName)) {
            $this->mongo->ensureIndex($colName, Str::getUuid(), $indexKeys, $unique);
            if (!$mulit)
                $this->saddQueneEnsureTable($colName);
        }
    }

    /**
     * 获取连接的数据库
     * @return string
     */
    protected function getConnectDb(): string
    {
        return $this->connect;
    }

    /**
     * 获取表名称
     * @return string
     */
    protected function getTable(): string
    {
        return $this->table;
    }

    /**
     * 获取DB配置
     * @return array
     */
    protected function getDbConfig(): array
    {
        // TODO: Implement getDbConfig() method.
    }

    protected function getIndexKeys(): string
    {
        return $this->indexKey;
    }

    protected function isUniqueKey(): bool
    {
        return $this->isUniqueKey;
    }
}