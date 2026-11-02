<?php

use Symfony\Component\Dotenv\Dotenv;

require dirname(__DIR__).'/vendor/autoload.php';

if (method_exists(Dotenv::class, 'bootEnv')) {
    (new Dotenv())->bootEnv(dirname(__DIR__).'/.env');
}

if ($_SERVER['APP_DEBUG'] ?? false) {
    umask(0000);
}

$projectDir = dirname(__DIR__);
$jwtDir = $projectDir.'/var/jwt';
if (!is_dir($jwtDir)) {
    mkdir($jwtDir, 0777, true);
}

$privateKeyPath = $jwtDir.'/test-private.pem';
$publicKeyPath = $jwtDir.'/test-public.pem';

if (!file_exists($privateKeyPath) || !file_exists($publicKeyPath)) {
    $passphrase = $_SERVER['JWT_PASSPHRASE'] ?? $_ENV['JWT_PASSPHRASE'] ?? 'test-passphrase';
    $res = openssl_pkey_new([
        'private_key_type' => OPENSSL_KEYTYPE_RSA,
        'private_key_bits' => 2048,
    ]);
    openssl_pkey_export($res, $privateKey, $passphrase);
    $publicKey = openssl_pkey_get_details($res)['key'];
    file_put_contents($privateKeyPath, $privateKey);
    file_put_contents($publicKeyPath, $publicKey);
}

// Rebuild the SQLite test database from entity metadata so each test run starts clean.
$dbFile = $projectDir.'/var/test.db';
if (file_exists($dbFile)) {
    unlink($dbFile);
}

passthru(sprintf(
    '%s %s doctrine:schema:create --no-interaction --env=test 2>&1',
    escapeshellarg(PHP_BINARY),
    escapeshellarg($projectDir.'/bin/console'),
), $exit);
if ($exit !== 0) {
    throw new RuntimeException('Failed to create test database schema (exit '.$exit.')');
}
