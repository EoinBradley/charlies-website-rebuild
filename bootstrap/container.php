<?php

use App\ServiceProviders\DatabaseServiceProvider;
use App\ServiceProviders\LoggerServiceProvider;
use Illuminate\Container\Container;

$container = Container::getInstance();

(new DatabaseServiceProvider())->register($container);
(new LoggerServiceProvider())->register($container);

return $container;
