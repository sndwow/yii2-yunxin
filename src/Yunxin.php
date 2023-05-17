<?php
/**
 *  author: youwei
 *  date: 7/29/2022
 */

namespace sndwow\yunxin;

use Yii;
use yii\base\BaseObject;
use yii\di\ServiceLocator;

/**
 * @property User $user
 * @property Chatroom $chatroom
 */
class Yunxin extends BaseObject
{
    public ?string $appKey = null; // 网易云信分配的账号
    public ?string $appSecret = null; // 网易云信分配的密钥
    public int $timeout = 5; // 请求超时时间
    public array $queue = [];
    
    private ?object $_queue = null;
    private ServiceLocator $locator;
    
    public function init()
    {
        parent::init();
        
        $this->locator = new ServiceLocator();
        $this->locator->set('user', [
            'class' => User::class,
            'appKey' => $this->appKey,
            'appSecret' => $this->appSecret,
            'timeout' => $this->timeout,
            'queue' => $this->queue(),
        ]);
        $this->locator->set('chatroom', [
            'class' => Chatroom::class,
            'appKey' => $this->appKey,
            'appSecret' => $this->appSecret,
            'timeout' => $this->timeout,
            'queue' => $this->queue(),
        ]);
    }
    
    public function __get($name)
    {
        return $this->locator->get($name);
    }
    
    private function queue()
    {
        if (!$this->queue) {
            return null;
        }
        
        if (!$this->_queue) {
            $this->_queue = Yii::createObject($this->queue);
        }
        
        return $this->_queue;
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
