<?php
namespace Jihe\Domain\Team;

use Jihe\Domain\User\User;

interface UpdateRequestRepository
{
    /**
     * find a create request by id
     *
     * @param $id string
     * @return UpdateRequest|null
     */
    public function find($id);

    /**
     * get the count of approved request of the user
     *
     * @param User $requester
     * @return boolean
     */
    public function getApprovedCount(User $requester);

    /**
     * get the count of pending request of the user
     *
     * @param User $requester
     * @return boolean
     */
    public function getPendingCount(User $requester);

    public function store(UpdateRequest $updateRequest);
}