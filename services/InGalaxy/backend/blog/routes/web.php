<?php

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

Route::get('/', 'Main@indexPage')->name('index');
Route::post('/', 'Auth\LoginController@login');


Route::post('logout', 'Auth\LoginController@logout')->name('logout');
Route::get('/register', 'Auth\RegisterController@showRegistrationForm')->name('register');
Route::post('/register', 'Auth\RegisterController@register');

Route::get('/news', 'HomeController@news')->name('news');

Route::get('/profile/{id}', 'HomeController@profile')->name('profile');
Route::post('/profile/{id}', 'HomeController@addPost')->name('addPost');

Route::get('/friends', 'HomeController@friends')->name('friends');
Route::post('/addFriend', 'HomeController@addFriend')->name('addFriend');
Route::post('/removeFriend', 'HomeController@removeFriend')->name('removeFriend');

Route::get('/messages/{token}', 'HomeController@messages')->name('messages');
Route::get('/messages/', 'HomeController@messages')->name('messages');

Route::post('/addMessage','HomeController@addMessage')->name('addMessage');

Route::get('/account','HomeController@account')->name('account');
Route::post('/account','HomeController@editProfile')->name('editProfile');
Route::post('/changePasswd','HomeController@changePasswd')->name('changePasswd');

Route::get('/users','HomeController@users')->name('users');