<?php

namespace Denngarr\Seat\SmfBridge\Models;

use Illuminate\Database\Eloquent\Model;

class MemberGroups extends Model
{
	protected $connection = 'smf';

	protected $table = 'membergroups';

        protected $primaryKey = 'id_group';

        public $timestamps = false;

	protected $fillable = [ 'id_group','group_name','description','online_color','min_posts','max_messages','stars','group_type','hidden','id_parent' ];

}

