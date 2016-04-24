<?php
namespace Jihe\Domain\Team;

use Jihe\Domain\DomainException;
use Jihe\Domain\Team\Events\UpdateRequestApprovedEvent;
use Jihe\Domain\Team\Events\UpdateRequestRejectedEvent;
use Jihe\Events\Dispatcher;

class UpdateRequestService
{
    /**
     * detail of a team can only be updated once
     */
    const MAX_ALLOWED_UPDATED_TIMES = 1;
    /**
     * a team leader is restricted to request when there is any request is pending.
     */
    const MAX_ALLOWED_PENDING_REQUEST = 1;

    /**
     * @var UpdateRequestRepository
     */
    private $updateRequestRepository;
    /**
     * @var CertificationRepository
     */
    private $teamRepository;
    /**
     * @var Dispatcher
     */
    private $eventDispatcher;

    function __construct(UpdateRequestRepository $updateRequestRepository,
                         TeamRepository          $teamRepository,
                         Dispatcher              $eventDispatcher)
    {
        $this->updateRequestRepository = $updateRequestRepository;
        $this->teamRepository = $teamRepository;
        $this->eventDispatcher = $eventDispatcher;
    }


    public function submit(UpdateRequest $updateRequest)
    {
        $this->ensureCanSubmit($updateRequest);
        $this->updateRequestRepository->store($updateRequest);
    }

    public function approve(UpdateRequest $updateRequest)
    {
        if ($updateRequest->hasBeenAssessed()) {
            throw new DomainException('该请求已经被处理过');
        }

        $updateRequest->approve();

        $this->teamRepository->store($updateRequest->getTeam());
        $this->updateRequestRepository->store($updateRequest);

        $this->eventDispatcher->dispatch([new UpdateRequestApprovedEvent($updateRequest)]);
    }

    public function reject(UpdateRequest $updateRequest)
    {
        if ($updateRequest->hasBeenAssessed()) {
            throw new DomainException('该请求已经被处理过');
        }

        $updateRequest->reject();

        $this->updateRequestRepository->store($updateRequest);

        $this->eventDispatcher->dispatch([new UpdateRequestRejectedEvent($updateRequest)]);
    }

    private function ensureCanSubmit(UpdateRequest $updateRequest)
    {
        $pendingRequestCount = $this->updateRequestRepository->getPendingCount(
            $updateRequest->getRequester());
        if ($pendingRequestCount >= self::MAX_ALLOWED_PENDING_REQUEST) {
            throw new DomainException('社团申请正在处理中，请勿重复提交');
        }

        $approvedRequestCount = $this->updateRequestRepository->getApprovedCount(
            $updateRequest->getRequester());
        if ($approvedRequestCount >= self::MAX_ALLOWED_UPDATED_TIMES) {
            throw new DomainException('社团资料修改次数已达上限');
        }
    }
}