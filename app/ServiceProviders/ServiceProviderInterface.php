<?php

namespace App\ServiceProviders;

use Illuminate\Container\Container;

interface ServiceProviderInterface
{
    public function register(Container $container): void;
}
