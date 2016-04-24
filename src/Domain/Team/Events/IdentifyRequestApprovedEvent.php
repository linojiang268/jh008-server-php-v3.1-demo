<?php
namespace Jihe\Domain\Team\Events;

use Jihe\Domain\Team\IdentifyRequest;

class IdentifyRequestApprovedEvent
{
    private $identifyRequest;

    function __construct(IdentifyRequest $identifyRequest)
    {
        $this->identifyRequest = $identifyRequest;
    }

    /**
     * @return mixed
     */
    public function getIdentifyRequest()
    {
        return $this->identifyRequest;
    }

    /**
     * @param mixed $identifyRequest
     * @return EnrollmentRequestApprovedEvent
     */
    public function setIdentifyRequest($identifyRequest)
    {
        $this->identifyRequest = $identifyRequest;
    }
}