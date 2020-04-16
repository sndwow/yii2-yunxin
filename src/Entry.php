<?php

namespace sndwow\yunxin;

use yii\base\Component;

/**
 * 文件入口
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
    
    private $instances = [];
    
    private $classMap = [
        'user' => User::class,
        'msg' => Msg::class,
        'friend' => Friend::class,
    ];
    
    
    /**
     * @return User
     */
    public function user()
    {
        return $this->factory('user');
    }
    
    /**
     * @return Msg
     */
    public function msg()
    {
        return $this->factory('msg');
    }
    
    /**
     *
     * @return Friend
     */
    public function friend()
    {
        return $this->factory('friend');
    }
    
    private function factory($className)
    {
        if (empty($this->instances[$className])) {
            $this->instances[$className] = new  $this->classMap[$className]($this->appKey, $this->appSecret, $this->timeout);
        }
        return $this->instances[$className];
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
    
    
}
