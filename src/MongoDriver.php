<?php

namespace asinking\mongo;

use MongoDB\Driver\BulkWrite;
use MongoDB\Driver\Command;
use MongoDB\Driver\Manager;
use MongoDB\Driver\Query;
use MongoException;

abstract class MongoDriver
{
    /**
     * @var  \MongoDB\Driver\Manager
     */
    private $_manager;
    private $_options;

    public function __construct()
    {
        $this->_options = $this->getDbConfig();
        $serverUri = $this->_options['server_uri'];
        $options = [
            'connect' => TRUE,
        ];
        try {
            $this->_manager = new Manager($serverUri, $options);
        } catch (MongoException $e) {
            throw new \Exception('Failed to connect mongodb [' . $e->getMessage() . ']', 500);
        }
    }

    public function __call($method, $args)
    {
        if (!$this->_manager) return false;
        return call_user_func_array(array($this->_manager, $method), $args);
    }

    /**
     * @param mixed ...$args
     * @return MongoDriver
     * @throws \Exception
     */
    public static function query(... $args)
    {
        $obj= new static();
        if (!empty($args)) {
            if(!is_numeric($args[0])) throw new \Exception("分库失败！");
            $table = $obj->getTable();
            $number = MurmurHashUtils::getTableSuffix($args[0]);
            $obj->setTable($table . $number);
        }
        return $obj;
    }

    /**
     * @param array $where
     * @param array $select
     * @param int|null $start
     * @param int|null $limit
     * @param array $sort
     * @return array
     * @throws \MongoDB\Driver\Exception\Exception
     */
    public function find(array $where = array(), array $select = ['_id' => 0], int $start = null, int $limit = null, array $sort = array())
    {
        $options = [
            'projection' => $select,
            'sort' => $sort,
        ];
        if (!Str::isEmpty($start)) {
            $options['skip'] = $start;
        }
        if (!Str::isEmpty($limit)) {
            $options['limit'] = $limit;
        }
        $query = new Query($where, $options);
        $cursor = $this->_manager->executeQuery($this->getConnectDb() . "." . $this->getTable(), $query);
        return empty($cursor) ? [] : $cursor->toArray();
    }


    /**
     * ser_number为分批数据添加序列化标记
     * @param array $params
     * @param int $ser_number
     * @return int|null
     */
    public function insertBatch(array $params = array(), int $ser_number = -1)
    {
        if (empty($params)) return 0;
        $bulk = new BulkWrite;
        foreach ($params as $arr) {
            if (!is_array($arr)) continue;
            if ($ser_number != -1) $arr['ser_number'] = $ser_number;
            $bulk->insert($arr);
        }
        unset($params);
        $result = $this->_manager->executeBulkWrite($this->getConnectDb() . "." . $this->getTable(), $bulk);
        return $result->getInsertedCount();
    }

    /**
     * @param array $params
     * @return int|null
     */
    public function insertOne(array $params = array())
    {
        if (empty($params) || !is_array($params)) return 0;
        $bulk = new BulkWrite;
        $bulk->insert($params);
        unset($params);
        $result = $this->_manager->executeBulkWrite($this->getConnectDb() . "." . $this->getTable(), $bulk);
        return $result->getInsertedCount();
    }

    /**
     * @param array $where
     * @param array $updatParams
     * @param array $unsetParams
     * @param array $incParams
     * @param bool $multi
     * @param bool $upsert
     * @return bool|int
     */
    public function update(array $where = array(), array $updatParams = array(), array $unsetParams = array(), array $incParams = array(), bool $multi = true, bool $upsert = false)
    {
        if (empty($updatParams) && empty($unsetParams) && empty($incParams)) return 0;
        if (!empty($updatParams)) $updateParams = ['$set' => $updatParams];
        if (!empty($unsetParams)) $updateParams['$unset'] = $unsetParams;
        if (!empty($incParams)) $updateParams['$inc'] = $incParams;
        $bulk = new BulkWrite;
        $bulk->update(
            $where,
            $updateParams,
            ['multi' => $multi, 'upsert' => $upsert]
        );
        $result = $this->_manager->executeBulkWrite($this->getConnectDb() . "." . $this->getTable(), $bulk);
        return $result->getModifiedCount() || $result->getUpsertedCount();
    }

    /**
     * @param array $where
     * @param bool $multi 匹配多项
     * @return int|null
     * @throws \Exception
     */
    public function delete(array $where = array(), bool $multi = true)
    {
        $bulk = new BulkWrite;
        $bulk->delete(
            $where,
            ['multi' => $multi]
        );
        $result = $this->_manager->executeBulkWrite($this->getConnectDb() . "." . $this->getTable(), $bulk);
        return $result->getDeletedCount();
    }

    /**
     * @param array $filter
     * @return int
     * @throws \MongoDB\Driver\Exception\Exception
     */
    public function count(array $filter = array())
    {
        $command = new Command([
            'count' => $this->getTable(),//集合名
            'query' => $filter,
        ]);
        $cursor = $this->_manager->executeCommand($this->getConnectDb(), $command);
        if (empty($cursor)) return 0;
        $arr = $cursor->toArray();
        return reset($arr)->n;
    }

    /**
     * @param $pipeline
     * @return array|mixed
     * @throws \MongoDB\Driver\Exception\Exception
     */
    public function aggregate($pipeline)
    {
        $command = new Command([
            'aggregate' => $this->getTable(),//集合名
            'pipeline' => $pipeline,
            'cursor' => new  \stdClass,
        ]);
        $cursor = $this->_manager->executeCommand($this->getConnectDb(), $command);
        return empty($cursor) ? [] : json_decode(json_encode($cursor->toArray()), true);
    }

    /**
     * 设置索引操作
     * @param $indexKeys ['key01'=> 1]
     * @param bool $unique
     * @return bool
     * @throws \MongoDB\Driver\Exception\Exception
     */
    public function ensureIndex($indexKeys, $unique = false)
    {
        $command = new Command([
            'createIndexes' => $this->getTable(),
            'indexes' => [
                [
                    'name' => ($unique ? "uk_" : "idx_") . $indexKeys,
                    'key' => $indexKeys,
                    'unique' => $unique
                ]
            ],
        ]);
        $result = $this->_manager->executeCommand($this->getConnectDb(), $command);
        $response = current($result->toArray());
        return $response->ok == 1;
    }

    /**
     * 删除索引操作
     * @param $indexKeys
     * @param bool $unique
     * @return bool
     * @throws \MongoDB\Driver\Exception\Exception
     */
    public function dropIndex($indexKeys, $unique = false)
    {
        $command = new Command([
            'dropIndexes' => $this->getTable(),
            'index' => ($unique ? "uk_" : "idx_") . $indexKeys
        ]);
        $result = $this->_manager->executeCommand($this->getConnectDb(), $command);
        $response = current($result->toArray());
        return $response->ok == 1;
    }

    /**
     * 删除数据表
     * @return bool
     * @throws \MongoDB\Driver\Exception\Exception
     */
    public function dropDb()
    {
        $command = new Command([
            'drop' => $this->getTable(),
        ]);
        $result = $this->_manager->executeCommand($this->getConnectDb(), $command);
        $response = current($result->toArray());
        return $response->ok == 1;
    }

    /**
     * 获取连接的数据库
     * @return string
     */
    abstract protected function getConnectDb(): string;

    /**
     * 获取表名称
     * @return string
     */
    abstract protected function getTable(): string;

    /**
     * 设置表名称
     * @param string $table
     * @return mixed
     */
    abstract protected function setTable(string $table);

    /**
     * 获取DB配置
     * @return array
     */
    abstract protected function getDbConfig(): array;

}