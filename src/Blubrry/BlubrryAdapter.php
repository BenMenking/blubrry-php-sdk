<?php

namespace Blubrry;

use League\Flysystem\FilesystemAdapter;
use League\Flysystem\FilesystemException;
use League\Flysystem\Config;
use League\Flysystem\FileAttributes;
use Blubrry\REST\API;
use GuzzleHttp\Client;

class BlubrryAdapter implements FilesystemAdapter {
    private $program = '';
    private $connector = null;
    private $auth = [];

    public function __construct(string $program) {
        $this->program = $program;
        $this->connector = new API();
    }

    public function getPrograms(): array {
        return $this->connector->mediaHosting()->listPrograms();
    }

    /**
     * 
     * @throws FilesystemException
     */
    public function fileExists(string $path): bool {
        throw new FilesystemException('Not implemented');
    }

    /**
     * @throws UnableToWriteFile
     * @throws FilesystemException
     */
    public function write(string $path, string $contents, Config $config): void {
        $tmpf = tempnam('', '');

        if( $tmpf === false ) throw new FilesystemException('Unable to create a temporary file');

        file_put_contents($tmpf, $contents);

        $media = [
            'name'=>$path,
            'tmp_name'=>$tmpf
        ];

        $response = $this->connector->mediaHosting()->uploadMedia($this->program, $media);

        unlink($tmpf);
    }

    /**
     * @param resource $contents
     *
     * @throws UnableToWriteFile
     * @throws FilesystemException
     */
    public function writeStream(string $path, $contents, Config $config): void {
        $tmpf = tempnam('', '');

        if( $tmpf === false ) throw new FilesystemException('Unable to create a temporary file');

        $fp = fopen($tmpf, 'wb');

        while(!feof($contents)) {
            fwrite($fp, fread($contents, 4096));
        }

        fclose($fp);
        
        $media = [
            'name'=>$path,
            'tmp_name'=>$tmpf
        ];

        $response = $this->connector->mediaHosting()->uploadMedia($this->program, $media);

        unlink($tmpf);
    }

    /**
     * @throws UnableToReadFile
     * @throws FilesystemException
     */
    public function read(string $path): string {
        throw new UnableToReadFile('Not implemented');
    }

    /**
     * @return resource
     *
     * @throws UnableToReadFile
     * @throws FilesystemException
     */
    public function readStream(string $path) {
        throw new UnableToReadFile('Not implemented');
    }

    /**
     * @throws UnableToDeleteFile
     * @throws FilesystemException
     */
    public function delete(string $path): void {
        $this->connector->mediaHosting()->deleteMedia($path);
    }

    /**
     * @throws UnableToDeleteDirectory
     * @throws FilesystemException
     */
    public function deleteDirectory(string $path): void {
        throw new UnableToDeleteDirectory('Not implemented');
    }

    /**
     * @throws UnableToCreateDirectory
     * @throws FilesystemException
     */
    public function createDirectory(string $path, Config $config): void {
        throw new UnableToCreateDirectory('Not implemented');
    }

    /**
     * @throws InvalidVisibilityProvided
     * @throws FilesystemException
     */
    public function setVisibility(string $path, string $visibility): void {
        $this->connector->mediaHosting()->publishMedia($this->program, $path, $visibility);
    }

    /**
     * @throws UnableToRetrieveMetadata
     * @throws FilesystemException
     */
    public function visibility(string $path): FileAttributes {
        throw new UnableToRetrieveMetadata('Not implemented');
    }

    /**
     * @throws UnableToRetrieveMetadata
     * @throws FilesystemException
     */
    public function mimeType(string $path): FileAttributes {
        $pi = pathinfo($path);

        $mime = '';

        switch($pi['extension']) {
            case 'mp3': 
                $mime = 'audio/mpeg';
                break;
            case 'm4a':
                $mime = 'audio/mp4';
                break;
            default:
                $mime = 'application/octet-stream';
        }

        return $mime;
    }

    /**
     * @throws UnableToRetrieveMetadata
     * @throws FilesystemException
     */
    public function lastModified(string $path): FileAttributes {
        throw new UnableToRetrieveMetadata('Not implemented');
    }

    /**
     * @throws UnableToRetrieveMetadata
     * @throws FilesystemException
     */
    public function fileSize(string $path): FileAttributes {

    }

    /**
     * @return iterable<StorageAttributes>
     *
     * @throws FilesystemException
     */
    public function listContents(string $path, bool $deep): iterable {

    }

    /**
     * @throws UnableToMoveFile
     * @throws FilesystemException
     */
    public function move(string $source, string $destination, Config $config): void {
        throw new UnableToMoveFile('Not implemented');
    }

    /**
     * @throws UnableToCopyFile
     * @throws FilesystemException
     */
    public function copy(string $source, string $destination, Config $config): void {
        throw new UnableToCopyFile('Not implemented');
    }

    private function getFileAttributes(string $path) {
        $httpclient = new Client([
            'base_url'=>'https://feeds.blubrry.com/'
        ]);

        $response = $client->get('feeds/' . $this->program . '.xml');

        $info = simplexml_load_string((string)$response->getBody());

        foreach($info['channel']['item'] as $item) {
            // get enclosure attributes and such
        }
    }

    public function authorizeClientCredentials($client_user, $client_password, $client_id, $client_secret) {
        $params = [
            'grant_type'=>'password',
            'username'=>$client_user,
            'password'=>$client_password
        ];

        $httpclient = new Client([
            'base_uri'=>'https://api.blubrry.com/'
        ]);

        $response = $client->post('oauth2/authorize', [
            'auth'=>[
                $client_id,
                $client_secret
            ],
            'body' => http_build_query($params),
        ]);

        $info = json_decode((string)$response->getBody(), true);
       
        $this->auth = $this->connector->auth($client_id, $client_secret)->getNewAccessToken($info['refresh_token']);

        return $this->auth;
    }
}