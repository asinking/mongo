<?php

namespace asinking\mongo;

use asinking\mongo\MongoDriver;

/**
 * MongoDb操作类
 * Class Mongo
 * @package asinking\mongo
 */
class Mongo extends MongoDriver
{
    /**
     * 连接的db
     * @var null
     */
    public $connect = null;
    /**
     * 要操作的table表集合
     * @var null
     */
    public $table = null;

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

    /**
     * 设置表名称
     * @param string $table
     * @return mixed
     */
    protected function setTable(string $table)
    {
        $this->table = $table;
    }

}