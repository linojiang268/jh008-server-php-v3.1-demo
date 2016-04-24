<?php
namespace Jihe\Domain\Team;

use Jihe\Domain\User\User;

interface IdentifyRequestRepository
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

    public function store(IdentifyRequest $approveRequest);
}