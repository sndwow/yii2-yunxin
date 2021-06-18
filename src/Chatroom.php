<?php
/**
 *  author: youwei
 *  date: 17/05/2021
 */

namespace sndwow\yunxin;

use Exception;
use Yii;

/**
 * 聊天室接口
 */
class Chatroom extends Base
{
    /**
     * 创建聊天室
     *
     * @see https://dev.yunxin.163.com/docs/product/IM%E5%8D%B3%E6%97%B6%E9%80%9A%E8%AE%AF/%E6%9C%8D%E5%8A%A1%E7%AB%AFAPI%E6%96%87%E6%A1%A3/%E8%81%8A%E5%A4%A9%E5%AE%A4?pos=toc-0-0-5
     *
     * @param string $creator 聊天室属主的账号accid
     * @param string $name 聊天室名称，长度限制128个字符
     * @param array $options
     *
     * @return array
     * @throws Exception
     */
    public function create(string $creator, string $name, array $options = [])
    {
        $ret = $this->send('chatroom/create.action', array_merge($options, ['creator' => $creator, 'name' => $name]));
        return $ret['chatroom'] ?? null;
    }
    
    /**
     * 获取聊天室信息
     *
     * @see https://dev.yunxin.163.com/docs/product/IM%E5%8D%B3%E6%97%B6%E9%80%9A%E8%AE%AF/%E6%9C%8D%E5%8A%A1%E7%AB%AFAPI%E6%96%87%E6%A1%A3/%E8%81%8A%E5%A4%A9%E5%AE%A4?pos=toc-0-0-5
     *
     * @param int $roomid 聊天室id
     * @param bool $needOnlineUserCount 是否需要返回在线人数，true或false，默认false
     *
     * @return array
     * @throws Exception
     */
    public function get(int $roomid, bool $needOnlineUserCount)
    {
        $ret = $this->send('chatroom/get.action', ['creator' => $roomid, 'needOnlineUserCount' => $needOnlineUserCount]);
        return $ret['chatroom'] ?? null;
    }
    
    /**
     * 更新聊天室信息
     *
     * @see https://dev.yunxin.163.com/docs/product/IM%E5%8D%B3%E6%97%B6%E9%80%9A%E8%AE%AF/%E6%9C%8D%E5%8A%A1%E7%AB%AFAPI%E6%96%87%E6%A1%A3/%E8%81%8A%E5%A4%A9%E5%AE%A4?pos=toc-0-0-5
     *
     * @param int $roomid 聊天室id
     * @param array $options 是否需要返回在线人数，true或false，默认false
     *
     * @return array
     * @throws Exception
     */
    public function update(int $roomid, array $options = [])
    {
        $ret = $this->send('chatroom/update.action', array_merge($options, ['roomid' => $roomid]));
        return $ret['chatroom'] ?? null;
    }
    
    /**
     * 修改聊天室开/关闭状态
     *
     * @see https://dev.yunxin.163.com/docs/product/IM%E5%8D%B3%E6%97%B6%E9%80%9A%E8%AE%AF/%E6%9C%8D%E5%8A%A1%E7%AB%AFAPI%E6%96%87%E6%A1%A3/%E8%81%8A%E5%A4%A9%E5%AE%A4?pos=toc-0-0-5
     *
     * @param int $roomid 聊天室id
     * @param string $operator 操作者账号，必须是创建者才可以操作
     * @param bool $valid true或false，false:关闭聊天室；true:打开聊天室
     *
     * @return array
     * @throws Exception
     */
    public function toggleCloseStat(int $roomid, string $operator, bool $valid)
    {
        $ret = $this->send('chatroom/toggleCloseStat.action', ['roomid' => $roomid, 'operator' => $operator, 'valid' => $valid]);
        return $ret['desc'] ?? null;
    }
    
    /**
     * 设置聊天室内用户角色
     *
     * @see https://dev.yunxin.163.com/docs/product/IM%E5%8D%B3%E6%97%B6%E9%80%9A%E8%AE%AF/%E6%9C%8D%E5%8A%A1%E7%AB%AFAPI%E6%96%87%E6%A1%A3/%E8%81%8A%E5%A4%A9%E5%AE%A4?pos=toc-0-0-5
     *
     * @param int $roomid 聊天室id
     * @param string $operator 操作者账号accid
     * @param string $target 被操作者账号accid
     *
     * @param int $opt 操作：
     * 1: 设置为管理员，operator必须是创建者
     * 2:设置普通等级用户，operator必须是创建者或管理员
     * -1:设为黑名单用户，operator必须是创建者或管理员
     * -2:设为禁言用户，operator必须是创建者或管理员
     *
     * @param bool $optvalue true或false，true:设置；false:取消设置；执行“取消”设置后，若成员非禁言且非黑名单，则变成游客
     * @param string $notifyExt 通知扩展字段，长度限制2048，请使用json格式
     *
     * @return array
     * @throws Exception
     */
    public function setMemberRole(int $roomid, string $operator, string $target, int $opt, bool $optvalue, string $notifyExt = '')
    {
        $ret = $this->send('chatroom/setMemberRole.action', [
            'roomid' => $roomid,
            'operator' => $operator,
            'target' => $target,
            'opt' => $opt,
            'optvalue' => $optvalue,
            'notifyExt' => $notifyExt,
        ]);
        return $ret['desc'] ?? null;
    }
    
    /**
     * 发送聊天室消息
     *
     * @see https://dev.yunxin.163.com/docs/product/IM%E5%8D%B3%E6%97%B6%E9%80%9A%E8%AE%AF/%E6%9C%8D%E5%8A%A1%E7%AB%AFAPI%E6%96%87%E6%A1%A3/%E8%81%8A%E5%A4%A9%E5%AE%A4?pos=toc-0-0-5
     *
     * @param int $roomid 聊天室id
     * @param string $fromAccid 客户端消息id，使用uuid等随机串，msgId相同的消息会被客户端去重
     * @param int $msgType 消息发出者的账号accid
     * @param array $options
     *
     * @return array
     * @throws \yii\base\Exception
     */
    public function sendMsg(int $roomid, string $fromAccid, int $msgType, array $options = [])
    {
        $ret = $this->send('chatroom/sendMsg.action', array_merge($options, [
            'roomid' => $roomid,
            'fromAccid' => $fromAccid,
            'msgType' => $msgType,
            'msgId' => $options['msgId'] ?? Yii::$app->security->generateRandomString(),
        ]));
        return $ret['desc'] ?? null;
    }
    
    /**
     * 排序列出队列中所有元素
     *
     * @see https://dev.yunxin.163.com/docs/product/IM%E5%8D%B3%E6%97%B6%E9%80%9A%E8%AE%AF/%E6%9C%8D%E5%8A%A1%E7%AB%AFAPI%E6%96%87%E6%A1%A3/%E8%81%8A%E5%A4%A9%E5%AE%A4?pos=toc-0-0-5
     *
     * @param int $roomid 聊天室id
     *
     * @return array
     * @throws Exception
     */
    public function queuePoll(int $roomid)
    {
        $ret = $this->send('chatroom/queueList.action', ['roomid' => $roomid]);
        return $ret['desc']['list'] ?? null;
    }
    
    /**
     * 删除清理整个队列
     *
     * @see https://dev.yunxin.163.com/docs/product/IM%E5%8D%B3%E6%97%B6%E9%80%9A%E8%AE%AF/%E6%9C%8D%E5%8A%A1%E7%AB%AFAPI%E6%96%87%E6%A1%A3/%E8%81%8A%E5%A4%A9%E5%AE%A4?pos=toc-0-0-5
     *
     * @param int $roomid 聊天室id
     *
     * @throws Exception
     */
    public function queueDrop(int $roomid)
    {
        $this->send('chatroom/queueDrop.action', ['roomid' => $roomid]);
    }
    
    /**
     * 初始化队列
     *
     * @see https://dev.yunxin.163.com/docs/product/IM%E5%8D%B3%E6%97%B6%E9%80%9A%E8%AE%AF/%E6%9C%8D%E5%8A%A1%E7%AB%AFAPI%E6%96%87%E6%A1%A3/%E8%81%8A%E5%A4%A9%E5%AE%A4?pos=toc-0-0-5
     *
     * @param int $roomid 聊天室id
     * @param int $sizeLimit 队列长度限制，0-1000
     *
     * @throws Exception
     */
    public function queueInit(int $roomid, int $sizeLimit = 1000)
    {
        $this->send('chatroom/queueInit.action', ['roomid' => $roomid, 'sizeLimit' => $sizeLimit]);
    }
    
    /**
     * 往聊天室有序队列中新加或更新元素
     *
     * @see https://dev.yunxin.163.com/docs/product/IM%E5%8D%B3%E6%97%B6%E9%80%9A%E8%AE%AF/%E6%9C%8D%E5%8A%A1%E7%AB%AFAPI%E6%96%87%E6%A1%A3/%E8%81%8A%E5%A4%A9%E5%AE%A4?pos=toc-0-0-5
     *
     * @param int $roomid 聊天室id
     * @param string $key elementKey,新元素的UniqKey,长度限制128字符
     * @param string $value elementValue,新元素内容，长度限制4096字符
     *
     * @param string $operator 提交这个新元素的操作者accid，默认为该聊天室的创建者，若operator对应的帐号不存在，会返回404错误。
     * 若指定的operator不在线，则添加元素成功后的通知事件中的操作者默认为聊天室的创建者；若指定的operator在线，则通知事件的操作者为operator。
     *
     * @param bool $transient
     * 这个新元素的提交者operator的所有聊天室连接在从该聊天室掉线或者离开该聊天室的时候，提交的元素是否需要删除。
     * true：需要删除；false：不需要删除。默认false。
     * 当指定该参数为true时，若operator当前不在该聊天室内，则会返回403错误。
     *
     * @throws Exception
     */
    public function queueOffer(int $roomid, string $key, string $value, string $operator = '', bool $transient = false)
    {
        
        $data = ['roomid' => $roomid, 'key' => $key, 'value' => $value, 'transient' => $transient];
        if ($operator) {
            $data['operator'] = $operator;
        }
        
        $this->send('chatroom/queueOffer.action', $data);
    }
    
}
