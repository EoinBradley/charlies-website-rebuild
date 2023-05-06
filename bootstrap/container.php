<?php

use App\ServiceProviders\LoggerServiceProvider;
use Illuminate\Container\Container;

$container = Container::getInstance();

(new LoggerServiceProvider())->register($container);

return $container;
