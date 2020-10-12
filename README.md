# Blubrry PHP SDK

The SDK is based on Blubrry API version 2 and you can find the documentation [here](https://create.blubrry.com/resources/blubrry-api/)

## Supported Features

  - Media Hosting
  - Podcast Statistics
  - Social Medias


## Installation

Blubrry SDK requires [PHP](https://www.php.net/) v7.2+ to run.

`use Blubrry\REST\Api`

### Authenticating Users

The Blubrry API has OAuth2.0 authentication system.

In way to keep using that authentication system, you will have to implement some thing to make that work. The first step is to contat the [Blubrry Support Team](https://www.blubrry.com/contact/) and ask them for user credentials to use their API.

After that, you will have to add a button into your Website with a redirect to a link like this:

```
https://api.blubrry.com/oauth2/authorize?response_type=code&client_id=$client_id&redirect_uri=$redirect_uri
```

Where:

Parameters  | Description | Type | Optional
----------  | ----------- | -    |--------
client_id |  The client_id that the customer recieved from Blubrry Support Team. | string | no
redirect_uri |  The url that the user should be redirected after login into the Blubrry account. | string | no

This will return a link like this:

```
https://$redirect_uri/code=767a88a9576asdasdasda123123cfd
```

Then, you will have to retrieve a Refresh token for this user:

### - getRefresh
Description: Gets Access and Refresh token from Blubrry API.

Parameters  | Description | Type | Optional
----------  | ----------- | -    |--------
clientId | Client Id recieved from Blubrry Team | string | no
clientSecret |  Client Secret recieved from Blubrry | string | no
code | Response code from User login at Blubrry | string | no
redirectUri | The url that the user should be redirected after login into the Blubrry account | integer | yes

Example request:

``` php
<?php

$api = new \Blubrry\REST\API();

$code = '767a88a9576asdasdasda123123cfd';
$redirectUri = 'https://google.com/login';
$clientId = '123456789';
$clientSecret = '12d3sa4d56as74d65asd32as1d';

$api->auth($clientId, $clientSecret)->getRefresh($code, $redirectUri);
```

Example response:

``` json
{"access_token":"3b636a92ee50a8f17543f6a531b27e55d525bcd1", "expires_in":3600, "token_type":"bearer", "scope":null, "refresh_token":"55b01e60a74e45b3c66032627dcbc0dddd0bbd6a"}
```

And then, you will use the `access_token` to be able to do requests to the another API endpoits.

The `access_token` expires in one hour, you will need to save the `refresh_token` locally and send a request to the endpoint `refreshToken` to retrieve a new `access_token` without need of the user loggin into the Blubrry account.

### - getNewAccessToken

Description: Updates the Access token using the Refresh Token.

Parameters  | Description | Type | Optional
----------  | ----------- | -    |--------
clientId | Client Id recieved from Blubrry Team | string | no
clientSecret |  Client Secret recieved from Blubrry | string | no
refreshToken | Response field from getRefresh function | string | no

Example request:

``` php
<?php

$api = new \Blubrry\REST\API();

$clientId = '123456789';
$clientSecret = '12d3sa4d56as74d65asd32as1d';
$refreshToken = '55b01e60a74e45b3c66032627dcbc0dddd0bbd6a';

$api->auth($clientId, $clientSecret)->getNewAccessToken($refreshToken);
```

Example response:

``` json
{"access_token":"3b636a92ee50a8f17543f6a531b27e55d525bcd1", "expires_in":3600, "token_type":"bearer", "scope":null}
```

# Endpoints

### - listPrograms
Description: List Programs from Blubrry.

Parameters  | Description | Type | Optional
----------  | ----------- | -    |--------
start |  Specifies the number of results to return. The default is 20, 100 maximum | integer | yes
limit |  Specifies the start position of returned results | integer | yes

Example request:

``` php
<?php

$api = new \Blubrry\REST\API($accessToken);

$start = 0;
$limit = 100;

$api->mediaHosting()->listPrograms($limit, $start);
```

Example response:

``` json
[
  {
    "program_id":"1",
    "program_title":"Your Program Title",
    "program_keyword":"somewordhere"
  }
]
```
---
### - listUnpublished
Description: List umpublished Media from Blubrry.

Parameters  | Description | Type | Optional
----------  | ----------- | -    |--------
programKeyword | Specifies the program | string | no
start |  Specifies the number of results to return. The default is 20, 100 maximum | integer | yes
limit |  Specifies the start position of returned results | integer | yes

Example request:

``` php
<?php

$api = new \Blubrry\REST\API($accessToken);

$start = 0;
$limit = 100;
$programKeyword = "my_program";

$api->mediaHosting()->listUnpublished($programKeyword, $limit, $start);
```

Example response:

``` json
{}
```
---
### - publishMedia
Description: Publish Media into Blubrry.

Parameters  | Description | Type | Optional
----------  | ----------- | -    |--------
programKeyword | Specifies the program | string | no
mediafile |  Specifies the media file to insert | string | no
publish |  When true, the media file will be made publicly available. | boolean | no

Example request:

``` php
<?php

$api = new \Blubrry\REST\API($accessToken);

$programKeyword = "my_program";
$mediafile = "";
$publish = false;

$api->mediaHosting()->publishMedia($programKeyword, $mediafile, $publish);
```

Example response:

``` json
{}
```
---
### - deleteMedia
Description: Delete media from Blubrry

Parameters  | Description | Type | Optional
----------  | ----------- | -    |--------
programKeyword | Specifies the program | string | no
mediafile | Specifies the media file to delete | string | no

Example request:

``` php
<?php

$api = new \Blubrry\REST\API($accessToken);

$programKeyword = "my_program";
$mediafile = "";

$api->mediaHosting()->deleteMedia($programKeyword, $mediafile);
```

Example response:

``` json
{}
```
---
### - addMigrateMediaUrl
Description: Adds media URLs to the migration queue.

Parameters  | Description | Type | Optional
----------  | ----------- | -    |--------
programKeyword | Specifies the program | string | no
url | Individual URL to add to migration queue. | string | no
urls | Multiple URLs separated by new lines to add to migration queue. | Array | yes

Example request:

``` php
<?php

$api = new \Blubrry\REST\API($accessToken);

$programKeyword = 'my_program';
$url = '';
$urls = ['', ''];

$api->mediaHosting()->addMigrateMediaUrl($programKeyword, $url, $urls);
```

Example response:

``` json
{"success":"URL(s) added successfully."}
```
---
### - removeMigrateMediaUrl
Description: Remove media URLs from the migration queue.

Parameters  | Description | Type | Optional
----------  | ----------- | -    |--------
programKeyword | Specifies the program | string | no
url | Individual URL to add to migration queue. | string | no
urls | Multiple URLs separated by new lines to add to migration queue. Send `null` or `[]` if you are using `url`) | Array | yes
ids | One or more unique migrate IDs separated by commas. | Array | no

Example request:

``` php
<?php

$api = new \Blubrry\REST\API($accessToken);

$programKeyword = 'my_program';
$url = '';
$urls = ['', ''];
$ids = [123, 321, 3444, 3555];

$api->mediaHosting()->removeMigrateMediaUrl($programKeyword, $url, $urls, $ids);
```

Example response:

``` json
{"success":"URL removed successfully."}
```
---
### - migrateStatus
Description: Makes the uploaded media file publicly available.

Parameters  | Description | Type | Optional
----------  | ----------- | -    |--------
programKeyword | Specifies the program | string | no
status | Only returns results with specific status. Status may be any one of `queued`, `downloading`, `completed`, `skipped`, `error` or empty string for no specific status | string | yes
start | Specifies the number of results to return. The default is 20, 100 maximum | integer | yes
limit | Specifies the start position of returned results | integer | yes
ids | One or more unique migrate IDs separated by commas. | Array | yes

Example request:

``` php
<?php

$api = new \Blubrry\REST\API($accessToken);

$programKeyword = 'my_program';
$status = 'queued';
$start = 0;
$limit = 100;
$ids = [123, 321, 3444, 3555];

$api->mediaHosting()->migrateStatus($programKeyword, $status, $start, $limit, $ids);
```

Example response:

``` json
{}
```
---
### - uploadMedia
Description: Uploads a media file to the server.

Parameters  | Description | Type | Optional
----------  | ----------- | -    |--------
programKeyword | Specifies the program | string | no
media_file | Specifies the media file to upload. | string | no


Example request:

``` php
<?php

$api = new \Blubrry\REST\API($accessToken);

$programKeyword = 'my_program';
$media_file = '';

$api->mediaHosting()->uploadMedia($programKeyword, $media_file);
```

Example response:

``` json
{}
```
---
### - summary
Description: Gets Podcast Summary.

Parameters  | Description | Type | Optional
----------  | ----------- | -    |--------
programKeyword | Specifies the program | string | no
month | Specific month to pull summary from. | string | yes
year | Specific year to pull summary from | string | yes

Example request:

``` php
<?php

$api = new \Blubrry\REST\API($accessToken);

$programKeyword = 'my_program';
$media_file = '';

$api->podcastStatistics()->summary($programKeyword, $month, $year);
```

Example response:

``` json
{
    "stats_url":"http:\/\/stats.blubrry.com",
    "program_id":"123456",
    "overall": {
        "total":null,
        "unique":null
    },
    "current_month": {
        "total":null,
        "unique":null
    },
    "last_month": {
        "total":null,
        "unique":null
    },
    "media":[]
}
```

---
### - totals
Description: Get totals from a specific podcast, only available to professional statistics accounts.
Note: `start-date` and `end-date` range cannot exceed 45 days.

Parameters | Sub-Parameters | Description | Type | Optional
---------- | -------------- | ----------- | ---- | --------
programKeyword | - | Specifies the program | string | no
params | - | Array with the following parameters | Array | no
-| start-date | A start date for fetching Statistics data. Requests can specify a start date formatted as YYYY-MM-DD.  | string | no
-| end-date | End date for fetching Statistics data. The request can specify an end date formatted as YYYY-MM-DD. | string | yes
-| fields | Defaults to date, episode, downloads Selector specifying a subset of fields to include in the response. Fields include date (YYYY-MM-DD), episode (media file name), downloads. | string | yes
-| start |  Specifies the number of results to return. The default is 20, 100 maximum | integer | yes
-| limit |  Specifies the start position of returned results | integer | yes


Example request:

``` php
<?php

$api = new \Blubrry\REST\API($accessToken);

$programKeyword = 'my_program';
$start_date = '';
$end_date = '';
$fields = '';
$start = '';
$limit = ;

$params = [
    'start-date' => $start_date,
    'end-date'   => $end_date,
    'fields'     => $fields,
    'start'      => $start,
    'limit'      => $limit,
];

$api->podcastStatistics()->totals($programKeyword, $params);
```

Example response:

``` json
{}
```
---
### - updateListing
Description: Updates the social listing.

Parameters  | Sub-Parameters |  Description | Type | Optional
----------  | -------------- |----------- | -    |--------
programKeyword | - |Specifies the program | string | no
params | - | Array with the following parameters | Array | no
-| title | Title of the podcast episode.  | string | no
-| date | Date in RFC 2822 format | string | no
-| mediaUrl | Podcast enclosure “url” value, must be a complete URL with protocol schema. | string | no
-| filesize | File size in bytes, this is the Podcast enclosure "length". Value should not be formatted, should not include commas.| string | no
-| feedUrl | The RSS feed URL for the specified podcast. Do not try to insert with the podcast episode data, will be used for a different purpose. | string | yes
-| guid | RSS item guid value. If not specified, the media-url is used as the guid value. | string | yes
-| subtitle | iTunes Subtitle of podcast episode, or the first 255 characters of blog post | string | yes
-| duration | iTunes duration, specified in hh:mm:ss | string | yes
-| explicit | iTunes explicit value, values can be one of: `yes`, `no`, `clean`. Default set to `no` | string | yes
-| link | RSS item “link” value, should be complete URL to the blog post or page associated with the podcast episode. | string | yes
-| image | RSS item "itunes:image" value or the episode’s official image in square coverart form, should be a complete URL to the episode specific image.| string | yes


Example request:

``` php
<?php

$api = new \Blubrry\REST\API($accessToken);

$programKeyword = 'my_program';

$params = [
    'feed-url'  => $feedUrl,
    'title'     => $title,
    'date'      => $date,
    'guid'      => $guid,
    'media-url' => $mediaUrl,
    'subtitle'  => $subtitle,
    'duration'  => $duration,
    'filesize'  => $filesize,
    'explicit'  => $explicit,
    'link'      => $link,
    'image'     => $image,
];

$api->social()->updateListing($programKeyword, $params);
```

Example response:

``` json
{}
```
---
### - getSocial

Description: Get Social Options.

Parameters  | Sub-Parameters |  Description | Type | Optional
----------  | -------------- |----------- | -    |--------
programKeyword | - |Specifies the program | string | no
params | - | Array with the following parameters | Array | no
-| **social-type** | Array with all of the another parameters | array | no
-| podcast-id | ID of podcast to post to social. | string | no
-| post-data | Destinations to post to with specified parameters | string | no
-| social-id | Destination social ID which is the meta_id value of the social network settings in the programs_meta database table.| string | no
-| social-image | URL to an image to represent the social network in a web page form. | string | no
-| social-title | Title of social destination | string | yes
-| **form-data** | This is an array of items of key/value pairs. The options are for generating the form and mapping the values to fields for the eventual post to social endpoints. | Array | yes
-| row-type | Specifies type of row to enter into form. Currently supported types: input-text, input-checkbox | string | yes
-| row-order | Order number for the row to appear in form. Number starts with 1 and increments upward. | string | yes
-| **row-attributes** | Row attributes if row type is `HTML` | array | yes
-| content | Raw HTML text to be displayed in on page. | string | yes
-| **row-attributes** | Social data array if social-type is `input-text` | Array | yes
-| label | Title of input, placed above input wrapped within a label tag. | string | yes
-| placeholder | Placeholder within input. | string | yes
-| help-text | Text to go below input box. | string | yes
-| rows | Number of rows for input box. Default if not specified is 1. If 2 or more, the textarea tag is used instead of input. | string | yes
-| maxlength | Maximum character length for the input box. | string | yes
-| name | Name of input field | string | yes
-| value | Value for input field | string | yes
-| **row-attributes** | Social data array if social-type is `input-checkbox` | Array | yes
-| label | Title of input, placed to the right of checkbox within a label tag. | string | yes
-| checked | Value of either blank (not checked) or 'checked' to indicate that the input should be checked upon initial loading. | string | yes
-| name | Name of input field (only used once). | string | yes
-| value  | Value of input field| string | yes
-| **row-attributes** | Social data array if social-type is `input-radio` | Array | yes
-| label | Title of input, placed to the right of checkbox within a label tag. | string | yes
-| checked | Value of either blank (not checked) or 'checked' to indicate that the input should be checked upon initial loading. | string | yes
-| name | Name of input field (may be used multiple times to indicate a series of checkboxes) | string | yes
-| value  | Value of input field | string | yes

Example request:

``` php
<?php

$api = new \Blubrry\REST\API($accessToken);

$programKeyword = 'my_program';

$params = [
    'twitter' => [
        'podcast-id'   => $podcastId,
        'post-data'    => $postData,
        'social-id'    => $socialId,
        'social-type'  => 'twitter',
        'social-title' => $socialTitle,
        'form-data'    => [
            'row-type'  => 'HTML',
            'row-order' => 1,
            'row-arrtibutes' => [
                'content' => '<p>hi</p>',
            ],
        ],
    ],
    'youtube' => [
        'podcast-id'   => $podcastId,
        'post-data'    => $postData,
        'social-id'    => $socialId,
        'social-type'  => 'youtube',
        'social-title' => $socialTitle,
        'form-data'    => [
            'row-type'  => 'HTML',
            'row-order' => 1,
            'row-arrtibutes' => [
                'content' => '<p>hello</p>',
            ],
        ],
    ],
    'facebook' => [
        'podcast-id'   => $podcastId,
        'post-data'    => $postData,
        'social-id'    => $socialId,
        'social-type'  => 'facebook',
        'social-title' => $socialTitle,
        'form-data'    => [
            'row-type'  => 'HTML',
            'row-order' => 1,
            'row-arrtibutes' => [
                'content' => '<p>hello</p>',
            ],
        ],
    ],
];

$api->social()->getSocial($programKeyword, $params);
```

Example response:

``` json
{}
```

---
### - postSocial

Description: Post to Social.

Parameters  | Sub-Parameters |  Description | Type | Optional
----------  | -------------- |----------- | -    |--------
programKeyword | - |Specifies the program | string | no
body | - | Array with the following parameters | Array | no
-| podcast-id | ID of podcast to post to social. | string | no
-| post-data | Destinations to post to with specified parameters | string | no
-| social-id | Destination social ID which is the meta_id value of the social network settings in the programs_meta database table.| string | no
-| social-type | Type of destination, not limited to but currently supported values: twitter, facebook, youtube| string | no
-| social-data | Array of data to be posted to the specified social network | string | no
-| social-data | Social data array if social-type is twitter | Array | yes
-| content–{SOCIAL-ID} | Message to post to twitter. Character limit: 140 | string | no
-| social-data | Social data array if social-type is facebook | Array | yes
-| title–{SOCIAL-ID} | Title of media file. Character limit: 90 | string | yes
-| description–{SOCIAL-ID} | Posting content to Facebook wall or fan page. Character limit: 4000 | string | yes
-| destination-{SOCIAL-ID}-{PAGE-ID} | One or more facebook destinations. | string | yes
-| social-data | Social data array if social-type is Youtube | Array | yes
-| title–{SOCIAL-ID} | Title of posting to YouTube. Character limit: 100 | string | yes
-| description–{SOCIAL-ID} |  Description of posting to YouTube. Character limit: 4000 | string | yes

Example request:

``` php
<?php

$api = new \Blubrry\REST\API($accessToken);

$programKeyword = 'my_program';

$body = [
    'podcast-id'  => $podcastId,
    'post-data'   => $postData,
    'social-id'   => $socialId,
    'social-type' => 'twitter',
    'social-data' => [
        'title-123456' => 'My awesome title for twitter',
    ],
];

$api->social()->postSocial($programKeyword, $body);
```

Example response:

``` json
{}
```

## LICENSE

This SDK is open source with a [public repository](github.com/lucashillebrandt/blubrry-php-sdk) on GitHub.
