# fastdfs-client-php
PHP实现的FastDFS客户端

### 安装
```
composer require rayson-x/fast-dfs
```

### 介绍
参考[java版](https://github.com/tobato/FastDFS_Client)实现

单元测试使用的服务端版本为FastDFS_V6.07

PHP版本要求8.0以上

### 开始使用

#### 连接到追踪服务器
```php
use Ant\FastDFS\TrackerClient;

include __DIR__ . "/vendor/autoload.php";

// 初始化追踪服务器客户端
$trackerClient = new TrackerClient(['localhost:22122']);
```

#### 从追踪服务器获取存储服务器信息
```php
// 随机获取存储服务客户端
$storageClient = $trackerClient->getStorageClient();
// 根据组名获取
$storageClient = $trackerClient->getStorageClientWithGroup('group1');
// 获取根据断点续传的文件获取可用的存储服务
$storageClient = $trackerClient->getAppendClient('group1', 'M00/00/5B/wKgM_mFMPeWEEzrOAAAAALdUwZ4945.txt');
```

#### 上传一个文件
```php
// 根据文件路径上传
$storePath = $storageClient->uploadFile('filepath');
// 上传字符串
$storePath = $storageClient->uploadBuffer('foobar', 'txt');

// group1/M00/00/52/wKgM_mFFkIiIX2FYAAAAPDbjiZ4AAAAcQCptLQAAABU459.txt
$urlPath = "{$storePath->group}/{$storePath->path}";
```

#### 创建一个断点续传的文件
```php
// 根据文件路径上传
$storePath = $storageClient->uploadFile('filepath', true);
// 上传字符串
$storePath = $storageClient->uploadBuffer('foobar', 'txt', true);
```

#### 在追加内容到支持断点续传文件上
```php
// 追加一个文件
$storageClient->appendFile($storePath->path, 'filepath');
// 追加字符串
$storageClient->appendFile($storePath->path, 'foobar');
```

### 参考文档
[FastDFS的架构原理参考](https://github.com/tobato/FastDFS_Client/wiki).
[协议参考](http://weakyon.com/2014/09/01/analysis-of-source-code-for-fastdfs.html)  

### TODO
* 分部署部署时,多台tracker服务器处理
* 实现一个可自定义的连接器
* 支持ReactPhp,Swoole,默认Socket
* 常驻进程模式支持连接池