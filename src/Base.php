<?php

namespace sndwow\yunxin;

use Exception;
use Yii;
use yii\base\Component;
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
     * 发送请求
     *
     * @param string $uri
     * @param array $data
     *
     * @return array
     * @throws Exception
     */
    protected function send($uri, array $data):array
    {
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
