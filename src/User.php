<?php
/**
 *  author: youwei
 *  date: 7/29/2022
 */

namespace sndwow\yunxin;

use Exception;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

/**
 * 用户
 */
class User extends Base
{
    /**
     * 创建用户
     *
     * @param string $accid 账户id，最大长度32字符，必须保证一个APP内唯一。
     * @param array $options 可选参数集合，支持如下：
     *
     * - name: string, 昵称，最大长度64字符。
     * - icon: string, 头像URL，开发者可选填，最大长度1024
     * - token: string,  登录token值，最大长度128字符，并更新，如果未指定，会自动生成token
     * - sign: string, 用户签名，最大长度256字符
     * - email: string, 用户email，最大长度64字符
     * - birth: string, 用户生日，最大长度16字符
     * - mobile: string, 用户mobile，最大长度32字符
     * - gender: int, 用户性别，0表示未知，1表示男，2女表示女，其它会报参数错误
     * - ex: string, 用户名片扩展字段，最大长度1024字符，用户可自行扩展，建议封装成JSON字符串
     *
     * @return string token
     */
    public function create(string $accid, array $options = [])
    {
        $r = $this->post('user/create.action', ArrayHelper::merge($options, ['accid' => $accid]));
        return $r['info']['token'] ?? '';
    }
    
    /**
     * 更新用户token
     *
     * @param string $accid 账户id
     * @param string $token 登录token值（即密码），最大长度128字符
     */
    public function updateToken(string $accid, string $token)
    {
        $this->post('user/update.action', ['accid' => $accid, 'token' => $token]);
    }
    
    /**
     * 封禁用户
     *
     * @param string $accid 账户id
     * @param bool $needKick 是否踢出云信
     */
    public function block(string $accid, bool $needKick = true)
    {
        $this->post('user/block.action', ['accid' => $accid, 'needkick' => $needKick]);
    }
    
    /**
     * 解禁用户
     *
     * @param string $accid 账户id
     */
    public function unblock(string $accid)
    {
        $this->post('user/unblock.action', ['accid' => $accid]);
    }
    
    /**
     * 禁言
     *
     * @param string $accid 账户id
     * @param bool $isMute 是否禁言，true禁言，false解除禁言
     */
    public function mute(string $accid, bool $isMute)
    {
        $this->post('user/mute.action', ['accid' => $accid, 'mute' => $isMute]);
    }
    
    /**
     * 获取用户名片 - 批量
     *
     * @param string $accid
     *
     * @return array
     */
    public function info(string $accid)
    {
        $r = $this->post('user/getUinfos.action', ['accids' => json_encode([$accid])]);
        return $r['uinfos'][0] ?? [];
    }
    
    /**
     * 获取用户名片 - 批量
     *
     * @param array $accids
     *
     * @return array
     */
    public function infoList(array $accids)
    {
        $r = $this->post('user/getUinfos.action', ['accids' => json_encode($accids)]);
        return $r['uinfos'] ?? [];
    }
    
    /**
     * 更新用户信息
     *
     * @param string $accid 用户帐号id
     * @param array $options 可选参数集合，支持参数如下：
     *
     * - name: string, 昵称，最大长度64字符。
     * - icon: string, 头像URL
     * - sign: string, 用户签名，最大长度256字符
     * - email: string, 用户email，最大长度64字符
     * - birth: string, 用户生日，最大长度16字符
     * - mobile: string, 用户mobile，最大长度32字符
     * - gender: int, 用户性别，0表示未知，1表示男，2女表示女，其它会报参数错误
     * - ex: string, 用户名片扩展字段，最大长度1024字符，用户可自行扩展，建议封装成JSON字符串
     */
    public function updateUserInfo(string $accid, array $options)
    {
        $this->post('user/updateUinfo.action', array_merge($options, ['accid' => $accid]));
    }
    
    /**
     * 发送通知
     *
     * @param string $fromAccid 发送者accid，用户帐号，最大32字符，APP内唯一
     * @param string $toAccid 用户id
     * @param string $attach 自定义通知内容
     * @param array $options 可选参数集合，支持如下：
     * - option: string, 指定消息计数等特殊行为,使用 self::noticeOption生成
     * - pushcontent: string, 推送文案,最长500个字符，android以此为推送显示文案；ios若未填写payload，显示文案以pushcontent为准
     * - payload: sting, ios 推送对应的payload,必须是JSON,不能超过2k字符
     * - sound: string, 如果有指定推送，此属性指定为客户端本地的声音文件名，长度不要超过30个字符，如果不指定，会使用默认声音
     * - save: int, 1表示只发在线，2表示会存离线，其他会报414错误。默认会存离线
     */
    public function noticeSend(string $fromAccid, string $toAccid, string $attach, array $options = [])
    {
        $this->post('msg/sendAttachMsg.action', array_merge($options, [
            'from' => $fromAccid,
            'msgtype' => 0,
            'to' => $toAccid,
            'attach' => $attach,
        ]));
    }
    
    /**
     * 发送通知 - 批量
     *
     * @param string $fromAccid 发送者accid，用户帐号，最大32字符，APP内唯一
     * @param array $toAccidList 接收者 最大限5000人
     * @param string $attach 自定义通知内容，第三方组装的字符串，建议是JSON串，最大长度4096字符
     * @param array $options 可选参数集合，支持以下选项:
     *
     * - option: string, 指定消息计数等特殊行为,使用 self::noticeOption生成
     * - pushcontent: string, 推送文案,最长500个字符，android以此为推送显示文案；ios若未填写payload，显示文案以pushcontent为准
     * - payload: sting, ios 推送对应的payload,必须是JSON,不能超过2k字符
     * - sound: string, 如果有指定推送，此属性指定为客户端本地的声音文件名，长度不要超过30个字符，如果不指定，会使用默认声音
     * - save: int, 1表示只发在线，2表示会存离线，其他会报414错误。默认会存离线
     */
    public function noticeSendBatch(string $fromAccid, array $toAccidList, string $attach, array $options = [])
    {
        $this->post('msg/sendBatchAttachMsg.action', array_merge($options, [
            'fromAccid' => $fromAccid,
            'toAccids' => json_encode($toAccidList),
            'attach' => $attach,
        ]));
    }
    
    /**
     * 发送p2p消息
     *
     * @param string $fromAccid 发送者accid
     * @param string $toAccid 用户id
     * @param int $msgType 消息类型
     * @param string $body 消息内容
     * @param array $options 可选参数集合，支持如下：
     *
     * - antispam: bool, 对于对接了易盾反垃圾功能的应用。true或false, 默认false。 只对消息类型为：100 自定义消息类型 的消息生效。
     * - option: string, 指定消息的漫游，存云端历史，发送方多端同步，推送，消息抄送等特殊行为,使用 self::chatOption
     * - pushcontent: string, 推送文案,最长500个字符，android以此为推送显示文案；ios若未填写payload，显示文案以pushcontent为准
     * - payload: sting, ios 推送对应的payload,必须是JSON,不能超过2k字符
     * - ext: string, 开发者扩展字段，长度限制1024字符
     * - forcepushlist: string
     * - forcepushcontent: string, 发送群消息时，针对强推列表forcepushlist中的用户，强制推送的内容
     * - forcepushall: bool, 发送群消息时，强推列表是否为群里除发送者外的所有有效成员，true或false，默认为false
     * - bid: string, 反垃圾业务ID，实现“单条消息配置对应反垃圾”，若不填则使用原来的反垃圾配置
     * - useYidun: int, 单条消息是否使用易盾反垃圾
     * - markRead: int, 群消息是否需要已读业务（仅对群消息有效），0:不需要，1:需要
     * - checkFriend: bool, 是否为好友关系才发送消息，默认false，注：使用该参数需要先开通功能服务
     *
     * @return string 消息id
     */
    public function p2pSend(string $fromAccid, string $toAccid, int $msgType, string $body, array $options = [])
    {
        $r = $this->post('msg/sendMsg.action', array_merge($options, [
            'from' => $fromAccid,
            'ope' => 0,
            'to' => $toAccid,
            'type' => $msgType,
            'body' => $body,
        ]));
        return (string)($r['data']['msgid'] ?? '');
    }
    
    /**
     * 发送p2p消息 - 批量
     *
     * @param string $fromAccid 发送者accid，用户帐号，最大32字符，必须保证一个APP内唯一
     * @param array $toAccidList 接受者数组，限500人
     * @param int $type 消息类型 对应self::MSG_TYPE_*
     * @param string $body 消息内容，最大长度5000字符，JSON格式
     * @param array $options 可选参数，支持如下
     *
     * - option: string, 指定消息的漫游，存云端历史，发送方多端同步，推送，消息抄送等特殊行为,使用 self::chatOption
     * - pushcontent: string, 推送文案,最长500个字符，android以此为推送显示文案；ios若未填写payload，显示文案以pushcontent为准
     * - payload: sting, ios 推送对应的payload,必须是JSON,不能超过2k字符
     * - ext: string, 开发者扩展字段，长度限制1024字符
     * - bid: string, 反垃圾业务ID，实现“单条消息配置对应反垃圾”，若不填则使用原来的反垃圾配置
     * - useYidun: int, 单条消息是否使用易盾反垃圾
     * - returnMsgid bool 是否需要返回消息ID，false：不返回消息ID（默认值），true：返回消息ID（toAccids包含的账号数量不可以超过100个）
     *
     * @throws Exception
     */
    public function p2pSendBatch(string $fromAccid, array $toAccidList, int $type, string $body, array $options = [])
    {
        if (count($toAccidList) > 500) {
            throw new Exception('接收方最多500人');
        }
        
        $r = $this->post('msg/sendBatchMsg.action', array_merge($options, [
            'fromAccid' => $fromAccid,
            'toAccids' => json_encode($toAccidList),
            'type' => $type,
            'body' => $body,
        ]));
        
        return $r['msgids'] ?? [];
    }
    
    /**
     * 所有用户发送广播消息
     *
     * @param string $body 广播消息内容
     * @param array $options 可选参数集合，支持以下选项:
     *
     * - from: string, 发送者accid, 用户帐号，最大长度32字符，必须保证一个APP内唯一
     * - isOffline: bool, 是否存离线，true或false，默认false
     * - ttl: int, 存离线状态下的有效期，单位小时，默认7天
     * - targetOs: string, 目标客户端，默认所有客户端，jsonArray，例如 ["ios","aos","pc","web","mac"]
     */
    public function broadcast(string $body, array $options = [])
    {
        $this->post('msg/broadcastMsg.action', array_merge($options, ['body' => $body]));
    }
    
    /**
     * 加好友
     *
     * @param string $fromAccid 加好友发起者accid
     * @param string $friendAccid 加好友接收者accid
     * @param int $type 1直接加好友，2请求加好友，3同意加好友，4拒绝加好友
     * @param string $msg 加好友对应的请求消息，第三方组装，最长256字符
     * @param string $serverEx 服务器端扩展字段，限制长度256。此字段client端只读，server端读写
     */
    public function friendAdd(string $fromAccid, string $friendAccid, int $type, string $msg = '', string $serverEx = '')
    {
        $this->post('friend/add.action', [
            'accid' => $fromAccid,
            'faccid' => $friendAccid,
            'type' => $type,
            'msg' => $msg,
            'serverex' => $serverEx,
        ]);
    }
    
    /**
     * 更新好友信息
     *
     * @param string $fromAccid 发起者accid
     * @param string $friendAccid 要修改朋友的accid
     * @param string $alias 给好友增加备注名，限制长度128，可设置为空字符串
     * @param string $ex 修改ex字段，限制长度256，可设置为空字符串
     * @param string $serverEx 服务器端扩展字段，限制长度256。此字段client端只读，server端读写
     */
    public function friendUpdate(string $fromAccid, string $friendAccid, string $alias = '', string $ex = '', string $serverEx = '')
    {
        $this->post('friend/update.action', [
            'accid' => $fromAccid,
            'faccid' => $friendAccid,
            'alias' => $alias,
            'ex' => $ex,
            'serverex' => $serverEx,
        ]);
    }
    
    /**
     * 删除好友
     *
     * @param string $accid 发起者accid
     * @param string $friendAccid 要修改朋友的accid
     * @param bool $isDeleteAlias 是否需要删除备注信息，false:不需要，true:需要
     */
    public function friendDelete(string $accid, string $friendAccid, bool $isDeleteAlias = false)
    {
        $this->post('friend/delete.action', ['accid' => $accid, 'faccid' => $friendAccid, 'isDeleteAlias' => $isDeleteAlias]);
    }
    
    /**
     * 获取好友关系列表
     * 查询某时间点起到现在有更新的双向好友
     *
     * @param string $accid 发起者accid
     * @param int $updateTime 更新时间戳，接口返回该时间戳之后有更新的好友列表
     *
     * @return array
     */
    public function friendList(string $accid, int $updateTime):array
    {
        $r = $this->post('friend/get.action', ['accid' => $accid, 'updatetime' => $updateTime]);
        $json = $r['friends'] ?? '';
        return Json::decode($json);
    }
    
    /**
     * 拉黑用户
     *
     * @param string $accid 用户账号
     * @param string $targetAccid 被拉黑的账号
     */
    public function blackUser(string $accid, string $targetAccid)
    {
        $this->post('user/setSpecialRelation.action', [
            'accid' => $accid,
            'targetAcc' => $targetAccid,
            'relationType' => 1,
            'value' => 1,
        ]);
    }
    
    /**
     * 取消拉黑
     *
     * @param string $accid 用户账号
     * @param string $targetAccid 被拉黑的账号
     */
    public function unblackUser(string $accid, string $targetAccid)
    {
        $this->post('user/setSpecialRelation.action', [
            'accid' => $accid,
            'targetAcc' => $targetAccid,
            'relationType' => 1,
            'value' => 0,
        ]);
    }
    
    /**
     * 查看拉黑列表
     *
     * @param string $accid
     *
     * @return array
     */
    public function blackList(string $accid)
    {
        $r = $this->post('user/listBlackAndMuteList.action', ['accid' => $accid]);
        return $r['blacklist'] ?? [];
    }
    
}
