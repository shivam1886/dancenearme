<?php

Route::get('/', 'HomeController@index')->name('index');
Route::get('/coach-profile', 'HomeController@coachProfile')->name('coach-profile');
Route::get('/dance-category', 'HomeController@danceCategory')->name('dance-category');
Route::get('/gigs-details', 'HomeController@gigsDetails')->name('gigs-details');
Route::get('/join', 'HomeController@join')->name('join');
Route::get('/lesson-cost', 'HomeController@lessionCost')->name('lessons-cost');
Route::get('/services', 'HomeController@services')->name('services');
Route::get('/login', 'HomeController@login')->name('login');
Route::get('/signup', 'HomeController@signup')->name('signup');
Route::get('/signup-step2', 'HomeController@signupStep2')->name('signup-step2');
Route::get('/signup-step3', 'HomeController@signupStep3')->name('signup-step3');
Route::get('/teacher-account', 'HomeController@teacherAccount')->name('teacher-account');
Route::get('/user-profile', 'HomeController@userProfile')->name('user-profile');

?>