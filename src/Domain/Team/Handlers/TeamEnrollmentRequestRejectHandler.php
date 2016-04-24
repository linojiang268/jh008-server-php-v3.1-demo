<?php
namespace Jihe\Domain\Team\Handlers;

use Jihe\Domain\ActionHandler;
use Jihe\Domain\DomainException;
use Jihe\Domain\Team\EnrollmentRequestRepository;
use Jihe\Domain\Team\EnrollmentRequestService;
use Jihe\Domain\Team\Validators\TeamEnrollmentRequestRejectValidator;

class TeamEnrollmentRequestRejectHandler implements ActionHandler
{
    /**
     * @var TeamEnrollmentRequestRejectValidator
     */
    private $validator;
    /**
     * @var EnrollmentRequestRepository
     */
    private $enrollmentRequestRepository;
    /**
     * @var EnrollmentRequestService
     */
    private $enrollmentRequestService;

    function __construct(TeamEnrollmentRequestRejectValidator $validator,
                         EnrollmentRequestRepository          $enrollmentRequestRepository,
                         EnrollmentRequestService             $enrollmentRequestService)
    {
        $this->validator                   = $validator;
        $this->enrollmentRequestRepository = $enrollmentRequestRepository;
        $this->enrollmentRequestService    = $enrollmentRequestService;
    }

    /**
     * approve the request by admin
     *
     * @param array $request keys taken:
     *                          - id       (int) which request to approve
     */
    public function handle(array $request = [])
    {
        $this->validator->validate($request);

        $enrollmentRequest = $this->enrollmentRequestRepository->find($request['id']);
        if (is_null($enrollmentRequest)) {
            throw new DomainException('非法申请');
        }

        $this->enrollmentRequestService->approve($enrollmentRequest);
    }
}