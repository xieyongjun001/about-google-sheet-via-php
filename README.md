# about-google-sheet-via-php
using google api for php develop an application 

here is the ref:
https://www.nidup.io/blog/manipulate-google-sheets-in-php-with-api

Important:
1. create a service account and add this account as one of the google doc editor which you want to read or write by your application
2. download credentials.json file from "service account" -> key

Google Sheets is a very used online spreadsheets system allowing real-time collaboration on the data. Let's learn how to manipulate Google Sheets using the PHP API Client.

The whole code and examples are available in a GitHub repository; it comes packaged as a simple Symfony project and provides a Docker image.

Create a Google Project and Configure Sheets API
First, let's configure a new Google Console project to enable Sheets API.

Open the Google Cloud Console and create a new project:

Google Cloud Console, new project

Click on Enable APIs and Service, search for Sheets API, enable the service:

Enable Google Sheets API

Once enabled, we can configure the service:

Google Sheets API is enabled

We now create credentials, in this tutorial, we'll use Application data access:

Google Sheets API, create credentials

We need to create a service account (the generated email will be useful later 💡):

Google Sheets API, create service account

We edit the service account to create a new key, using the json type:

Google Sheets API, create new key for service account

We can now download the key file in local and rename it to credentials.json.

The bit tedious configuration part is over!

Create and Share a Google Sheets
We can create a Google Sheets document. In this tutorial, I use a collection of movies:

Create a new Google Sheets

A spreadsheet can contain several sheets; this one has a single sheet named Sheet1.

We have to share this document with the previously generated service account email (we set Editor permissions to allow the update of the sheets):

Share the Google Sheets document with the service account

Install and Configure the PHP Client
Let's install the Google PHP API client:

composer require google/apiclient

The client is configured with the credentials.json:

// configure the Google Client
$client = new \Google_Client();
$client->setApplicationName('Google Sheets API');
$client->setScopes([\Google_Service_Sheets::SPREADSHEETS]);
$client->setAccessType('offline');
// credentials.json is the key file we downloaded while setting up our Google Sheets API
$path = 'data/credentials.json';
$client->setAuthConfig($path);

// configure the Sheets Service
$service = new \Google_Service_Sheets($client);


Get the Spreadsheet
We use the Sheets service to retrieve the Spreadsheet object:

// the spreadsheet id can be found in the url https://docs.google.com/spreadsheets/d/143xVs9lPopFSF4eJQWloDYAndMor/edit
$spreadsheetId = '143xVs9lPopFSF4eJQWloDYAndMor';
$spreadsheet = $service->spreadsheets->get($spreadsheetId);
var_dump($spreadsheet);


Fetch All the Rows of a Sheet
We read all the rows of a given sheet:

// get all the rows of a sheet
$range = 'Sheet1'; // here we use the name of the Sheet to get all the rows
$response = $service->spreadsheets_values->get($spreadsheetId, $range);
$values = $response->getValues();
var_dump($values);

The result:

array(10) {
  [0]=>
  array(6) {
    [0]=>
    string(2) "id"
    [1]=>
    string(5) "title"
    [2]=>
    string(6) "poster"
    [3]=>
    string(8) "overview"
    [4]=>
    string(12) "release_date"
    [5]=>
    string(6) "genres"
  }
  [1]=>
  array(6) {
    [0]=>
    string(6) "287947"
    [1]=>
    string(7) "Shazam!"
    [2]=>
    string(63) "https://image.tmdb.org/t/p/w500/xnopI5Xtky18MPhK40cZAGAOVeV.jpg"
    [3]=>
    string(98) "A boy is given the ability to become an adult superhero in times of need with a single magic word."
    [4]=>
    string(10) "1553299200"
    [5]=>
    string(23) "Action, Comedy, Fantasy"
  }
  ... more lines
}


Fetch a Few Rows by Using a Range
We read the ten first lines of our Google Sheets:

// we define here the expected range, columns from A to F and lines from 1 to 10
$range = 'Sheet1!A1:F10';
$response = $service->spreadsheets_values->get($spreadsheetId, $range);
$values = $response->getValues();
var_dump($values);


Fetch Only Cells of a Given Column
We read the cells of a given column to avoid fetching everything:

$range = 'Sheet1!B1:B21'; // the column containing the movie title
$response = $service->spreadsheets_values->get($spreadsheetId, $range);
$values = $response->getValues();
var_dump($values);

Convert Rows into JSON Objects
In many cases, it's easier to manipulate each row as an independent object. Let's transform each row into an associative array.

// Fetch the rows
$range = 'Sheet1';
$response = $service->spreadsheets_values->get($spreadsheetId, $range);
$rows = $response->getValues();
// Remove the first one that contains headers
$headers = array_shift($rows);
// Combine the headers with each following row
$array = [];
foreach ($rows as $row) {
    $array[] = array_combine($headers, $row);
}
var_dump($array);

The result is:

array(21) {
  [0]=>
  array(6) {
    ["id"]=>
    string(6) "287947"
    ["title"]=>
    string(7) "Shazam!"
    ["poster"]=>
    string(63) "https://image.tmdb.org/t/p/w500/xnopI5Xtky18MPhK40cZAGAOVeV.jpg"
    ["overview"]=>
    string(98) "A boy is given the ability to become an adult superhero in times of need with a single magic word."
    ["release_date"]=>
    string(10) "1553299200"
    ["genres"]=>
    string(23) "Action, Comedy, Fantasy"
  }
  [1]=>
  array(6) {
    ["id"]=>
    string(6) "299537"
    ["title"]=>
    string(14) "Captain Marvel"
    ["poster"]=>
    string(63) "https://image.tmdb.org/t/p/w500/AtsgWhDnHTq68L0lLsUrCnM7TjG.jpg"
    ["overview"]=>
    string(307) "The story follows Carol Danvers as she becomes one of the universe’s most powerful heroes when Earth is caught in the middle of a galactic war between two alien races. Set in the 1990s, Captain Marvel is an all-new adventure from a previously unseen period in the history of the Marvel Cinematic Universe."
    ["release_date"]=>
    string(10) "1551830400"
    ["genres"]=>
    string(34) "Action, Adventure, Science Fiction"
  }
  // ... more rows
}


This new structure allows us to manipulate each row as a specific JSON object, applying transformations or streaming its processing.

We can also convert it into a JSON string with a single line of code.

$jsonString = json_encode($array, JSON_PRETTY_PRINT);
print($jsonString);

[
    {
        "id": "287947",
        "title": "Shazam!",
        "poster": "https:\/\/image.tmdb.org\/t\/p\/w500\/xnopI5Xtky18MPhK40cZAGAOVeV.jpg",
        "overview": "A boy is given the ability to become an adult superhero in times of need with a single magic word.",
        "release_date": "1553299200",
        "genres": "Action, Comedy, Fantasy"
    },
    {
        "id": "299537",
        "title": "Captain Marvel",
        "poster": "https:\/\/image.tmdb.org\/t\/p\/w500\/AtsgWhDnHTq68L0lLsUrCnM7TjG.jpg",
        "overview": "The story follows Carol Danvers as she becomes one of the universe\u2019s most powerful heroes when Earth is caught in the middle of a galactic war between two alien races. Set in the 1990s, Captain Marvel is an all-new adventure from a previously unseen period in the history of the Marvel Cinematic Universe.",
        "release_date": "1551830400",
        "genres": "Action, Adventure, Science Fiction"
    },
...


Append a New Row
We write a new row at the end of the sheet:

$newRow = [
    '456740',
    'Hellboy',
    'https://image.tmdb.org/t/p/w500/bk8LyaMqUtaQ9hUShuvFznQYQKR.jpg',
    "Hellboy comes to England, where he must defeat Nimue, Merlin's consort and the Blood Queen. But their battle will bring about the end of the world, a fate he desperately tries to turn away.",
    '1554944400',
    'Fantasy, Action'
];
$rows = [$newRow]; // you can append several rows at once
$valueRange = new \Google_Service_Sheets_ValueRange();
$valueRange->setValues($rows);
$range = 'Sheet1'; // the service will detect the last row of this sheet
$options = ['valueInputOption' => 'USER_ENTERED'];
$service->spreadsheets_values->append($spreadsheetId, $range, $valueRange, $options);


When using USER_ENTERED as valueInputOption the service will parse the data the same way as when you're typing it directly into the Google Sheets UI. It means the strings will be converted into dates, links, etc, depending on their formats.

We can also use the RAW input option to keep the data unchanged.

Update an Existing Row
We replace an existing row by new values for its cells:

$updateRow = [
    '456740',
    'Hellboy Updated Row',
    'https://image.tmdb.org/t/p/w500/bk8LyaMqUtaQ9hUShuvFznQYQKR.jpg',
    "Hellboy comes to England, where he must defeat Nimue, Merlin's consort and the Blood Queen. But their battle will bring about the end of the world, a fate he desperately tries to turn away.",
    '1554944400',
    'Fantasy, Action'
];
$rows = [$updateRow];
$valueRange = new \Google_Service_Sheets_ValueRange();
$valueRange->setValues($rows);
$range = 'Sheet1!A2'; // where the replacement will start, here, first column and second line
$options = ['valueInputOption' => 'USER_ENTERED'];
$service->spreadsheets_values->update($spreadsheetId, $range, $valueRange, $options);


Delete Some Rows
We delete some rows by specifying a range of cells to clear:

$range = 'Sheet1!A23:F24'; // the range to clear, the 23th and 24th lines
$clear = new \Google_Service_Sheets_ClearValuesRequest();
$service->spreadsheets_values->clear($spreadsheetId, $range, $clear);

Download the Code and Examples
You can find all the code and examples in this GitHub repository.

It's packaged as a simple Symfony project, a set of commands, it also comes with a Docker image.
