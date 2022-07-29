<?php
/**
 *  author: youwei
 *  date: 7/29/2022
 */

namespace sndwow\yunxin;

use yii\base\Component;

/**
 * @property User $user
 * @property Chatroom $chatroom
 */
class Yunxin extends Component
{
    // 网易云信分配的账号
    public string $appKey;
    
    // 网易云信分配的密钥
    public string $appSecret;
    
    // 请求超时时间
    public int $timeout = 5;
    
    public function getUser()
    {
        return new User(['appKey' => $this->appKey, 'appSecret' => $this->appSecret, 'timeout' => $this->timeout]);
    }
    
    public function getChatroom()
    {
        return new Chatroom(['appKey' => $this->appKey, 'appSecret' => $this->appSecret, 'timeout' => $this->timeout]);
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
