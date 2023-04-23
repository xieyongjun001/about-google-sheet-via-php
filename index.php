<?php
require_once 'vendor/autoload.php';
// configure the Google Client
$client = new \Google_Client();
$client->setApplicationName('google docs op');
$client->setScopes([\Google_Service_Sheets::SPREADSHEETS]);
$client->setAccessType('offline');
// credentials.json is the key file we downloaded while setting up our Google Sheets API
$path = '*********1.json';
$client->setAuthConfig($path);

// configure the Sheets Service
$service = new \Google_Service_Sheets($client);


// the spreadsheet id can be found in the url https://docs.google.com/spreadsheets/d/***************2/edit
$spreadsheetId = '*********************3';
$spreadsheet = $service->spreadsheets->get($spreadsheetId);
// var_dump($spreadsheet);

$range = 'phonecall'; // here we use the name of the Sheet to get all the rows
$response = $service->spreadsheets_values->get($spreadsheetId, $range);
$values = $response->getValues();
var_dump($values);
