<?php
/**
 *  author: youwei
 *  date: 7/29/2022
 */

namespace sndwow\yunxin;

use Exception;
use Yii;
use yii\base\Component;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\httpclient\Client;
use yii\queue\Queue;

class Base extends Component
{
    // 网易云信分配的账号
    public string $appKey;
    
    // 网易云信分配的密钥
    public string $appSecret;
    
    // 请求超时时间
    public int $timeout = 5;
    
    // 网易接口基础url
    const NET_EASE_URI = 'https://api.netease.im/nimserver/';
    
    protected bool $isAsync = false;
    
    public array $queue = [];
    
    protected Queue|null $_queue = null;
    
    public function init()
    {
        if ($this->queue) {
            $class = $this->queue['class'];
            unset($this->queue['class']);
            $this->_queue = Yii::createObject($class, ArrayHelper::merge($this->queue,[
                'class' => yii\queue\amqp_interop\Queue::class,
                'as log' => yii\queue\LogBehavior::class,
                'strictJobType' => false,
                'serializer' => \yii\queue\serializers\JsonSerializer::class,
                'qosPrefetchCount' => 500,
            ]));
        }
    }
    
    /**
     * 是否异步发送
     * 若开启异步，将会发送到队列
     *
     * @return $this
     */
    public function async(bool $async = true)
    {
        $this->isAsync = $async;
        return $this;
    }
    
    /**
     * @param string $path
     * @param array $data
     *
     * @return array
     */
    public function post(string $path, array $data)
    {
        $data = $this->bool2String($data);
        
        if ($this->isAsync) {
            $this->_queue->push(['method' => $path, 'data' => $data]);
            $this->isAsync = false;
            return [];
        }
        
        // checksum校验生成
        $nonceStr = Yii::$app->getSecurity()->generateRandomString(128);
        $curTime = (string)time();
        
        $resp = (new Client())->post(self::NET_EASE_URI.$path, $data, [
            'AppKey' => $this->appKey,
            'Nonce' => $nonceStr,
            'CurTime' => $curTime,
            'CheckSum' => sha1($this->appSecret.$nonceStr.$curTime),
        ], ['timeout' => $this->timeout])->send();
        
        if ($resp->statusCode != 200) {
            throw new Exception('NetEase请求错误');
        }
        $ret = Json::decode($resp->content);
        if (!isset($ret['code']) || $ret['code'] != 200) {
            throw new Exception($resp->content);
        }
        return $ret;
    }
    
    /**
     * 将数组中的bool值转换为字符类型
     *
     * @param array $data
     *
     * @return array
     */
    private function bool2String(array $data)
    {
        foreach ($data as &$datum) {
            if (is_bool($datum)) {
                $datum = $datum ? 'true' : 'false';
            } elseif (is_array($datum)) {
                $datum = $this->bool2String($datum);
            }
        }
        
        return $data;
    }
}
