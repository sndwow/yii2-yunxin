<?php
/**
 *  author: youwei
 *  date: 7/29/2022
 */

namespace sndwow\yunxin;

use Yii;
use yii\base\Component;

/**
 * @property User $user
 * @property Chatroom $chatroom
 */
class Yunxin extends Component
{
    // 网易云信分配的账号
    public ?string $appKey=null;
    
    // 网易云信分配的密钥
    public ?string $appSecret=null;
    
    // 请求超时时间
    public int $timeout = 5;
    
    public array $queue = [];
    
    
    public function getUser()
    {
        return Yii::createObject(User::class, [
            'appKey' => $this->appKey,
            'appSecret' => $this->appSecret,
            'timeout' => $this->timeout,
            'queue'=>$this->queue
        ]);
    }
    
    public function getChatroom()
    {
        return Yii::createObject(Chatroom::class, [
            'appKey' => $this->appKey,
            'appSecret' => $this->appSecret,
            'timeout' => $this->timeout,
            'queue' => $this->queue
        ]);
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
    public function checksum($body, $curTime, $checksumPost)
    {
        return sha1($this->appSecret.md5($body).$curTime) === $checksumPost;
    }
}
