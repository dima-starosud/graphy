<?php


namespace Staro\Graphy\GoogleApi;


use Exception;
use Google_Client;
use Google_Service_Sheets;

final class GoogleServiceSheetsProvider {
    /**
     * @var GoogleServiceSheetsProviderConfig
     */
    private $config;

    function __construct(GoogleServiceSheetsProviderConfig $config) {
        $this->config = $config;
    }

    function getService(): Google_Service_Sheets {
        $client = new Google_Client();
        $client->setApplicationName( 'Google Sheets API PHP Quickstart' );
        $client->setScopes( Google_Service_Sheets::SPREADSHEETS_READONLY );
        /** @noinspection PhpUnhandledExceptionInspection */
        $client->setAuthConfig( $this->path( 'credentials.json' ) );
        $client->setAccessType( 'offline' );
        $client->setPrompt( 'select_account consent' );

        // Load previously authorized token from a file, if it exists.
        // The file token.json stores the user's access and refresh tokens, and is
        // created automatically when the authorization flow completes for the first
        // time.
        $tokenPath = $this->path( 'token.json' );
        if ( file_exists( $tokenPath ) ) {
            $accessToken = json_decode( file_get_contents( $tokenPath ), true );
            $client->setAccessToken( $accessToken );
        }

        // If there is no previous token or it's expired.
        if ( $client->isAccessTokenExpired() ) {
            // Refresh the token if possible, else fetch a new one.
            if ( $client->getRefreshToken() ) {
                $client->fetchAccessTokenWithRefreshToken( $client->getRefreshToken() );
            } elseif ( php_sapi_name() != 'cli' ) {
                /** @noinspection PhpUnhandledExceptionInspection */
                throw new Exception( 'Refresh Google API token.' );
            } else {
                // Request authorization from the user.
                $authUrl = $client->createAuthUrl();
                printf( "Open the following link in your browser:\n%s\n", $authUrl );
                print 'Enter verification code: ';
                $authCode = trim( fgets( STDIN ) );

                // Exchange authorization code for an access token.
                $accessToken = $client->fetchAccessTokenWithAuthCode( $authCode );
                $client->setAccessToken( $accessToken );

                // Check to see if there was an error.
                if ( array_key_exists( 'error', $accessToken ) ) {
                    /** @noinspection PhpUnhandledExceptionInspection */
                    throw new Exception( join( ', ', $accessToken ) );
                }
            }
            // Save the token to a file.
            if ( !file_exists( dirname( $tokenPath ) ) ) {
                mkdir( dirname( $tokenPath ), 0700, true );
            }
            file_put_contents( $tokenPath, json_encode( $client->getAccessToken() ) );
        }

        return new Google_Service_Sheets( $client );
    }

    private function path(string $file): string {
        $dir = $this->config->getBaseDir();
        return "$dir/$file";
    }
}
