<?php

namespace Alexs\PhpAdvanced\Blog\Repositories\CommitRepository;

use Alexs\PhpAdvanced\Blog\Commit;
use Alexs\PhpAdvanced\Blog\UUID;

interface CommitRepositoryInterface
{
    public function save(Commit $commit) : void;
    public function get(UUID $uuid) : Commit;
}