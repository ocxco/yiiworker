<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "user".
 *
 * @property int $id
 * @property int $type
 * @property string $username
 * @property string $password
 * @property string $mobile
 * @property string $avatar
 * @property string $avatar_64 小号头像(64*64)
 * @property string $avatar_256 大号头像(256*256)
 * @property int $is_email_verified email是否已经验证(0为未验证，1为已验证)
 * @property int $is_mobile_verified 手机号码是否已验证
 * @property int $is_confirm_mobile 是否已确认过手机号码 0 否 1是,用户中心跟购商宝整合需求时添加
 * @property string $mobile_verified_time 手机号码验证时间
 * @property string $mobile_send_time
 * @property int $mobile_send_count 短信发送条数
 * @property string $last_login_time 上次登录时间
 * @property string $last_login_ip 上次登录IP
 * @property int $is_auto_wireless 是否自动生成无线详情全局配置 （1：是  0：否）
 * @property int $state
 * @property string $create_time
 * @property string $create_ip 注册时的IP
 * @property string $update_time
 * @property string $last_session
 * @property string $last_session_time
 * @property string $email 供应商存放邮箱
 * @property int $new_encrypted 是否采用go2加密方式,0否 1是
 * @property string $source 平台
 * @property int $complete_info 资料是否完善
 * @property string $mobile_addr 手机号归属地
 * @property int $create_type 注册类型：1 用户注册，2运营后台注册
 * @property string $reg_device 注册终端类型(pc/android/ios)
 */
class User extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['type', 'is_email_verified', 'is_mobile_verified', 'is_confirm_mobile', 'mobile_send_count', 'is_auto_wireless', 'state', 'new_encrypted', 'complete_info', 'create_type'], 'integer'],
            [['mobile_verified_time', 'mobile_send_time', 'last_login_time', 'create_time', 'update_time', 'last_session_time'], 'safe'],
            [['username', 'mobile', 'mobile_addr'], 'string', 'max' => 50],
            [['password', 'last_session'], 'string', 'max' => 32],
            [['avatar'], 'string', 'max' => 512],
            [['avatar_64', 'avatar_256'], 'string', 'max' => 128],
            [['last_login_ip', 'create_ip'], 'string', 'max' => 15],
            [['email'], 'string', 'max' => 200],
            [['source'], 'string', 'max' => 45],
            [['reg_device'], 'string', 'max' => 16],
            [['username', 'source'], 'unique', 'targetAttribute' => ['username', 'source']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type' => 'Type',
            'username' => 'Username',
            'password' => 'Password',
            'mobile' => 'Mobile',
            'avatar' => 'Avatar',
            'avatar_64' => 'Avatar 64',
            'avatar_256' => 'Avatar 256',
            'is_email_verified' => 'Is Email Verified',
            'is_mobile_verified' => 'Is Mobile Verified',
            'is_confirm_mobile' => 'Is Confirm Mobile',
            'mobile_verified_time' => 'Mobile Verified Time',
            'mobile_send_time' => 'Mobile Send Time',
            'mobile_send_count' => 'Mobile Send Count',
            'last_login_time' => 'Last Login Time',
            'last_login_ip' => 'Last Login Ip',
            'is_auto_wireless' => 'Is Auto Wireless',
            'state' => 'State',
            'create_time' => 'Create Time',
            'create_ip' => 'Create Ip',
            'update_time' => 'Update Time',
            'last_session' => 'Last Session',
            'last_session_time' => 'Last Session Time',
            'email' => 'Email',
            'new_encrypted' => 'New Encrypted',
            'source' => 'Source',
            'complete_info' => 'Complete Info',
            'mobile_addr' => 'Mobile Addr',
            'create_type' => 'Create Type',
            'reg_device' => 'Reg Device',
        ];
    }
}
