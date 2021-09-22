<?php

namespace Ant\FastDFS\Protocols\Struct;

use Ant\FastDFS\Constants\Common;
use Ant\FastDFS\Protocols\FastDFSParam;

/**
 * 存储节点状态
 */
class StorageState
{
    /**
     * 状态代码
     */
    #[FastDFSParam(type: FastDFSParam::TYPE_BYTE, index: 0)]
    public $status;

    /**
     * id
     */
    #[FastDFSParam(type: FastDFSParam::TYPE_STRING, index: 1, max: Common::STORAGE_ID_MAX_SIZE)]
    public $id;

    /**
     * ip地址
     */
    #[FastDFSParam(type: FastDFSParam::TYPE_STRING, index: 2, max: Common::IP_ADRESS_SIZE)]
    public $ipAddr;

    /**
     * domain
     */
    #[FastDFSParam(type: FastDFSParam::TYPE_STRING, index: 3, max: Common::DOMAIN_NAME_MAX_SIZE)]
    public $domainName;

    /**
     * 源ip地址
     */
    #[FastDFSParam(type: FastDFSParam::TYPE_STRING, index: 4, max: Common::IP_ADRESS_SIZE)]
    public $srcIpAddr;

    /**
     * version
     */
    #[FastDFSParam(type: FastDFSParam::TYPE_STRING, index: 5, max: Common::VERSION_SIZE)]
    public $version;

    /**
     * 存储加入时间
     */
    #[FastDFSParam(type: FastDFSParam::TYPE_INT, index: 6)]
    public $joinTime; // storage join timestamp (create timestamp)

    /**
     * 存储更新时间
     */
    #[FastDFSParam(type: FastDFSParam::TYPE_INT, index: 7)]
    public $upTime; // storage service started timestamp

    /**
     * 存储总容量
     */
    #[FastDFSParam(type: FastDFSParam::TYPE_INT, index: 8)]
    public $totalMB; // total disk storage in MB

    /**
     * 空闲存储
     */
    #[FastDFSParam(type: FastDFSParam::TYPE_INT, index: 9)]
    public $freeMB; // free disk storage in MB

    /**
     * 文件上传权重
     */
    #[FastDFSParam(type: FastDFSParam::TYPE_INT, index: 10)]
    public $uploadPriority; // upload priority
    /**
     * 存储路径数
     */
    #[FastDFSParam(type: FastDFSParam::TYPE_INT, index: 11)]
    public $storePathCount; // store base path count of each storage
    // server
    /**
     * 存储路径子目录数
     */
    #[FastDFSParam(type: FastDFSParam::TYPE_INT, index: 12)]
    public $subdirCountPerPath;
    /**
     * 当前写路径
     */
    #[FastDFSParam(type: FastDFSParam::TYPE_INT, index: 13)]
    public $currentWritePath; // current write path index
    /**
     * 存储端口
     */
    #[FastDFSParam(type: FastDFSParam::TYPE_INT, index: 14)]
    public $storagePort;
    /**
     * 存储http端口
     */
    #[FastDFSParam(type: FastDFSParam::TYPE_INT, index: 15)]
    public $storageHttpPort; // storage http server port
    #[FastDFSParam(type: FastDFSParam::TYPE_INT, index: 16, max: Common::INT_SIZE)]
    public $connectionAllocCount;
    #[FastDFSParam(type: FastDFSParam::TYPE_INT, index: 17, max: Common::INT_SIZE)]
    public $connectionCurrentCount;
    #[FastDFSParam(type: FastDFSParam::TYPE_INT, index: 18, max: Common::INT_SIZE)]
    public $connectionMaxCount;

    /**
     * 总上传文件数
     */
    #[FastDFSParam(type: FastDFSParam::TYPE_INT, index: 19)]
    public $totalUploadCount;

    /**
     * 成功上传文件数
     */
    #[FastDFSParam(type: FastDFSParam::TYPE_INT, index: 20)]
    public $successUploadCount;

    /**
     * 合并存储文件数
     */
    #[FastDFSParam(type: FastDFSParam::TYPE_INT, index: 21)]
    public $totalAppendCount;

    /**
     * 成功合并文件数
     */
    #[FastDFSParam(type: FastDFSParam::TYPE_INT, index: 22)]
    public $successAppendCount;

    /**
     * 文件修改数
     */
    #[FastDFSParam(type: FastDFSParam::TYPE_INT, index: 23)]
    public $totalModifyCount;

    /**
     * 文件成功修改数
     */
    #[FastDFSParam(type: FastDFSParam::TYPE_INT, index: 24)]
    public $successModifyCount;

    /**
     * 总清除数
     */
    #[FastDFSParam(type: FastDFSParam::TYPE_INT, index: 25)]
    public $totalTruncateCount;

    /**
     * 成功清除数
     */
    #[FastDFSParam(type: FastDFSParam::TYPE_INT, index: 26)]
    public $successTruncateCount;

    /**
     * 总设置标签数
     */
    #[FastDFSParam(type: FastDFSParam::TYPE_INT, index: 27)]
    public $totalSetMetaCount;

    /**
     * 成功设置标签数
     */
    #[FastDFSParam(type: FastDFSParam::TYPE_INT, index: 28)]
    public $successSetMetaCount;

    /**
     * 总删除文件数
     */
    #[FastDFSParam(type: FastDFSParam::TYPE_INT, index: 29)]
    public $totalDeleteCount;

    /**
     * 成功删除文件数
     */
    #[FastDFSParam(type: FastDFSParam::TYPE_INT, index: 30)]
    public $successDeleteCount;

    /**
     * 总下载量
     */
    #[FastDFSParam(type: FastDFSParam::TYPE_INT, index: 31)]
    public $totalDownloadCount;

    /**
     * 成功下载量
     */
    #[FastDFSParam(type: FastDFSParam::TYPE_INT, index: 32)]
    public $successDownloadCount;

    /**
     * 总获取标签数
     */
    #[FastDFSParam(type: FastDFSParam::TYPE_INT, index: 33)]
    public $totalGetMetaCount;

    /**
     * 成功获取标签数
     */
    #[FastDFSParam(type: FastDFSParam::TYPE_INT, index: 34)]
    public $successGetMetaCount;

    /**
     * 总创建链接数
     */
    #[FastDFSParam(type: FastDFSParam::TYPE_INT, index: 35)]
    public $totalCreateLinkCount;

    /**
     * 成功创建链接数
     */
    #[FastDFSParam(type: FastDFSParam::TYPE_INT, index: 36)]
    public $successCreateLinkCount;

    /**
     * 总删除链接数
     */
    #[FastDFSParam(type: FastDFSParam::TYPE_INT, index: 37)]
    public $totalDeleteLinkCount;

    /**
     * 成功删除链接数
     */
    #[FastDFSParam(type: FastDFSParam::TYPE_INT, index: 38)]
    public $successDeleteLinkCount;

    /**
     * 总上传数据量
     */
    #[FastDFSParam(type: FastDFSParam::TYPE_INT, index: 39)]
    public $totalUploadBytes;

    /**
     * 成功上传数据量
     */
    #[FastDFSParam(type: FastDFSParam::TYPE_INT, index: 40)]
    public $successUploadBytes;

    /**
     * 合并数据量
     */
    #[FastDFSParam(type: FastDFSParam::TYPE_INT, index: 41)]
    public $totalAppendBytes;

    /**
     * 成功合并数据量
     */
    #[FastDFSParam(type: FastDFSParam::TYPE_INT, index: 42)]
    public $successAppendBytes;

    /**
     * 修改数据量
     */
    #[FastDFSParam(type: FastDFSParam::TYPE_INT, index: 43)]
    public $totalModifyBytes;

    /**
     * 成功修改数据量
     */
    #[FastDFSParam(type: FastDFSParam::TYPE_INT, index: 44)]
    public $successModifyBytes;

    /**
     * 下载数据量
     */
    #[FastDFSParam(type: FastDFSParam::TYPE_INT, index: 45)]
    public $totalDownloadloadBytes;

    /**
     * 成功下载数据量
     */
    #[FastDFSParam(type: FastDFSParam::TYPE_INT, index: 46)]
    public $successDownloadloadBytes;

    /**
     * 同步数据量
     */
    #[FastDFSParam(type: FastDFSParam::TYPE_INT, index: 47)]
    public $totalSyncInBytes;

    /**
     * 成功同步数据量
     */
    #[FastDFSParam(type: FastDFSParam::TYPE_INT, index: 48)]
    public $successSyncInBytes;

    /**
     * 同步输出数据量
     */
    #[FastDFSParam(type: FastDFSParam::TYPE_INT, index: 49)]
    public $totalSyncOutBytes;

    /**
     * 成功同步输出数据量
     */
    #[FastDFSParam(type: FastDFSParam::TYPE_INT, index: 50)]
    public $successSyncOutBytes;

    /**
     * 打开文件数量
     */
    #[FastDFSParam(type: FastDFSParam::TYPE_INT, index: 51)]
    public $totalFileOpenCount;

    /**
     * 成功打开文件数量
     */
    #[FastDFSParam(type: FastDFSParam::TYPE_INT, index: 52)]
    public $successFileOpenCount;

    /**
     * 文件读取数量
     */
    #[FastDFSParam(type: FastDFSParam::TYPE_INT, index: 53)]
    public $totalFileReadCount;

    /**
     * 文件成功读取数量
     */
    #[FastDFSParam(type: FastDFSParam::TYPE_INT, index: 54)]
    public $successFileReadCount;

    /**
     * 文件写数量
     */
    #[FastDFSParam(type: FastDFSParam::TYPE_INT, index: 56)]
    public $totalFileWriteCount;

    /**
     * 文件成功写数量
     */
    #[FastDFSParam(type: FastDFSParam::TYPE_INT, index: 57)]
    public $successFileWriteCount;

    /**
     * 最后上传时间
     */
    #[FastDFSParam(type: FastDFSParam::TYPE_INT, index: 58)]
    public $lastSourceUpdate;

    /**
     * 最后同步时间
     */
    #[FastDFSParam(type: FastDFSParam::TYPE_INT, index: 59)]
    public $lastSyncUpdate;

    /**
     * 最后同步时间戳
     */
    #[FastDFSParam(type: FastDFSParam::TYPE_INT, index: 60)]
    public $lastSyncedTimestamp;

    /**
     * 最后心跳时间
     */
    #[FastDFSParam(type: FastDFSParam::TYPE_INT, index: 61)]
    public $lastHeartBeatTime;

    /**
     * 是否trunk服务器
     */
    #[FastDFSParam(type: FastDFSParam::TYPE_BOOL, index: 62)]
    public $isTrunkServer;
}
