<?php

use Symfony\Component\Dotenv\Dotenv;

require dirname(__DIR__).'/vendor/autoload.php';

if (method_exists(Dotenv::class, 'bootEnv')) {
    (new Dotenv())->bootEnv(dirname(__DIR__).'/.env');
}

// executes the "php bin/console cache:clear" command
passthru(sprintf(
  'APP_ENV=%s php "%s/../bin/console" cache:clear --no-warmup',
    $_ENV['APP_ENV'],
   __DIR__
 ));

if ($_SERVER['APP_DEBUG']){
    umask(0000);
}

return[
    DAMA\DoctrineTestBundle\DAMADoctrineTestBundle::class => ['test' => true],
];
