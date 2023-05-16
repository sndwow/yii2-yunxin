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
    public ?string $appKey = null;
    
    // 网易云信分配的密钥
    public ?string $appSecret = null;
    
    // 请求超时时间
    public int $timeout = 5;
    
    public array $queue = [];
    
    private ?User $_user = null;
    private ?Chatroom $_chatroom = null;
    
    public function getUser()
    {
        if (!$this->_user) {
            $q = $this->queue;
            $this->_user = new User();
            $this->_user->appKey = $this->appKey;
            $this->_user->appSecret = $this->appSecret;
            $this->_user->timeout = $this->timeout;
            $this->_user->queue = Yii::$app->$q;
        }
        return $this->_user;
    }
    
    public function getChatroom()
    {
        if (!$this->_chatroom) {
            $q = $this->queue;
            $this->_chatroom = new Chatroom();
            $this->_chatroom->appKey = $this->appKey;
            $this->_chatroom->appSecret = $this->appSecret;
            $this->_chatroom->timeout = $this->timeout;
            $this->_chatroom->queue = Yii::$app->$q;
        }
        return $this->_user;
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
