# mongo 
mongoQuery
适用TP|Larvel|Lumen|Hyper等框架（遵循PSR-4协议）
# 构建数据查询模型
```javascript 
class DcQueryDetails extends Mongo
{
    /**
    *连接的数据库
    */
    public $connect = 'testDb';
    /**
    *操作的表集合
    */
    public $table = 'table_name';
    /**
    *表索引
    */
    public $indexKey = 'id_xxx'

    protected function getDbConfig(): array
    {
        return ['server_uri'=>'mongodb://rs1.example.com,rs2.example.com/?replicaSet=myReplicaSet'];
    }

}
 ```
# 插入数据数据
```javascript 
DcQueryDetails::query()->insertOne($data);
DcQueryDetails::query()->insertBatch($data);
 ```
# 更新数据数据
```javascript 
DcQueryDetails::query()->update($where, $updatParams, $unsetParams, $incParams, $multi, $upsert);
 ```
# 删除数据操作
```javascript 
DcQueryDetails::query()->delete($where, true);
 ```
....其它可参考内部方法

