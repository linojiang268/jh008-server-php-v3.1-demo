<?php
namespace Jihe\Domain\Team\Events;

use Jihe\Domain\Team\EnrollmentRequest;

class EnrollmentRequestRejectedEvent
{
    private $enrollmentRequest;

    function __construct(EnrollmentRequest $enrollmentRequest)
    {
        $this->enrollmentRequest = $enrollmentRequest;
    }

    /**
     * @return mixed
     */
    public function getEnrollmentRequest()
    {
        return $this->enrollmentRequest;
    }

    /**
     * @param mixed $enrollmentRequest
     * @return EnrollmentRequestApprovedEvent
     */
    public function setEnrollmentRequest($enrollmentRequest)
    {
        $this->enrollmentRequest = $enrollmentRequest;
    }

}