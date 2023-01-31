<?php
use Google\CloudFunctions\FunctionsFramework;
use Psr\Http\Message\ServerRequestInterface;
use Dotenv\Dotenv;
use Google\Cloud\Storage\StorageClient;

FunctionsFramework::http('uploadPrivateFile', 'uploadPrivateFile');

function uploadPrivateFile(ServerRequestInterface $request): string
{
    if (is_file(__DIR__.'/.env')) {
        // Load .env file (for local environment)
        $dotenv = Dotenv::createMutable(__DIR__);
        $dotenv->safeLoad();
    }

    try {
        $storage = new StorageClient([
            'keyFile' => json_decode($_ENV['FIREBASE_CREDENTIAL_JSON'], true)
        ]);
        $bucket = $storage->bucket($_ENV['GCS_BUCKET']);
        $bucket->upload(
            fopen(__DIR__.'/private_file.txt', 'r'),
            [
                'name' => 'files/g/private/private_file.txt',
                'predefinedAcl' => 'private'
            ]
        );
        return 'Success';
    } catch (\Throwable $th) {
        return 'Error';
    }
}
