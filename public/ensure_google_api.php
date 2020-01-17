<?php

use Staro\Graphy\GoogleApi\GoogleServiceSheetsProvider;

define( 'DIR_NAME', dirname( __DIR__ ) );

require_once DIR_NAME . '/vendor/autoload.php';

$provider = new GoogleServiceSheetsProvider( DIR_NAME . '/private/google' );
$client = $provider->getService();

// Prints the names and majors of students in a sample spreadsheet:
// https://docs.google.com/spreadsheets/d/1BxiMVs0XRA5nFMdKvBdBZjgmUUqptlbs74OgvE2upms/edit
$spreadsheetId = '1BxiMVs0XRA5nFMdKvBdBZjgmUUqptlbs74OgvE2upms';
$range = 'Class Data!A2:E';
$response = $client->spreadsheets_values->get( $spreadsheetId, $range );
$values = $response->getValues();

if ( empty( $values ) ) {
    print "No data found.\n";
} else {
    print "Name, Major:\n";
    foreach ($values as $row) {
        // Print columns A and E, which correspond to indices 0 and 4.
        printf( "%s, %s\n", $row[0], $row[4] );
    }
}
