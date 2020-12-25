# mongo
mongoQuery
#构建数据查询模型
class DcQueryDetails extends Mongo
{
    public $connect = 'testDb';
    public $table = 'table_name';


    protected function getDbConfig(): array
    {
        return ['server_uri'=>'mongodb://rs1.example.com,rs2.example.com/?replicaSet=myReplicaSet'];
    }

}
# 插入数据数据
DcQueryDetails::query()->insertOne($data);
DcQueryDetails::query()->insertBatch($data);

# 更新数据数据
DcQueryDetails::query()->update($where, $updatParams, $unsetParams, $incParams, $multi, $upsert);

# 删除数据操作
DcQueryDetails::query()->delete($where, true);

....其它可参考内部方法

