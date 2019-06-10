<?php

namespace Facebook\WebDriver;

use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\WebDriverCapabilityType;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;

require_once('vendor/autoload.php');

$fbUsername = getenv('FB_USERNAME');
$fbPassword = getenv('FB_PASSWORD');
// Always the name of the host that's linked to this containerized script
$host = 'http://chrome:4444/wd/hub';

// Setup User-Agent and Luminati Proxy
$userAgent = "Mozilla/5.0 (Linux; Android 4.4; Nexus 5 Build/_BuildID_) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/30.0.0.0 Mobile Safari/537.36";
$options = new ChromeOptions();
$options->addArguments(['user-agent="' . $userAgent . '"']);
$caps = DesiredCapabilities::chrome();
$caps->setCapability(ChromeOptions::CAPABILITY, $options);

$guzzle = new Client();
do {
    try {
        $res = $guzzle->request('POST', 'http://luminati:22999/api/proxies', [
            RequestOptions::JSON => [
                "proxy" => [
                    "ips" => [],
                    "vips" => [],
                    "port" => 24000,
                    "seed" => false,
                    "pool_size" => 0,
                    "session" => true,
                    "zone" => "static",
                    "keep_alive" => 50,
                    "max_requests" => 0,
                    "pool_type" => null,
                    "sticky_ip" => false,
                    "whitelist_ips" => [],
                    "session_duration" => 0,
                    "country" => 'Brazil',
                    "proxy_type" => "persist",
                    "customer" => "<customer-id>",
                    "password" => "<password>",
                    "last_preset_applied" => "session_long"
                ]
            ]
        ]);
    } catch (GuzzleException $e) {
        sleep(1);
        echo $e->getMessage();
        continue;
    }
    echo $res->getBody();
    break;
} while (true);

$caps->setCapability(WebDriverCapabilityType::PROXY, [
    'proxyType' => 'manual',
    'httpProxy' => 'luminati:24000',
    'sslProxy' => 'luminati:24000',
]);

$driver = RemoteWebDriver::create($host, $caps);
$driver->get('https://www.facebook.org/');
$driver->findElement(WebDriverBy::cssSelector('input[type="email"]'))->sendKeys($fbUsername);
$driver->findElement(WebDriverBy::cssSelector('input[type="password"]'))->sendKeys($fbPassword);
$driver->findElement(WebDriverBy::id('loginbutton'))->click();
