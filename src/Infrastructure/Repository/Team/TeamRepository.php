<?php
namespace Jihe\Infrastructure\Repository\Team;

use Doctrine\ORM\EntityRepository;
use Jihe\Domain\Team\Team;
use Jihe\Domain\Team\TeamRepository as TeamRepositoryContract;

class TeamRepository extends EntityRepository implements TeamRepositoryContract
{
    public function store(Team $team)
    {
        $this->_em->persist($team);
        $this->_em->flush($team);
    }
}