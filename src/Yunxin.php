<?php
/**
 *  author: youwei
 *  date: 7/29/2022
 */

namespace sndwow\yunxin;

use Yii;
use yii\base\Component;
use yii\di\ServiceLocator;
use yii\helpers\ArrayHelper;

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
    
    private ServiceLocator $locator;
    
    public function init()
    {
        $this->locator = new ServiceLocator();
        
        $queue = [];
        if ($this->queue) {
            $queue = ArrayHelper::merge($this->queue, [
                'class' => yii\queue\amqp_interop\Queue::class,
                'as log' => yii\queue\LogBehavior::class,
                'strictJobType' => false,
                'serializer' => \yii\queue\serializers\JsonSerializer::class,
                'qosPrefetchCount' => 500,
            ]);
        }
        
        $this->locator->setComponents([
            'user' => [
                'class' => User::class,
                'appKey' => $this->appKey,
                'appSecret' => $this->appSecret,
                'timeout' => $this->timeout,
                'queue' => $queue,
            ],
            'chatroom' => [
                'class' => Chatroom::class,
                'appKey' => $this->appKey,
                'appSecret' => $this->appSecret,
                'timeout' => $this->timeout,
                'queue' => $queue,
            ],
        ]);
    }
    
    public function getUser()
    {
        return $this->locator->get('user');
    }
    
    public function getChatroom()
    {
        return $this->locator->get('chatroom');
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
