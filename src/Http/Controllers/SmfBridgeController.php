<?PHP

namespace Denngarr\Seat\SmfBridge\Http\Controllers;

use DB;
use Seat\Web\Http\Controllers\Controller;
use Seat\Services\Repositories\Configuration\UserRespository;
use Seat\Web\Models\User;
use Seat\Services\Models\UserSetting;
use Seat\Services\Settings\Profile;
use Seat\Web\Http\Validation\ProfileSettings;
use Seat\Eveapi\Api\Character\CharacterSheet;
use Seat\Services\Repositories\Character\Info;
use Denngarr\Seat\SmfBridge\Models\SmfBridgeUser;

use Laravel\Socialite\Contracts\Factory as Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;

define('SMF', 'API');

class SmfBridgeController extends Controller {

    use UserRespository, Info;

    protected $smfdatabase = array();

    public function __construct() {
                $db = $this->setDatabase();
		\Config::set('database.connections', $db);
	}

    public function setDatabase() {
		$connections = \Config::get('database.connections');
		return array_merge($connections, 
			['smf' => [
				'driver'    => 'mysql',
				'host'      => env('SMF_HOST', 'localhost'),
				'port'      => env('SMF_PORT', '3306'),
				'database'  => env('SMF_DATABASE', ''),
				'username'  => env('SMF_USERNAME', ''),
				'password'  => env('SMF_PASSWORD', ''),
				'charset'   => 'utf8',
				'collation' => 'utf8_unicode_ci',
				'prefix'    => 'smf_',
				'strict'    => true,
				'engine'    => null,
				'modes'     => [
					'STRICT_TRANS_TABLES',
					'NO_ZERO_IN_DATE',
					'NO_ZERO_DATE',
					'ERROR_FOR_DIVISION_BY_ZERO',
					'NO_AUTO_CREATE_USER',
					'NO_ENGINE_SUBSTITUTION',
				]
			]
		]);

	}

	public function SmfGetConfiguration() {
		return view('smfbridge::configuration');
	}

	public function SmfGetUserByEmail($email = '') {

		return $users = DB::connection('smf')->table('members')
			->select()
			->where('email_address', $email)
                        ->get();
	}

	public function SmfGetUserById($id = '') {

		return $users = DB::connection('smf')->table('members')
			->select()
			->where('id_member', $id)
                        ->get();
	}

	public function SmfGetUserByUsername($username = '') {

		return $users = DB::connection('smf')->table('members')
			->select()
			->where('member_name', $username)
                        ->get();
	}

	public static function SmfGenerateSalt($len = 6)
	{
		$chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charslen = strlen($chars);
		$salt = '';

		$randomString = '';
		for ($i = 0; $i < $len; $i++) {
			$salt .= $chars[rand(0, $charslen - 1)];
		}	
		return $salt;
	}

	public function SmfSyncUser($id = '')
	{
		$baseUser = $this->getFullUser($id);
		$main_id = User::find($id)->settings()->where('name', 'main_character_id')->get();
		if ($main_id != null) {
			$main_char = $this->getCharacterSheet($main_id[0]->value);
			$smfUser = new SmfBridgeUser;		

			$smfGroup = DB::connection('smf')->table('membergroups')
				->select('id_group')
				->where('group_name', $main_char->corporationName)
				->get();

			$smfUser->userdata['id_group'] = $smfGroup[0]->id_group;
	                $smfUser->userdata['member_name'] = $main_char->name;
	       	        $smfUser->userdata['real_name'] = $main_char->name;
	       	        $smfUser->userdata['icq'] = $main_char->characterID;
	       	        $smfUser->userdata['email_address'] = $baseUser->email;
	       	        $smfUser->userdata['birthdate'] = $main_char->DoB;
	       	        $smfUser->userdata['personal_text'] = $main_char->corporationName;
	                $smfUser->userdata['avatar'] = 'https://image.eveonline.com/Character/'.$main_char->characterID.'_128.jpg';
	                $smfUser->userdata['is_activated'] = $baseUser->active;
	                $smfUser->userdata['date_registered'] = time();
			$smfUser->userdata['password_salt'] = $this->SmfGenerateSalt();
			$smfUser->userdata['passwd'] = sha1($baseUser->password . $smfUser->userdata['password_salt']);

			DB::connection('smf')->table('members')
				->insert($smfUser->userdata);		
			return 0;
		}
		return 1;
	}

        // Author: Jonathan Sampson
        public function SmfGetDomain($url)
	{
		$pieces = parse_url($url);
		$domain = isset($pieces['host']) ? $pieces['host'] : '';
		if (preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', $domain, $regs)) {
			return $domain;
		}
		return false;
	}

        public function SmfGetSettings() 
	{
		$settings = array();
		$results = DB::connection('smf')->table('settings')
                                ->select()
				->get();
		foreach ($results as $index) {
			$settings[$index->variable] = $index->value;
		}
		return $settings;
	}

	public function SmfGetConfig() {
		$configfile = "/srv/devcc.cripplecreekcorp.com/htdocs/seat-development/public/forum/Settings.php";

		$contents = file($configfile);
		$config = array();

		foreach ($contents as $line) {
		        if (preg_match("/\\$([A-Za-z0-9_-]+)\s*=\s*'(.+)'/", $line, $matches)) {
				$config[$matches[1]] = $matches[2];
		        }
	
		}
		return $config;
	}

        public function SmfUrlParts($local, $global)
	{
		$config = $this->SmfGetConfig();
		$parsed_url = parse_url($config['boardurl']);
		$host = $this->SmfGetDomain($config['boardurl']);

		if (!$local) {
			$path = '';
		} else {
			$path = $parsed_url['path'];
		}
		return array($host, $path . '/');
	}

	public function SmfSetLoginCookie($user_id, $password)
	{
		$cookie_length = 525600;

		$settings = array_merge($this->SmfGetSettings(), $this->SmfGetConfig());

		// the cookie may already exist, and have been set with different options
		$cookie_state = (empty($settings['localCookies']) ? 0 : 1) | (empty($settings['globalCookies']) ? 0 : 2);

		$data = serialize(empty($user_id) ? array(0, '', 0) : array($user_id, $password, time() + $cookie_length, $cookie_state));
		$cookie_url = $this->SmfUrlParts(!empty($settings['localCookies']), !empty($settings['globalCookies']));

		// set the cookie, $_COOKIE, and session variable
	        setcookie($settings['cookiename'], $data, time() + $cookie_length, $cookie_url[1], $cookie_url[0], !empty($settings['secureCookie']));

	        $_COOKIE[$settings['cookiename']] = $data;
		// make sure the user logs in with a new session ID
		if (!isset($_SESSION['login_' . $settings['cookiename']])
		|| $_SESSION['login_' . $settings['cookiename']] !== $data) {
			$_SESSION['login_' . $settings['cookiename']] = $data;
		}

		return true;
	}		
	
	public function SmfAuthenticate($id = 0) {

		if ($id == 0) {
			echo "No id passed.";
			exit;
		}
		$baseUser = $this->getFullUser($id);

		$login = $baseUser->name;
		//$passwd = sha1(strtolower($login) . $baseUser->passwd);
		$passwd = sha1($baseUser->password);
                $salt = $this->SmfGenerateSalt();
                $passwd_salted = sha1($passwd . $salt);
	
		DB::connection('smf')->table('members')
				->where('member_name', $login)
                                ->update(['passwd' => $passwd,
                                          'password_salt' => $salt]);
	
		$smfUserId = DB::connection('smf')->table('members')
                                ->where('member_name', $login)
                                ->select('id_member')->get();

		$this->SmfSetLoginCookie($smfUserId[0]->id_member, $passwd_salted);
		
        }

	public function SmfLogin() {

		$this->SmfAuthenticate(auth()->user()->id);
		DB::connection('smf')->table('log_online')
				->where('session', session_id())
				->delete();

		DB::connection('smf')->table('log_online')->insert([
				'session' => session_id(),
				'log_time' => time(),
				'id_member' => '11',
				'id_spider' => '0',
				'ip' => '2832697721',
				'url' => 'a:1:{s:10:"USER_AGENT";s:114:"Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/61.0.3163.100 Safari/537.36";}']);
		return view('smfbridge::forum');
	}

};

