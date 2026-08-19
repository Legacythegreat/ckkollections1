<?php
function getConfig() {
    $envFile  = __DIR__ . '/../.env';
    $envLocal = __DIR__ . '/../.env.local';

    if (!file_exists($envFile) && !file_exists($envLocal)) {
        die('Missing .env file. Please create .env with your database credentials.');
    }

    $config = [];

    // Load base .env (production credentials)
    if (file_exists($envFile)) {
        $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || strpos($line, '#') === 0) continue;
            $parts = explode('=', $line, 2);
            $key   = isset($parts[0]) ? trim($parts[0]) : '';
            $value = isset($parts[1]) ? trim($parts[1]) : '';
            if ($key !== '') $config[$key] = $value;
        }
    }

    // Only apply .env.local overrides when running on localhost / local development.
    // This prevents .env.local from accidentally overriding production settings
    // if the file gets uploaded to the hosting server.
    $isLocalhost = in_array(
        $_SERVER['SERVER_NAME'] ?? $_SERVER['HTTP_HOST'] ?? '',
        ['localhost', '127.0.0.1', '::1', '']
    ) || (php_sapi_name() === 'cli');

    if ($isLocalhost && file_exists($envLocal)) {
        $lines = file($envLocal, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || strpos($line, '#') === 0) continue;
            $parts = explode('=', $line, 2);
            $key   = isset($parts[0]) ? trim($parts[0]) : '';
            $value = isset($parts[1]) ? trim($parts[1]) : '';
            if ($key !== '') $config[$key] = $value;
        }
    }

    // Enable debug mode when DEBUG=1 in .env
    if (isset($config['DEBUG']) && ($config['DEBUG'] === '1' || strtolower($config['DEBUG']) === 'true')) {
        ini_set('display_errors', '1');
        ini_set('display_startup_errors', '1');
        error_reporting(E_ALL);
    }

    return $config;
}
?>