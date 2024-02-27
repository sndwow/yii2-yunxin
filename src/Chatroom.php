<?php
/**
 *  author: youwei
 *  date: 17/05/2021
 */

namespace sndwow\yunxin;

use Yii;
use yii\base\Exception;
use yii\base\NotSupportedException;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

/**
 * 聊天室接口
 */
class Chatroom extends Base
{
    /**
     * 创建聊天室
     *
     * @param string $creator 聊天室属主的账号accid
     * @param string $name 聊天室名称，长度限制128个字符
     * @param array $options
     *
     * @return int 返回聊天室id，失败返回0
     */
    public function create(string $creator, string $name, array $options = [])
    {
        $r = $this->post('chatroom/create.action', ArrayHelper::merge($options, ['creator' => $creator, 'name' => $name]));
        return (int)($r['chatroom']['roomid'] ?? 0);
    }

    /**
     * 获取聊天室信息
     *
     * @param int $roomId 聊天室id
     * @param bool $needOnlineUserCount 是否需要返回在线人数
     *
     * @return array|null 失败返回null
     */
    public function get(int $roomId, bool $needOnlineUserCount = true)
    {
        $r = $this->post('chatroom/get.action', ['roomid' => $roomId, 'needOnlineUserCount' => $needOnlineUserCount]);
        return $r['chatroom'] ?? null;
    }

    /**
     * 更新聊天室信息
     *
     * @param int $roomId 聊天室id
     * @param array $options 更新选项
     */
    public function update(int $roomId, array $options = [])
    {
        $this->post('chatroom/update.action', ArrayHelper::merge($options, ['roomid' => $roomId]));
    }

    /**
     * 修改聊天室开/关闭状态
     *
     * @param int $roomId 聊天室id
     * @param string $creatorId 创建者账号
     * @param bool $isClose true 关闭 false 打开
     */
    public function close(int $roomId, string $creatorId, bool $isClose)
    {
        $this->post('chatroom/toggleCloseStat.action', [
            'roomid' => $roomId,
            'operator' => $creatorId,
            'valid' => !$isClose,
        ]);
    }

    /**
     * 设置角色
     *
     * @param int $roomId 聊天室id
     * @param string $operator 操作者账号accid
     * @param string $target 被操作者账号accid
     *
     * @param int $opt 操作：
     * 1: 设置为管理员，operator必须是创建者
     * 2:设置普通等级用户，operator必须是创建者或管理员
     * -1:设为黑名单用户，operator必须是创建者或管理员
     * -2:设为禁言用户，operator必须是创建者或管理员
     *
     * @param bool $optValue true或false，true:设置；false:取消设置；执行“取消”设置后，若成员非禁言且非黑名单，则变成游客
     * @param string $notifyExt 通知扩展字段，长度限制2048，请使用json格式
     */
    public function setRole(int $roomId, string $operator, string $target, int $opt, bool $optValue, string $notifyExt = '')
    {
        $this->post('chatroom/setMemberRole.action', [
            'roomid' => $roomId,
            'operator' => $operator,
            'target' => $target,
            'opt' => $opt,
            'optvalue' => $optValue,
            'notifyExt' => $notifyExt,
        ]);
    }

    /**
     * 设置角色信息（前提是得有角色）
     *
     * @param int $roomId
     * @param string $accid
     * @param array $options
     */
    public function setRoleInfo(int $roomId, string $accid, array $options = [])
    {
        $this->post('chatroom/updateMyRoomRole.action', ArrayHelper::merge($options, [
            'roomid' => $roomId,
            'accid' => $accid,
        ]));
    }

    /**
     * 发送消息
     *
     * @param int $roomId 聊天室id
     * @param string $accid 发起者
     * @param int $msgType 消息发出者的账号accid
     * @param array $options
     */
    public function sendMsg(int $roomId, string $accid, int $msgType, array $options = [])
    {
        $msgId = $options['msgId'] ?? '';
        if (!$msgId) {
            $msgId = md5(Yii::$app->security->generateRandomString().microtime());
        }

        $this->post('chatroom/sendMsg.action', array_merge($options, [
            'roomid' => $roomId,
            'fromAccid' => $accid,
            'msgType' => $msgType,
            'msgId' => $msgId,
        ]));
    }

    /**
     * 发送消息 - 批量
     *
     * @param int $roomId 聊天室id
     * @param array $msgList
     * @param string $accid 发起者
     * @param array $options
     *
     * @throws Exception
     * @throws NotSupportedException
     */
    public function sendMsgBatch(int $roomId,array $msgList, string $accid, array $options = [])
    {
        $this->post('chatroom/batchSendMsg.action', array_merge($options, [
            'roomid' => $roomId,
            'fromAccid' => $accid,
            'msgList' => Json::encode($msgList),
        ]));
    }

    /**
     * 发送定向消息
     *
     * @param int $roomId 聊天室id
     * @param string $accid 发起者
     * @param int $msgType 消息发出者的账号accid
     * @param array $toAccids
     * @param array $options
     */
    public function sendMsgToSomeone(int $roomId, string $accid, int $msgType, array $toAccids, array $options = [])
    {
        $msgId = $options['msgId'] ?? '';
        if (!$msgId) {
            $msgId = md5(Yii::$app->security->generateRandomString().microtime());
        }

        $this->post('chatroom/sendMsgToSomeone.action', array_merge($options, [
            'roomid' => $roomId,
            'fromAccid' => $accid,
            'msgType' => $msgType,
            'msgId' => $msgId,
            'toAccids' => Json::encode($toAccids),
        ]));
    }

    /**
     * 关闭指定聊天室进出通知
     *
     * @param int $roomId
     * @param bool $close true：关闭进出通知，false：不关闭
     */
    public function closeInOutNotice(int $roomId, bool $close)
    {
        $this->post('chatroom/updateInOutNotification.action', ['roomid' => $roomId, 'close' => $close]);
    }

    /**
     * 全服广播消息
     *
     * @param string $accid 发送者
     * @param int $msgType 消息类型
     * @param array $options
     */
    public function broadcast(string $accid, int $msgType, array $options = [])
    {
        $msgId = $options['msgId'] ?? '';
        if (!$msgId) {
            $msgId = md5(Yii::$app->security->generateRandomString().microtime());
        }

        $data = array_merge($options, ['msgId' => $msgId, 'fromAccid' => $accid, 'msgType' => $msgType]);
        $this->post('chatroom/broadcast.action', $data);
    }

    /**
     * 聊天室云端历史消息查询
     *
     * @param int $roomId 聊天室ID
     * @param string $accid 用户账号
     * @param int $timeTag 查询的时间戳锚点，13位。reverse=1时timeTag为起始时间戳，reverse=2时timeTag为终止时间戳
     * @param int $limit 本次查询的消息条数上限(最多200条),小于等于0，或者大于200，会提示参数错误
     * @param int $reverse 1按时间正序排列，2按时间降序排列。其它返回参数414错误。默认是2按时间降序排列
     * @param string $type 查询指定的多个消息类型，类型之间用","分割，不设置该参数则查询全部类型消息。格式示例： 0,1,2,3
     * 支持的消息类型：0:文本，1:图片，2:语音，3:视频，4:地理位置，5:通知，6:文件，10:提示，11:智能机器人消息，100:自定义消息。用英文逗号分隔。
     * @return array
     * @throws NotSupportedException
     */
    public function queryChatroomMsg(int $roomId, string $accid, int $timeTag, int $limit, int $reverse = 2, string $type = '')
    {
        return $this->post('history/queryChatroomMsg.action', [
            'roomid' => $roomId,
            'accid' => $accid,
            'timetag' => $timeTag,
            'limit' => $limit,
            'reverse' => $reverse,
            'type' => $type,
        ]);
    }
}
