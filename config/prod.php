<?php

// configure your app for the production environment

$app['twig.path'] = array(__DIR__ . '/../templates');
$app['twig.options'] = array('cache' => __DIR__ . '/../var/cache/twig');

$app['photos_directory'] = __DIR__ . '/../public_html';
$app['photos_public_directory'] = '/fotos/';

$app['swiftmailer.options'] = array(
    'host' => getenv('MAIL_HOST'),
    'port' => getenv('MAIL_PORT'),
    'username' => getenv('MAIL_USERNAME'),
    'password' => getenv('MAIL_PASSWORD'),
    'encryption' => getenv('MAIL_ENCRYPTION'),
    'auth_mode' => getenv('MAIL_AUTH_MODE'),
);

$app['mail.config'] = [
    'to.booking' => getenv('MAIL_TO_BOOKING'),
    'to.manager' => getenv('MAIL_TO_MANAGER'),
    'to.admin' => getenv('MAIL_TO_ADMIN'),
    'from' => getenv('MAIL_FROM'),
    'subject' => getenv('MAIL_SUBJECT')
];
