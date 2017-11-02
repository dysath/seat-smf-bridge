<?PHP

Route::group([
    'namespace' => 'Denngarr\Seat\SmfBridge\Http\Controllers',
    'prefix' => 'smfbridge'
], function () {

    Route::group([
        'middleware' => 'web'
    ], function () {

        Route::get('/configuration', [
            'as'   => 'smfbridge.configuration',
            'uses' => 'SmfBridgeAdminController@SmfGetConfiguration', 
        ]);
        Route::get('/syncuser', [
            'as'   => 'smfbridge.syncusers',
            'uses' => 'SmfBridgeController@SmfSyncUsers', 
        ]);
	Route::get('/auth/login', [
		'as'   => 'smfbridge.login',
		'uses' => 'SmfBridgeController@SmfLogin',
	]);
	Route::get('/test', [
		'as'   => 'smfbridge.test',
		'uses' => 'SmfBridgeController@SmfTest',
	]);
    });
});
