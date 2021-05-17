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
        return new User(['appKey' => $this->appKey, 'appSecret' => $this->appSecret, 'timeout' => $this->timeout]);
    }
    
    public function getFriend()
    {
        return new Friend(['appKey' => $this->appKey, 'appSecret' => $this->appSecret, 'timeout' => $this->timeout]);
    }
    
    public function getMsg()
    {
        return new Msg(['appKey' => $this->appKey, 'appSecret' => $this->appSecret, 'timeout' => $this->timeout]);
    }
    
    public function getChatroom()
    {
        return new Chatroom(['appKey' => $this->appKey, 'appSecret' => $this->appSecret, 'timeout' => $this->timeout]);
    }
}
