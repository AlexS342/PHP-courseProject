<?php

namespace Alexs\PhpAdvanced\Http\auth;

use Alexs\PhpAdvanced\Blog\User;
use Alexs\PhpAdvanced\Http\Request;

interface AuthenticationInterface
{
    public function user(Request $request): User;
}