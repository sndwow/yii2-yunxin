<?php

namespace sndwow\yunxin;

use yii\base\Component;

/**
 * 文件入口
 *
 * @property User $user 用户
 * @property Msg $msg  消息
 * @property Friend $friend  好友
 * @property Chatroom $chatroom  好友
 */
class Entry extends Component
{
    /**
     * 网易云信分配的账号
     *
     * @var string
     */
    public $appKey;
    
    /**
     * 网易云信分配的密钥
     *
     * @var string
     */
    public $appSecret;
    
    /**
     * 请求超时时间
     *
     * @var int
     */
    public $timeout = 5;
    
    /**
     * 组件队列ID
     * 用于定义异步调用时，消息默认推送到哪个yii组件队列
     *
     * @var string
     */
    public $queueId;
    
    /**
     * 构建类的数据
     *
     * @var array
     */
    private $constructData;
    
    public function init()
    {
        parent::init();
        $this->constructData = [
            'appKey' => $this->appKey,
            'appSecret' => $this->appSecret,
            'timeout' => $this->timeout,
            'defaultQueueId' => $this->queueId,
        ];
    }
    
    /**
     * 抄送消息验证检验码
     *
     * @param $body
     * @param $curTime
     * @param $checksumPost
     *
     * @return bool
     */
    public function isLegalChecksum($body, $curTime, $checksumPost)
    {
        return sha1($this->appSecret.md5($body).$curTime) === $checksumPost;
    }
    
    public function getUser()
    {
        return new User($this->constructData);
    }
    
    public function getFriend()
    {
        return new Friend($this->constructData);
    }
    
    public function getMsg()
    {
        return new Msg($this->constructData);
    }
    
    public function getChatroom()
    {
        return new Chatroom($this->constructData);
    }
    
}
