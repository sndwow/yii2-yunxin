<?php

namespace sndwow\yunxin;

/**
 * 消息格式
 */
class MsgFormat
{
    const TYPE_TEXT = 0; // 文本类型
    const TYPE_IMAGE = 1; // 图片消息
    const TYPE_VOICE = 2; // 语音消息
    const TYPE_VIDEO = 3; // 视频消息
    const TYPE_LOCATION = 4; // 地理位置消息
    const TYPE_FILE = 6; // 文件消息
    const TYPE_CUSTOM = 100; // 自定义消息
    
    /**
     * 生成文本消息
     * @param string $msg
     *
     * @return string
     */
    public static function text(string $msg)
    {
        return json_encode(['msg' => $msg]);
    }
    
    /**
     * 图片消息
     *
     * @param string $name
     * @param string $md5
     * @param string $url
     * @param string $ext
     * @param int $w
     * @param int $h
     * @param int $size
     *
     * @return string
     */
    public static function image(string $name, string $md5, string $url, string $ext, int $w, int $h, int $size)
    {
        return json_encode([
            'name' => $name,
            'md5' => $md5,
            'url' => $url,
            'ext' => $ext,
            'w' => $w,
            'h' => $h,
            'size' => $size,
        ]);
    }
    
    /**
     * 语音消息
     *
     * @param int $dur 时长ms
     * @param string $md5 播放地址
     * @param string $url
     * @param int $size 文件大小
     *
     * @return string
     */
    public static function voice(int $dur, string $md5, string $url, int $size)
    {
        return json_encode([
            'dur' => $dur,
            'md5' => $md5,
            'url' => $url,
            'ext' => 'acc', // 语音消息格式，只能是aac格式
            'size' => $size,
        ]);
    }
    
    /**
     * 视频消息
     *
     * @param int $dur 视频持续时长ms
     * @param string $md5
     * @param string $url 播放地址
     * @param int $w 宽
     * @param int $h 高
     * @param string $ext 后缀名
     * @param int $size 文件大小
     *
     * @return string
     */
    public static function video(int $dur, string $md5, string $url, int $w, int $h, string $ext, int $size)
    {
        return json_encode([
            'dur' => $dur,
            'md5' => $md5,
            'url' => $url,
            'w' => $w,
            'h' => $h,
            'ext' => $ext,
            'size' => $size,
        ]);
    }
    
    /**
     * 地理位置消息
     *
     * @param string $title 地理位置说明，例如：中国 浙江省 杭州市 网商路 599号
     * @param string $lng 经度，例如 120.1908686708565
     * @param string $lat 纬度，30.18704515647036
     *
     * @return string
     */
    public static function location(string $title, string $lng, string $lat)
    {
        return json_encode(['title' => $title, 'lng' => $lng, 'lat' => $lat]);
    }
    
    /**
     * 文件消息
     *
     * @param string $name 文件名
     * @param string $md5
     * @param string $url 地址
     * @param string $ext 格式，例如ttf
     * @param int $size 文件大小
     *
     * @return string
     */
    public static function file(string $name, string $md5, string $url, string $ext, int $size)
    {
        return json_encode(['name' => $name, 'md5' => $md5, 'url' => $url, 'ext' => $ext, 'size' => $size]);
    }
}
