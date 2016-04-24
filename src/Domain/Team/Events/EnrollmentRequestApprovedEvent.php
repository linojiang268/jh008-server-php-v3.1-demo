<?php
namespace Jihe\Domain\Team\Events;

class EnrollmentRequestApprovedEvent
{
    private $enrollmentRequest;

    function __construct($enrollmentRequest)
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