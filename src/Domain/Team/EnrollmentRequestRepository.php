<?php
namespace Jihe\Domain\Team;

use Jihe\Domain\User\User;

interface EnrollmentRequestRepository
{
    /**
     * find a create request by id
     *
     * @param $id string
     * @return EnrollmentRequest|null
     */
    public function find($id);

    /**
     * get the count of pending request of the user
     *
     * @param User $requester
     * @return boolean
     */
    public function getPendingCount(User $requester);

    /**
     * get the count of approved request of the user
     *
     * @param User $requester
     * @return boolean
     */
    public function getApprovedCount(User $requester);

    public function store(EnrollmentRequest $enrollmentRequest);
}