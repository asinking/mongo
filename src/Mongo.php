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
    public $connect = null;
    public $table = null;
    public $indexKey = null;
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
        return $this->indexKey;
    }

    protected function isUniqueKey(): bool
    {
        return $this->isUniqueKey;
    }
}