<?php
namespace Jihe\Domain\Team;

use Jihe\Domain\DomainException;
use Jihe\Domain\Team\Events\IdentifyRequestApprovedEvent;
use Jihe\Domain\Team\Events\IdentifyRequestRejectedEvent;
use Jihe\Events\Dispatcher;

class IdentifyRequestService
{
    /**
     * can request for identify only when there is no request pending
     */
    const MAX_ALLOWED_PENDING_REQUEST = 1;

    /**
     * @var IdentifyRequestRepository
     */
    private $identifyRequestRepository;
    /**
     * @var CertificationRepository
     */
    private $certificationRepository;
    /**
     * @var Dispatcher
     */
    private $eventDispatcher;

    function __construct(IdentifyRequestRepository $identifyRequestRepository,
                         CertificationRepository $certificationRepository,
                         Dispatcher $eventDispatcher)
    {
        $this->identifyRequestRepository = $identifyRequestRepository;
        $this->certificationRepository = $certificationRepository;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function submit(IdentifyRequest $identifyRequest)
    {
        $this->ensureCanSubmit($identifyRequest);
        $this->identifyRequestRepository->store($identifyRequest);
    }

    public function approve(IdentifyRequest $identifyRequest)
    {
        if ($identifyRequest->hasBeenAssessed()) {
            throw new DomainException('该请求已经被处理');
        }

        $identifyRequest->approve();

        foreach ($identifyRequest->getCertifications() as $certification) {
            $this->certificationRepository->store($certification);
        }
        $this->identifyRequestRepository->store($identifyRequest);

        $this->eventDispatcher->dispatch([new IdentifyRequestApprovedEvent($identifyRequest)]);
    }

    public function reject(IdentifyRequest $identifyRequest)
    {
        if ($identifyRequest->hasBeenAssessed()) {
            throw new DomainException('该请求已经被处理');
        }

        $identifyRequest->reject();

        foreach ($identifyRequest->getCertifications() as $certification) {
            $this->certificationRepository->store($certification);
        }
        $this->identifyRequestRepository->store($identifyRequest);

        $this->eventDispatcher->dispatch([new IdentifyRequestRejectedEvent($identifyRequest)]);
    }

    private function ensureCanSubmit(IdentifyRequest $enrollmentRequest)
    {
        // there is only a pending request can be exist
        $pendingRequestCount = $this->identifyRequestRepository->getPendingCount(
            $enrollmentRequest->getRequester());
        if ($pendingRequestCount >= self::MAX_ALLOWED_PENDING_REQUEST) {
            throw new DomainException('社团申请正在处理中，请勿重复提交');
        }
    }
}