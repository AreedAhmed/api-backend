<?php

declare(strict_types=1);

define('RECONMAP_APP_DIR', realpath('../'));

require RECONMAP_APP_DIR . '/vendor/autoload.php';

use GuzzleHttp\Psr7\Response;
use Laminas\HttpHandlerRunner\Emitter\SapiEmitter;
use League\Route\Http\Exception\NotFoundException;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Reconmap\ApiRouter;
use Reconmap\Services\ApplicationContainer;
use Reconmap\Services\ConfigLoader;

$logger = new Logger('http');
$logsDirectory = RECONMAP_APP_DIR . '/logs';
$applicationLogPath = RECONMAP_APP_DIR . '/logs/application.log';
if (is_writable($logsDirectory)) {
    $logger->pushHandler(new StreamHandler($applicationLogPath, Logger::DEBUG));
}

$configFilePath = RECONMAP_APP_DIR . '/config.json';
if (!file_exists($configFilePath) || !is_readable($configFilePath)) {
    $errorMessage = 'Missing or unreadable API configuration file (config.json)';
    $logger->error($errorMessage);
    header($errorMessage, true, 503);
    echo $errorMessage, PHP_EOL;
    exit;
}
$config = (new ConfigLoader())->loadFromFile($configFilePath);
$config->update('appDir', RECONMAP_APP_DIR);

$container = new ApplicationContainer($config, $logger);

$router = new ApiRouter();
$router->mapRoutes($container, $logger);

try {
    $request = GuzzleHttp\Psr7\ServerRequest::fromGlobals();
    $response = $router->dispatch($request);
} catch (NotFoundException $e) {
    $logger->error($e->getMessage());
    $response = (new Response)->withStatus(404);
} catch (Exception $e) {
    $logger->error($e->getMessage());
    $response = (new Response)->withStatus(500);
}

(new SapiEmitter)->emit($response);
