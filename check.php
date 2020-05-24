<?php

/**
 * This file is part of the Phalconer.
 * 
 * (c) Tsend-Ayush Sharavdorj <tsendee0409@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

// Get text with tag
function textWithTag($isCli, $text, $startTag, $endTag)
{
    return $isCli ? $text : $startTag . $text . $endTag;
}

// Print requirement
function checkRequirement($isCli, $success, $message = '', $help = '', $mandatory = true)
{
    if ($success) {
        $status = textWithTag($isCli, '[SUCCESS]', '<div class="success">', '</div>');
        $status = textWithTag(!$isCli, $status, "\e[1;37;42m ", " \e[0m");
    } elseif ($mandatory) {
        $status = textWithTag($isCli, '[DANGER]', '<div class="danger">', '</div>');
        $status = textWithTag(!$isCli, $status, "\e[1;37;41m ", "  \e[0m");
    } else {
        $status = textWithTag($isCli, '[WARNING]', '<div class="warning">', '</div>');
        $status = textWithTag(!$isCli, $status, "\e[0;30;43m ", " \e[0m");
    }

    echo $isCli ? $status . "\t" : '<div class="flex">' . $status;
    echo $success ? $message : $help;
    echo $isCli ? "\n" : '</div>';
}

// Check cli or http
$isCli = isset($_SERVER['HTTP_HOST']) === false;

if ($isCli) {
    echo "\n";
    echo "===========================================\n";
    echo "        Phalcon 4 requirements check       \n";
    echo "===========================================\n";
    echo "\n";
    echo "\e[0;30;43m                 [WARNING]                 \e[0m\n";
    echo "\e[0;30;43m   The PHP CLI uses a different php.ini    \e[0m\n";
    echo "\e[0;30;43m  file from the web server. Please launch  \e[0m\n";
    echo "\e[0;30;43m   this (check.php) from your web server.  \e[0m\n";
    echo "\n";
} else {
    echo '<!DOCTYPE html><html><head><title>Phalconer</title><style>.flex{display:flex;}.success,.danger,.warning{min-width:100px;text-align:center;margin-right:20px;}.success{background-color:green;color:white;}.danger{background-color:red;color:white;}.warning{background-color:yellow;}</style></head><body>';
    echo '<h3>Phalcon 4 requirements check</h3>';
}

echo textWithTag($isCli, 'Configuration File (php.ini): ' . (php_ini_loaded_file() ?: 'A php.ini file is not loaded') . "\n\n", '<p>', '</p>');

$extensions = [
    ['name' => 'psr', 'mandatory' => true, 'help' => ''],
    ['name' => 'curl', 'mandatory' => true, 'help' => ''],
    ['name' => 'fileinfo', 'mandatory' => true, 'help' => ''],
    ['name' => 'gettext', 'mandatory' => true, 'help' => ''],
    ['name' => 'gd', 'mandatory' => false, 'help' => 'if you use the Phalcon\Image\Adapter\Gd class'],
    ['name' => 'imagick', 'mandatory' => false, 'help' => 'if you use the Phalcon\Image\Adapter\Imagick class'],
    ['name' => 'json', 'mandatory' => true, 'help' => ''],
    ['name' => 'PDO', 'mandatory' => false, 'help' => 'if you use the database'],
    ['name' => 'mysqlnd', 'mandatory' => false, 'help' => 'if you use the MySQL database'],
    ['name' => 'pgsql', 'mandatory' => false, 'help' => 'if you use the PostgreSQL database'],
    ['name' => 'openssl', 'mandatory' => true, 'help' => ''],
    ['name' => 'mbstring', 'mandatory' => true, 'help' => ''],
    ['name' => 'memcached', 'mandatory' => false, 'help' => 'if you use the cache']
];

$minPhpVersion = '7.2.0';
$minPhalconVersion = '4.0.0';

checkRequirement(
    $isCli,
    version_compare(phpversion(), $minPhpVersion, '>='),
    'PHP version is ' . phpversion(),
    'PHP version must be >= ' . $minPhpVersion . ' (Current version is ' . phpversion() . ')',
    true
);

foreach ($extensions as $extension) {
    checkRequirement(
        $isCli,
        extension_loaded($extension['name']),
        strtoupper($extension['name']) . ' extension is installed',
        'Install and enable the ' . strtoupper($extension['name']) . ' extension ' . $extension['help'],
        $extension['mandatory']
    );
}

checkRequirement(
    $isCli,
    extension_loaded('phalcon') && version_compare(phpversion('phalcon'), $minPhalconVersion, '>='),
    'Phalcon version is ' . phpversion('phalcon'),
    extension_loaded('phalcon') ? 'Phalcon version must be >= ' . $minPhalconVersion . ' (Current version is ' . phpversion('phalcon') . ')' : 'Install and enable the Phalcon extension',
    true
);

echo $isCli ? "\n" : '</body></html>';
