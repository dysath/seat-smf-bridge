<?PHP

Route::group([
    'namespace' => 'Denngarr\Seat\SmfBridge\Http\Controllers',
    'prefix' => 'smfbridge'
], function () {

    Route::group([
        'middleware' => 'web'
    ], function () {

	Route::get('/login', [
		'as'   => 'smfbridge.login',
		'uses' => 'SmfBridgeController@SmfLogin',
	]);
    });
});
