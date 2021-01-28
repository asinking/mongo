# mongo 
mongoQuery

# 构建数据查询模型
```javascript 

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

