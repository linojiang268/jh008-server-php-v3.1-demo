<?php
namespace Jihe\Infrastructure\Repository\Team;

use Doctrine\ORM\EntityRepository;
use Jihe\Domain\Team\IdentifyRequest;
use Jihe\Domain\Team\IdentifyRequestRepository as IdentifyRequestRepositoryContract;
use Jihe\Domain\User\User;

class IdentifyRequestRepository extends EntityRepository implements IdentifyRequestRepositoryContract
{
    /**
     * @inheritdoc
     */
    public function find($id)
    {
        return parent::find($id);
    }

    /**
     * @inheritdoc
     */
    public function getPendingCount(User $requester)
    {
        return $this->getCount($requester, IdentifyRequest::STATUS_PENDING);
    }

    private function getCount(User $requester, $status)
    {
        $dql = 'SELECT COUNT(ir.id) ' .
            'FROM Jihe\Domain\Team\EnrollmentRequest ir JOIN ir.requester req ' .
            'WHERE req.id=:requesterId ' .
            sprintf('AND ir.status = %d ', $status) .
            'AND ir.deletedAt IS NULL';

        $query = $this->_em->createQuery($dql)->setParameters([
            'requesterId'    => $requester->getId()
        ]);

        return $query->getSingleResult()[1];
    }

    public function store(IdentifyRequest $approveRequest)
    {
        $this->_em->persist($approveRequest);
        $this->_em->flush($approveRequest);
    }
}