<?php

Route::get('/chimp-campaign/{id}', ['as' => 'chimpCampaigns.themplate', 'uses' => 'MailChimpThemplateController@getIndex@index']);