<?php

namespace App\Rest;

interface Request
{
    public function get(string $endpoint, ?array $params = [], ?array $headers = []): array;

    public function post(string $endpoint, ?array $params = [], ?array $headers = []): array;
}