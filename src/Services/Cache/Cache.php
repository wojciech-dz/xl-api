<?php

namespace App\Services\Cache;

interface Cache
{
    public function __construct($cacheProvider);
    public function get($key): ?string;
    public function set($key): void;
}