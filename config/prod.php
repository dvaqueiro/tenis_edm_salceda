<?php

// configure your app for the production environment

$app['twig.path'] = array(__DIR__.'/../templates');
$app['twig.options'] = array('cache' => __DIR__.'/../var/cache/twig');

$app['photos_directory'] = __DIR__ . '/../web';
$app['photos_public_directory'] ='/fotos/';

$app['swiftmailer.options'] = array(
    'host' => '',
    'port' => '',
    'username' => '',
    'password' => '',
    'encryption' => '',
    'auth_mode' => '',
);