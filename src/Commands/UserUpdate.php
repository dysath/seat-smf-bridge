<?php

namespace Denngarr\Seat\SmfBridge\Commands;

use DB;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;
use Seat\Services\Repositories\Configuration\UserRespository;
use Seat\Web\Models\User;
use Seat\Web\Models\Acl\Role;
use Seat\Services\Models\UserSetting;
use Seat\Eveapi\Api\Character\CharacterSheet;
use Seat\Services\Repositories\Character\Info;
use Denngarr\Seat\SmfBridge\Models\Members;
use Denngarr\Seat\SmfBridge\Models\MemberGroups;
use Denngarr\Seat\SmfBridge\Http\Controllers\SmfBridgeController;

class UserUpdate extends Command
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

        $smfGroups = [];
        $smfRoles = [];
        $seatRoles = [];
        $smfUsers = Members::all();
        $seatUsers = User::all();

        foreach ($seatUsers as $seatUser)
        {
            if ($seatUser->name != "admin") {
echo "Working on " . $seatUser->name . "... \n";

                $user_settings = User::find($seatUser->id)->settings()->get();

                if (!empty($user_settings->where('name', 'main_character_id')->first())) {

                    $main_character_id = $user_settings->where('name', 'main_character_id')->first()->value;
                    $main_character_name = $user_settings->where('name', 'main_character_name')->first()->value;

                    $smf_member = Members::where('member_name', $main_character_name)->first();
                    if (empty($smf_member)) {
                        if ($seatUser->active === 1) {
                            echo "Creating user: $main_character_name\n";
                            $smf_new_member = $this->createMember($seatUser, $main_character_id, $main_character_name);
                            if (is_object($smf_new_member)) {
                                $smf_new_member->additional_groups = $this->getGroups($smf_new_member, $seatUser);
                                $smf_new_member->save();
                            }
                        }
                    }
                    else {
                        if ($seatUser->account_status === 1) {
                                $smf_member->is_activated = '1';
                                $smf_member->additional_groups = $this->getGroups($smf_member, $seatUser);
                                $smf_member->save();
                        }
                        else {
                                $smf_member->is_activated = '0';
                                $smf_member->save();
                        }
                    }
                }
            }
        }
    }

    private function createMember($user, $main_character_id, $main_character_name)
    {

        if ((!$main_character_id) || (!$main_character_name)) {
            echo "Can't create SMF Account for " . $user->name . " as there is no Main Character selected.\n";
            return;
        }

        $main_char = $this->getCharacterSheet($main_character_id);
        if (empty($main_char)) {
          echo "Ignoring " . $user->name . ": No Main Character selected or APIs installed.\n";
          return;
        }
        $smfUser = new Members;
        $smfGroup = MemberGroups::where('group_name', $main_char->corporationName)->first();

        if (empty($smfGroup)) {
           return '';
        }
        $smfUser->id_group = $smfGroup->id_group;
        $smfUser->member_name = $main_char->name;
        $smfUser->real_name = $main_char->name;
        $smfUser->icq = $main_char->characterID;
        $smfUser->email_address = $user->email;
        $smfUser->birthdate = $main_char->DoB;
        $smfUser->personal_text = $main_char->corporationName;
        $smfUser->avatar = 'https://image.eveonline.com/Character/'.$main_char->characterID.'_128.jpg';
        $smfUser->is_activated = '1';
        $smfUser->date_registered = time();
        $smfUser->password_salt = "84g9";

        return $smfUser;
    }

    private function getGroups($smf_member, $seatUser)
    {

        $seatRoles = [];
        $smfRoles = [];

        //
        // $smfRoles equals All Assigned SMF MemberGroups
        // $primary_group equals The Main_Character_ID's role(corp_id)
        //
        $primary_group = $smf_member->id_group;

        if (!empty($smf_member->additional_groups)) {
            $smfGroups = explode(',', $smf_member->additional_groups);
        }
        else {
            $smfGroups = [];
        }
        array_push($smfGroups, $primary_group);
        foreach ($smfGroups as $group) {
            $membergroup = MemberGroups::find($group);
            if (!empty($membergroup)) {
                $smfRoles[$membergroup->group_name] = $membergroup->id_group;
            }
        }
        $user_roles = User::find($seatUser->id)->roles()->get();
        foreach($user_roles as $role) {
            $seatRoles[$role->title] = $role->id;
        }
        if (!empty(array_diff_key($seatRoles, $smfRoles))) {
            foreach ($seatRoles as $role => $id) {
                if (!isset($smfRoles[$role])) {
                    $member_group = MemberGroups::where('group_name', $role)->first();
                    if (!empty($member_group)) {
                        $smfRoles[$member_group->group_name] = $member_group->id_group;
                    }
                }
            }
            return implode(',', array_values($smfRoles));
        }
        return $smf_member->additional_groups;
    }
}

