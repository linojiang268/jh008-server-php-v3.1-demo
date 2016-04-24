<?php
namespace Jihe\Infrastructure\Repository\Team;

use Doctrine\ORM\EntityRepository;
use Jihe\Domain\Team\EnrollmentRequest;
use Jihe\Domain\Team\EnrollmentRequestRepository as EnrollmentRequestRepositoryContract;
use Jihe\Domain\User\User;

class EnrollmentRequestRepository extends EntityRepository implements EnrollmentRequestRepositoryContract
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
        return $this->getCount($requester, EnrollmentRequest::STATUS_PENDING);
    }

    /**
     * @inheritdoc
     */
    public function getApprovedCount(User $requester)
    {
        return $this->getCount($requester, EnrollmentRequest::STATUS_APPROVED);
    }

    private function getCount(User $requester, $status)
    {
        $dql = 'SELECT COUNT(er.id) ' .
            'FROM Jihe\Domain\Team\EnrollmentRequest er JOIN er.requester req ' .
            'WHERE req.id=:requesterId ' .
            sprintf('AND er.status = %d ', $status) .
            'AND er.deletedAt IS NULL';

        $query = $this->_em->createQuery($dql)->setParameters([
            'requesterId'    => $requester->getId()
        ]);

        return $query->getSingleResult()[1];
    }

    public function store(EnrollmentRequest $enrollmentRequest)
    {
        $this->_em->persist($enrollmentRequest);
        $this->_em->flush($enrollmentRequest);
    }
}