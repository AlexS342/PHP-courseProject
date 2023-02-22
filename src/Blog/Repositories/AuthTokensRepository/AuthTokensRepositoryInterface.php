<?php

namespace Alexs\PhpAdvanced\Blog\Repositories\AuthTokensRepository;

use Alexs\PhpAdvanced\AuthToken;

interface AuthTokensRepositoryInterface
{
// Метод сохранения токена
    public function save(AuthToken $authToken): void;
// Метод получения токена
    public function get(string $token): AuthToken;
}