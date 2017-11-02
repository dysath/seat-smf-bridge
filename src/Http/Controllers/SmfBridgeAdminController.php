<?PHP

namespace Denngarr\Seat\SmfBridge\Http\Controllers;

// require_once('smf_2_api.php');
use Seat\Web\Http\Controllers\Controller;

class SmfBridgeAdminController extends Controller {

	public function getConfiguration() {
		return view('smfbridge::configuration');
	}

};

