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
     * 执行table表索引，设置参数可自动创建
     * @var null
     */
    public $indexKey = null;
    /**
     * 是否设置为唯一索引，true=唯一索引false=普通索引
     * @var bool
     */
    public $isUniqueKey = false;

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
        return $this->indexKey ?? null;
    }

    protected function isUniqueKey(): bool
    {
        return $this->isUniqueKey;
    }
}