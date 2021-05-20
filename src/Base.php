<?php

namespace sndwow\yunxin;

use Exception;
use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\httpclient\Client;

abstract class Base extends Component
{
    private $baseUrl = 'https://api.netease.im/nimserver/';
    
    /**
     * @var string
     */
    public $appKey;
    
    /**
     * @var string
     */
    public $appSecret;
    
    /**
     * @var int
     */
    public $timeout;
    
    /**
     * @var string 默认队列
     */
    public $defaultQueueId;
    
    /**
     * 此刻要发送的队列，空标识不发送队列
     *
     * @var string
     */
    private $curQueueId;
    
    /**
     * 设置本次消息发送为异步消息，消息将被推送到指定组件的队列中
     * 异步发送时，所有接口都返回空值
     *
     * @param string $queueId Yii队列组件id，若不指定则使用默认组件id，若组件不存在则报错
     *
     * @return $this
     * @throws InvalidConfigException
     */
    public function async(string $queueId = '')
    {
        $this->curQueueId = $queueId ?: $this->defaultQueueId;
        
        $id = $this->curQueueId;
        
        if (empty(Yii::$app->$id)) {
            throw new InvalidConfigException('云信异步组件无效');
        }
        
        return $this;
    }
    
    /**
     * 发送请求
     *
     * @param string $uri
     * @param array $data
     *
     * @return array
     * @throws Exception
     */
    protected function send(string $uri, array $data):array
    {
        // 异步发送
        if ($this->curQueueId) {
            $id = $this->curQueueId;
            
            /* @var \yii\queue\amqp_interop\Queue $c */
            $c = Yii::$app->$id;
            $c->push(['method' => $uri, 'data' => $data]);
            $this->curQueueId = '';
            return [];
        }
        
        // checksum校验生成
        $nonceStr = Yii::$app->getSecurity()->generateRandomString(128);
        $curTime = (string)time();
        
        $response = (new Client())->post(
            $this->baseUrl.$uri,
            $this->bool2String($data),
            [
                'AppKey' => $this->appKey,
                'Nonce' => $nonceStr,
                'CurTime' => $curTime,
                'CheckSum' => sha1($this->appSecret.$nonceStr.$curTime),
            ],
            ['timeout' => $this->timeout]
        )->send();
        
        if ($response->getStatusCode() != 200) {
            throw new Exception('NetEase Network Error: '.$response->getStatusCode());
        }
        
        $arr = json_decode($response->getContent(), true);
        if (!isset($arr['code']) || $arr['code'] != 200) {
            throw new Exception('NetEase response error：'.$response->getContent());
        }
        
        return $arr;
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
