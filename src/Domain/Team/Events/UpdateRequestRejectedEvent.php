<?php
namespace Jihe\Domain\Team\Events;

use Jihe\Domain\Team\UpdateRequest;

class UpdateRequestRejectedEvent
{

    private $updateRequest;

    function __construct(UpdateRequest $updateRequest)
    {
        $this->updateRequest = $updateRequest;
    }

    /**
     * @return mixed
     */
    public function getUpdateRequest()
    {
        return $this->updateRequest;
    }

    /**
     * @param mixed $updateRequest
     * @return UpdateRequestApprovedEvent
     */
    public function setUpdateRequest($updateRequest)
    {
        $this->updateRequest = $updateRequest;
    }
}