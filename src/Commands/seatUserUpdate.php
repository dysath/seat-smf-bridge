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
use Seat\Web\Models\Acl\Role;
use Seat\Web\Acl\AccessManager;
use Seat\Eveapi\Models\Character\CharacterSheet;

class seatUserUpdate extends Command
{
    use AccessManager;

    protected $signature = 'seat-ext:users:update';

    protected $description = 'Sync users with Seat Roles';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {

        $userList = User::all();
	$roleList = Role::all();
	
	foreach ($userList as $user) {
            $corpList = [];
            $keys = $user->keys;
            $roles = $user->roles; 

            foreach ($keys as $key) {
		$characters = $key->characters;
                foreach ($characters as $character) {
                    $corpList[$character->corporationName] = true;

                    $charSheet = CharacterSheet::find($character->characterID);
                    $titles = $charSheet->corporation_titles;


                    if (count($titles) > 0) {
                        $corpList[strip_tags($titles[0]->titleName)] = true;
                    }
                }
            }
            foreach ($roles as $role) {
                if (isset($corpList[$role->title])) {
                    unset($corpList[$role->title]);
                }
            }
            if (count($corpList) > 0) {
                foreach ($corpList as $corp => $val) {
                   $role = Role::where('title', $corp)->first();
		   if (isset($role->title)) {
                       echo "I, $user->name, need to be assigned the role for: $role->title\n";
                       $this->giveUsernamesRole([$user->name], $role->id);
                   }
                }
            }
        }
    }
}
