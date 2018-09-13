<?php
/**
 * Created by PhpStorm.
 * User: Paladise
 * Date: 15-Aug-18
 * Time: 1:11 PM
 */

defined('APP_PATH') || define('APP_PATH', realpath('.'));

$config_apps = array(
    'apps' => 'admin',
    'environment' => "production",               //TODO :: development | production
    'dbenvironment' => "mysql",               //TODO :: development | production
    'version' => '1.0.0',
    'url' => array(
        'base' => 'http://api.softco.dev/',
        'assets' => 'http://api.softco.dev/assets/',
        'media' => 'http://api.softco.dev/media/'
    ),
    'template' => 'admin',
    'language' => array(
        'id' => array(
            "code" => "id",
            "name" => "Indonesia",
            "status" => true,
            'default' => false
        ),
        'en' => array(
            "code" => "en",
            "name" => "English",
            "status" => true,
            'default' => true
        ),
        'cn' => array(
            "code" => "cn",
            "name" => "China",
            "status" => true,
            'default' => false
        )
    ),
    'database' => array(
        'adapter'     => 'Mysql',
        'host'        => 'localhost',
        'username'    => 'root',
        'password'    => '',
        'dbname'      => 'bo',
        'charset'     => 'utf8',
    ),
    'security' => array(
//        'site_iv'     => 'öL2f×¸nf€{-ün)U³J N‘;æéÕd~3³',
        'site_iv'     => 'öL2f×¸nf€{',
        'site_key'    => '1234567812345678',
    ),
    'production' => array(
        'admin' => array(
            'xxx'     => 'öL2f×¸nf€{-ün)U³J N‘;æéÕd~3³',
            'xxx'     => 'öL2f×¸nf€{',
            'xxx'    => '1234567812345678',
        )
    )
);


return $config_apps;