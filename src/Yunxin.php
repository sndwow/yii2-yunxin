<?php
/**
 *  author: youwei
 *  date: 7/29/2022
 */

namespace sndwow\yunxin;

use Yii;
use yii\base\Component;
use yii\helpers\ArrayHelper;

/**
 * @property User $user
 * @property Chatroom $chatroom
 */
class Yunxin extends Component
{
    
    public ?string $appKey = null; // 网易云信分配的账号
    public ?string $appSecret = null; // 网易云信分配的密钥
    public int $timeout = 5; // 请求超时时间
    
    public array $queue = [];
    
    private ?object $_queue = null;
    private ?User $_user = null;
    private ?Chatroom $_chatroom = null;
    
    private function queue()
    {
        if (!$this->queue) {
            return null;
        }
        
        if (!$this->_queue) {
            $this->_queue = Yii::createObject(ArrayHelper::merge($this->queue, [
                'class' => yii\queue\amqp_interop\Queue::class,
                'as log' => yii\queue\LogBehavior::class,
                'strictJobType' => false,
                'serializer' => \yii\queue\serializers\JsonSerializer::class,
                'qosPrefetchCount' => 500,
            ]));
        }
        
        return $this->_queue;
    }
    
    public function getUser()
    {
        if (!$this->_user) {
            $this->_user = new User();
            $this->_user->appKey = $this->appKey;
            $this->_user->appSecret = $this->appSecret;
            $this->_user->timeout = $this->timeout;
            $this->_user->queue = $this->queue();
        }
        return $this->_user;
    }
    
    public function getChatroom()
    {
        if (!$this->_chatroom) {
            $this->_chatroom = new Chatroom();
            $this->_chatroom->appKey = $this->appKey;
            $this->_chatroom->appSecret = $this->appSecret;
            $this->_chatroom->timeout = $this->timeout;
            $this->_chatroom->queue = $this->queue();
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
