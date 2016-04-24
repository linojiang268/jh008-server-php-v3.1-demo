<?php
namespace Jihe\Domain\Team\Handlers;

use Illuminate\Contracts\Auth\Guard;
use Jihe\Domain\ActionHandler;
use Jihe\Domain\DomainException;
use Jihe\Domain\Team\EnrollmentRequestRepository;
use Jihe\Domain\Team\EnrollmentRequestService;
use Jihe\Domain\Team\Validators\TeamEnrollmentRequestRejectValidator;
use Jihe\Domain\Team\Validators\TeamEnrollmentValidator;

class TeamEnrollmentRequestApproveHandler implements ActionHandler
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
    /**
     * @var Guard
     */
    private $auth;

    function __construct(TeamEnrollmentRequestRejectValidator $validator,
                         EnrollmentRequestRepository          $enrollmentRequestRepository,
                         EnrollmentRequestService             $enrollmentRequestService,
                         Guard                                $auth)
    {
        $this->validator                   = $validator;
        $this->enrollmentRequestRepository = $enrollmentRequestRepository;
        $this->enrollmentRequestService    = $enrollmentRequestService;
        $this->auth                        = $auth;
    }

    /**
     * reject the request by admin
     *
     * @param array $request  keys taken:
     *                            - id       (int) which request to reject
     */
    public function handle(array $request = [])
    {
        $this->validator->validate($request);

        $enrollmentRequest = $this->enrollmentRequestRepository->find($request['id']);
        $userId = $this->auth->user()->getAuthIdentifier();
        if (is_null($enrollmentRequest) || $enrollmentRequest->getRequester()->getId() != $userId) {
            throw new DomainException('非法申请');
        }

        $this->enrollmentRequestService->reject($enrollmentRequest);
    }
}