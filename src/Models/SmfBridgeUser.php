<?php

namespace Denngarr\Seat\SmfBridge\Models;

use Illuminate\Database\Eloquent\Model;
use Seat\Web\Models\User;

class SmfBridgeUser extends Model
{
	protected $connection = 'smf';

	protected $table = 'members';

//	protected $fillable = [
//		'id_member','member_name','date_registered','posts','id_group','lngfile','last_login','real_name','instant_messages',
//		'unread_messages','new_pm','buddy_list','pm_ignore_list','pm_prefs','mod_prefs','message_labels','passwd','openid_uri',
//		'email_address','personal_text','gender','birthdate','website_title','website_url','location','icq','aim','yim','msn',
//		'hide_email','show_online','time_format','signature','time_offset','avatar','pm_email_notify','karma_bad','karma_good',
//		'usertitle','notify_announcements','notify_regularity','notify_send_body','notify_types','member_ip','member_ip2',
//		'secret_question','secret_answer','id_theme','is_activated','validation_code','id_msg_last_visit','additional_groups',
//		'smiley_set','id_post_group','total_time_logged_in','password_salt','ignore_boards','warning','passwd_flood','pm_receive_from' ];

	public $userdata = [
		'id_member' => null,
		'member_name' => '',
		'date_registered' => 0,
		'posts' => 0,
		'id_group' => 0,
		'lngfile' => '',
		'last_login' => 0,
		'real_name' => '',
		'instant_messages' => 0,
		'unread_messages' => 0,
		'new_pm' => 0,
		'buddy_list' => '',
		'pm_ignore_list' => '',
		'pm_prefs' => 0,
		'mod_prefs' => 0,
		'message_labels' => '',
		'passwd' => '',
		'openid_uri' => '',
		'email_address' => '',
		'personal_text' => '',
		'gender' => 0,
		'birthdate' => 0,
		'website_title' => '',
		'website_url' => '',
		'location' => '',
		'icq' => '',
		'aim' => '',
		'yim' => '',
		'msn' => '',
		'hide_email' => 1,
		'show_online' => 1,
		'time_format' => '',
		'signature' => '',
		'time_offset' => 0.0,
		'avatar' => '',
		'pm_email_notify' => 0,
		'karma_bad' => 0,
		'karma_good' => 0,
		'usertitle' => '',
		'notify_announcements' => 1,
		'notify_regularity' => 1,
		'notify_send_body' => 0,
		'notify_types' => 0,
		'member_ip' => 0,
		'member_ip2' => 0,
		'secret_question' => '',
		'secret_answer' => '',
		'id_theme' => 0,
		'is_activated' => 0,
		'validation_code' => '',
		'id_msg_last_visit' => 0,
		'additional_groups' => '',
		'smiley_set' => '',
		'id_post_group' => 0,
		'total_time_logged_in' => 0,
		'password_salt' => '',
		'ignore_boards' => '',
		'warning' => 0,
		'passwd_flood' => '',
		'pm_receive_from' => 0
	];
}

