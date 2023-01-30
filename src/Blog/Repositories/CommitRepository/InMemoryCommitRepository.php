<?php

namespace Alexs\PhpAdvanced\Blog\Repositories\CommitRepository;

use Alexs\PhpAdvanced\Blog\Exceptions\CommitNotFoundException;
use Alexs\PhpAdvanced\Blog\Commit;
use Alexs\PhpAdvanced\Blog\UUID;

class InMemoryCommitRepository  implements CommitRepositoryInterface
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
     * @param UUID $uuid
     * @return Commit
     * @throws CommitNotFoundException
     */
    public function get(UUID $uuid): Commit
    {
        foreach ($this->commits as $commit){
            if($commit->getUuid() === $uuid){
                return $commit;
            }
        }
        throw new CommitNotFoundException("Error: Commit not found $uuid");
    }
}