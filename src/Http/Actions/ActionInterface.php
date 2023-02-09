<?php

namespace Alexs\PhpAdvanced\Http\Actions;

use Alexs\PhpAdvanced\Http\Request;
use Alexs\PhpAdvanced\Http\Response;
interface ActionInterface
{
    public function handle(Request $request): Response;
}