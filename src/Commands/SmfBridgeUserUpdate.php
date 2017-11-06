<?php
/**
 * User: Warlof Tutsimo <loic.leuilliot@gmail.com>
 * Date: 17/06/2016
 * Time: 18:51
 */

namespace Denngarr\Seat\SmfBridge\Commands;

use DB;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;
use Seat\Services\Repositories\Configuration\UserRespository;
use Seat\Web\Models\User;
use Seat\Services\Models\UserSetting;
use Seat\Services\Settings\Profile;
use Seat\Web\Http\Validation\ProfileSettings;
use Seat\Eveapi\Api\Character\CharacterSheet;
use Seat\Services\Repositories\Character\Info;
use Denngarr\Seat\SmfBridge\Models\SmfBridgeUser;
use Denngarr\Seat\SmfBridge\Http\Controllers\SmfBridgeController;

class SmfBridgeUserUpdate extends Command
{
    use UserRespository, Info;

    protected $signature = 'smfbridge:users:update';

    protected $description = 'Sync users from Seat to Simple Machines Forum';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $smfUsers = DB::connection('smf')->table('members')
        	->select(['id_member','member_name'])
		->get();

        $seatUsers = DB::connection()->table('users')
               ->select(['id','name'])
               ->get();

        $users = array();

         //  Sync Users in SeAT to SMF
        foreach ($smfUsers as $user) 
	{
               array_push($users, ['id' => $user->id_member, 'name' => $user->member_name]);
        }

        foreach ($seatUsers as $seatUser) 
	{
		if ($seatUser->id > 0) {
			$fullUser = $this->getFullUser($seatUser->id);
			if ($fullUser->active) {
				$main_id = User::find($seatUser->id)->settings()->where('name', 'main_character_id')->get();
				if (isset($main_id[0])) {
					$main_char = $this->getCharacterSheet($main_id[0]->value);
			                if ($main_char != null) {
						$seatUser->name = $main_char->name;
				        	if ((($index = array_search($main_char->name, array_column($users, 'name'))) == null) 
							&& ($main_char->name != "admin")) {
							$this->SmfSyncUser($seatUser->id);
               					}
					}
				}
			}
		}
        }

	// Disable any users NOT in Seat outside of 'admin'
        $smfUsers = DB::connection('smf')->table('members')
                ->select(['id_member','member_name'])
                ->get();

        $seatUsers = DB::connection()->table('users')
                ->select(['id','name'])
                ->get();


	foreach ($seatUsers as $user) 
	{
		if ($user->id > 0) {
			$fullUser = $this->getFullUser($user->id);
			if ($fullUser->active) {
				$main_id = User::find($user->id)->settings()->where('name', 'main_character_id')->get();
				if (isset($main_id[0])) {
					$main_char = $this->getCharacterSheet($main_id[0]->value);
			                if ($main_char != null) {
						$user->name = $main_char->name;
					}
				}
			}
		}
		array_push($users, ['id' => $user->id, 'name' => $user->name]);
        }
	foreach ($smfUsers as $user)
	{
		if ((array_search($user->member_name, array_column($users, 'name')) == null)
			&& ($user->member_name != 'admin'))
		{
			$smfUsers = DB::connection('smf')->table('members')
				->where('member_name', $user->member_name)
				->update(['is_activated' => 0]);
		}
	}
    }

    public function SmfSyncUser($id = '')
        {
                $baseUser = $this->getFullUser($id);
                $main_id = User::find($id)->settings()->where('name', 'main_character_id')->get();
                if ((count($main_id) > 0)) {

                        $main_char = $this->getCharacterSheet($main_id[0]->value);
                        $smfUser = new SmfBridgeUser;

                        $smfGroup = DB::connection('smf')->table('membergroups')
                                ->select('id_group')
                                ->where('group_name', $main_char->corporationName)
                                ->get();
			
			if (!isset($smfGroup[0])) 
				return 1;
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
                        $smfUser->userdata['password_salt'] = SmfBridgeController::SmfGenerateSalt();
                        $smfUser->userdata['passwd'] = sha1($baseUser->password . $smfUser->userdata['password_salt']);

                        DB::connection('smf')->table('members')
                                ->insert($smfUser->userdata);
                        return 0;
                }
                return 1;
        }

}
