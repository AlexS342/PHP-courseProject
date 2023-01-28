<?php

namespace Alexs\PhpAdvanced\Blog\Repositories;

use Alexs\PhpAdvanced\Blog\Exceptions\CommitNotFoundException;
use Alexs\PhpAdvanced\Blog\Commit;

class InMemoryCommitRepository
{
    /**
     * @var array
     */
    private array $commits = [];

    /**
     * @param Commit $commit
     * @return void
     */
    public function save(Commit $commit):void
    {
        $this->commits[] = $commit;
    }

    /**
     * @param int $id
     * @return Commit
     * @throws CommitNotFoundException
     */
    public function get(int $id): Commit
    {
        foreach ($this->commits as $commit){
            if($commit->getId() === $id){
                return $commit;
            }
        }
        throw new CommitNotFoundException("Commit not found $id");
    }
}