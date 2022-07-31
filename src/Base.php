<?php
/**
 *  author: youwei
 *  date: 7/29/2022
 */

namespace sndwow\yunxin;

use Exception;
use Yii;
use yii\base\BaseObject;
use yii\helpers\Json;
use yii\httpclient\Client;

class Base extends BaseObject
{
    // 网易云信分配的账号
    public string $appKey;
    
    // 网易云信分配的密钥
    public string $appSecret;
    
    // 请求超时时间
    public int $timeout = 5;
    
    // 网易接口基础url
    const NET_EASE_URI = 'https://api.netease.im/nimserver/';
    
    /**
     * @param string $path
     * @param array $data
     *
     * @return array
     */
    public function post(string $path, array $data)
    {
        $data = $this->bool2String($data);
        
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
            Yii::error($resp->content, 'yunxin error');
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
