<?php
namespace Jihe\Infrastructure\Repository\Team;

use Doctrine\ORM\EntityRepository;
use Jihe\Domain\Team\UpdateRequest;
use Jihe\Domain\Team\UpdateRequestRepository as UpdateRequestRepositoryContract;
use Jihe\Domain\User\User;

class UpdateRequestRepository extends EntityRepository implements UpdateRequestRepositoryContract
{
    public function store(UpdateRequest $updateRequest)
    {
        $this->_em->persist($updateRequest);
        $this->_em->flush($updateRequest);
    }

    /**
     * @inheritdoc
     */
    public function getPendingCount(User $requester)
    {
        return $this->getCount($requester, UpdateRequest::STATUS_PENDING);
    }

    /**
     * @inheritdoc
     */
    public function getApprovedCount(User $requester)
    {
        return $this->getCount($requester, UpdateRequest::STATUS_APPROVED);
    }

    private function getCount(User $requester, $status)
    {
        $dql = 'SELECT COUNT(ur.id) ' .
            'FROM Jihe\Domain\Team\UpdateRequest ur JOIN ur.requester re ' .
            'WHERE re.id=:requesterId ' .
            sprintf('AND ur.status = %d ', $status) .
            'AND ur.deletedAt IS NULL';

        $query = $this->_em->createQuery($dql)->setParameters([
            'requesterId'    => $requester->getId()
        ]);

        return $query->getSingleResult()[1];
    }
}