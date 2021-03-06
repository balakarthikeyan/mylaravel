<?php

use Illuminate\Support\Facades\Route;
// use Facades\App\Helpers\Helpers;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

//Authentication Routes
Auth::routes();
Route::get('/home', 'HomeController@index')->name('home');

//Products Routes
Route::resource('products','ProductController');

//Products based on Datatables
Route::get('product-list', 'ProductController@ajaxlist')->name('ajax.products.list');
Route::post('product-list/store', 'ProductController@ajaxstore')->name('ajax.products.store');
Route::get('product-list/edit/{id}', 'ProductController@ajaxedit');
Route::get('product-list/delete/{id}', 'ProductController@ajaxdelete');

//High Charts
Route::get('chart', 'AjaxController@charts');

//Ajax Routes
Route::get('ajax', 'AjaxController@ajaxPage');
Route::post('ajax', 'AjaxController@ajaxPost')->name('ajax.post');

//Upload Image/File Routes
Route::get('image','ImageController@create')->name('image.create');
Route::post('image','ImageController@store')->name('image.store');
Route::post('ajax-image','ImageController@ajax')->name('image.ajax.store');

//Dynamic Dropdown Routes
Route::get('categories','CategoryController@index');
Route::post('categories/{id}','CategoryController@getCategory')->name('subcategories');

Route::get('/encode', function (\Illuminate\Http\Request $request) {
    return response()->json([
        'result' => base64_encode($request->input('value')),
    ]);
});

Route::get('/decode', function (\Illuminate\Http\Request $request) {
    return response()->json([
        'result' => base64_decode($request->input('value')),
    ]);
});

Route::get('/quote', function() {
    // Picks a different quote every day (for a maximum of 366 quotes)
    // - $count: the total number of available quotes
    // - $day: the current day of the year (from 0 to 365)
    // - $page: the page to look for to retrieve the correct record

    // $count 	= Quote::query()->get()->count();
    // $day 	= (int) date('z');
    // $page 	= $day % $count + 1;
    // $quotes = Quote::query()->get()->forPage($page, 1)->all();
	$quotes = App\Models\Quote::query()->get()->all();

    if (empty($quotes)) {
        throw new \Illuminate\Database\Eloquent\ModelNotFoundException();
    }

    return view('pages.quote', ['quote' => $quotes[0]]);
});

//Test Page Routes
Route::get('/test-page', function () {
    return view('pages.index');
})->name('test-page');
Route::post('/test-form','TestController@testform')->name('test-form'); 
Route::get('/test-routes','TestController@saveroutes'); 
Route::get('/test-component', function () {
    return view('pages.component');
});
Route::get('/test-middleware', 'TestController@index')->middleware('test.middleware');
Route::get('/test-event', 'TestController@index')->name('test.event');
Route::get('/test-mail-markdown', 'TestController@testmail')->name('test.markdown');
Route::get('/test-mail', 'TestController@sendmail')->name('test.mail');
Route::get('/test-notify', 'TestController@testnotify')->name('test.notify');

//oauth Routes
Route::get('auth/social', 'Auth\LoginController@show')->name('social.login');
Route::get('oauth/{driver}', 'Auth\LoginController@redirectToProvider')->name('social.oauth');
Route::get('oauth/{driver}/callback', 'Auth\LoginController@handleProviderCallback')->name('social.callback');

//Cache Routes
Route::get('test-users', function(){
    return \Facades\App\Repository\Users::all('name');
});
Route::get('cache-users', function () {
    return \Illuminate\Support\Facades\Cache::get('users');
});

//Custom Cache with Events
Route::get('posts', 'PostController@index');

//Custom Macro & Helper
Route::get('test-macro', function(){
    Helpers::debug_variable_helper(\Illuminate\Support\Str::isLength('This is a Laravel Macro', 23));
    Helpers::debug_variable_helper(Helpers::app_name());
});

Route::get('test-queries', function (\Illuminate\Http\Request $request) {
    $response = DB::table('users')->select('name', 'email')->where('id', $request->input('id'))->get();
    // $response = \App\User::select('name', 'email')->where('id', $request->input('id'))->orderBy('name')->get();
    // $response = \App\User::where('active', 1)->first();
    // $response = \App\Permission::where('slug','create')->first();   
    return response()->json([
        'result' => $response,
    ]);
});

Route::group(['middleware' => 'role:developer'], function() {
    Route::get('roles', 'PermissionController@index'); 
}); 

Route::get('notify-read', function(){
    foreach(auth()->user()->unreadNotifications as $notification) {
        $notification->markAsRead();                                 
    }
    return redirect()->back();
});

Route::get('notify-delete', function(){
    foreach(auth()->user()->unreadNotifications as $notification) {
        $notification->delete();                                 
    }
    return redirect()->back();
});

Route::prefix('/admin')->name('admin.')->namespace('Auth')->group(function(){
    //Login Routes
    Route::get('/login','AdminController@showLoginForm')->name('login');
    Route::post('/login','AdminController@login')->name('login.submit');
    Route::post('/logout','AdminController@logout')->name('logout');

    //Forgot Password Routes
    Route::get('/password/reset','ForgotPasswordController@showLinkRequestForm')->name('password.request');
    Route::post('/password/email','ForgotPasswordController@sendResetLinkEmail')->name('password.email');

    //Reset Password Routes
    Route::get('/password/reset/{token}','ResetPasswordController@showResetForm')->name('password.reset');
    Route::post('/password/reset','ResetPasswordController@reset')->name('password.update');

    //Pages
    Route::get('/','AdminController@index')->name('dashboard')->middleware('auth:admin');    
});

Route::get('test-queue', 'TestController@processQueue');

Route::get('/search-users', 'TestController@searchindex')->name('users.index');
Route::post('/search-users', 'TestController@searchusers')->name('users.search');
Route::get('/search-profile/{id}', 'ProfileController');

// Unhandled API Routes – Fallback Method
Route::fallback(function(){
    return response()->json([
        'message' => 'Page Not Found. If error persists, contact info@website.com'], 404);
});


Route::get('/test-s3', 'TestController@tests3list');
// Route::resource('images', 'TestController', ['only' => ['tests3store', 'tests3destroy']]);
Route::post('/test-fileupload','TestController@tests3store');