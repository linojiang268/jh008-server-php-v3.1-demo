<?php
namespace Jihe\Domain\Team;

use Jihe\Domain\DomainException;
use Jihe\Domain\Team\Events\EnrollmentRequestApprovedEvent;
use Jihe\Domain\Team\Events\EnrollmentRequestRejectedEvent;
use Jihe\Events\Dispatcher;

class EnrollmentRequestService
{
    /**
     * a team leader is restricted to create at most one team.
     */
    const MAX_ALLOWED_CREATED_TEAMS = 1;
    /**
     * a team leader is restricted to request when there is any request is pending.
     */
    const MAX_ALLOWED_PENDING_REQUEST = 1;

    /**
     * @var EnrollmentRequestRepository
     */
    private $enrollmentRequestRepository;
    /**
     * @var TeamRepository
     */
    private $teamRepository;
    /**
     * @var Dispatcher
     */
    private $eventDispatcher;

    function __construct(EnrollmentRequestRepository $enrollmentRequestRepository,
                         TeamRepository              $teamRepository,
                         Dispatcher                  $eventDispatcher)
    {
        $this->enrollmentRequestRepository = $enrollmentRequestRepository;
        $this->teamRepository              = $teamRepository;
        $this->eventDispatcher             = $eventDispatcher;
    }

    public function submit(EnrollmentRequest $enrollmentRequest)
    {
        $this->ensureCanSubmit($enrollmentRequest);
        $this->enrollmentRequestRepository->store($enrollmentRequest);
    }

    public function approve(EnrollmentRequest $enrollmentRequest)
    {
        if ($enrollmentRequest->hasBeenAssessed()) {
            throw new DomainException('该请求已经被处理过');
        }

        $enrollmentRequest->approve();

        $this->teamRepository->store($enrollmentRequest->getTeam());
        $this->enrollmentRequestRepository->store($enrollmentRequest);

        $this->eventDispatcher->dispatch([new EnrollmentRequestApprovedEvent($enrollmentRequest)]);
    }

    public function reject(EnrollmentRequest $enrollmentRequest)
    {
        if ($enrollmentRequest->hasBeenAssessed()) {
            throw new DomainException('该请求已经被处理过');
        }

        $enrollmentRequest->reject();

        $this->enrollmentRequestRepository->store($enrollmentRequest);

        $this->eventDispatcher->dispatch([new EnrollmentRequestRejectedEvent($enrollmentRequest)]);
    }

    private function ensureCanSubmit(EnrollmentRequest $enrollmentRequest)
    {
        // there is only a pending request can be exist
        $pendingRequestCount = $this->enrollmentRequestRepository->getPendingCount(
            $enrollmentRequest->getRequester());
        if ($pendingRequestCount >= self::MAX_ALLOWED_PENDING_REQUEST) {
            throw new DomainException('社团申请正在处理中，请勿重复提交');
        }

        // there is only a team created of one user
        $approvedRequestCount = $this->enrollmentRequestRepository->getApprovedCount(
            $enrollmentRequest->getRequester());
        if ($approvedRequestCount >= self::MAX_ALLOWED_CREATED_TEAMS) {
            throw new DomainException(sprintf('您已经创建了%d个社团，不能创建更多社团', $approvedRequestCount));
        }
    }
}