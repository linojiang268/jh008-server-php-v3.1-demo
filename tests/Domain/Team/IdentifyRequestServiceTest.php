<?php
namespace Test\Jihe\Domain\Team;

use Jihe\Domain\Team\Events\IdentifyRequestRejectedEvent;
use Jihe\Domain\Team\Team;
use Jihe\Domain\Team\Certification;
use Jihe\Domain\Team\CertificationRepository;
use Jihe\Domain\Team\Events\IdentifyRequestApprovedEvent;
use Jihe\Domain\Team\IdentifyRequest;
use Jihe\Domain\Team\IdentifyRequestRepository;
use Jihe\Domain\Team\IdentifyRequestService;
use Jihe\Domain\User\User;
use Jihe\Events\Dispatcher;
use Prophecy\Argument;
use Test\Jihe\App\DomainServiceTest;

class IdentifyRequestServiceTest extends DomainServiceTest
{
    public function testSubmit_Success()
    {
        $identifyRequest = $this->makeIdentifyRequest(IdentifyRequest::STATUS_PENDING);

        $identifyRequestRepository = $this->prophesize(IdentifyRequestRepository::class);
        $identifyRequestRepository->getPendingCount($identifyRequest->getRequester())
                                  ->shouldBeCalled()->willReturn(0);
        $identifyRequestRepository->store($identifyRequest)->shouldBeCalled();


        $this->makeService($identifyRequestRepository->reveal())->submit($identifyRequest);
    }

    /**
     * @expectedException \Jihe\Domain\DomainException
     * @expectedExceptionMessage 社团申请正在处理中，请勿重复提交
     */
    public function testSubmit_ExistPendingRequest()
    {
        $identifyRequest = $this->makeIdentifyRequest(IdentifyRequest::STATUS_PENDING);

        $identifyRequestRepository = $this->prophesize(IdentifyRequestRepository::class);
        $identifyRequestRepository->getPendingCount($identifyRequest->getRequester())
                                  ->shouldBeCalled()->willReturn(1);


        $this->makeService($identifyRequestRepository->reveal())->submit($identifyRequest);
    }

    public function testApprove_Success()
    {
        $identifyRequest = $this->makeIdentifyRequest(IdentifyRequest::STATUS_PENDING);

        $identifyRequestRepository = $this->prophesize(IdentifyRequestRepository::class);
        $identifyRequestRepository->store(Argument::that(
            function (IdentifyRequest $storedIdentifyRequest) use ($identifyRequest){
                return $storedIdentifyRequest == $identifyRequest                                           &&
                       $storedIdentifyRequest->getStatus() == IdentifyRequest::STATUS_APPROVED              &&
                       $storedIdentifyRequest->getCertifications()->forAll(function ($key, $certification) {
                           return $certification->getStatus() == IdentifyRequest::STATUS_APPROVED;
                       });
            })
        )->shouldBeCalled();

        $eventDispatcher = $this->prophesize(Dispatcher::class);
        $eventDispatcher->dispatch(Argument::that(function (array $events) use ($identifyRequest) {
            return count($events) == 1                                  &&
                   $events[0] instanceof IdentifyRequestApprovedEvent   &&
                   $events[0]->getIdentifyRequest() == $identifyRequest &&
                   $events[0]->getIdentifyRequest()->getCertifications()->forAll(function ($key, $certification) {
                       return $certification->getStatus() == IdentifyRequest::STATUS_APPROVED;
                   });
        }))->shouldBeCalled();

        $this->makeService($identifyRequestRepository->reveal(), null, $eventDispatcher->reveal())
            ->approve($identifyRequest);
    }

    /**
     * @expectedException \Jihe\Domain\DomainException
     * @expectedExceptionMessage 该请求已经被处理
     */
    public function testApprove_Approved()
    {
        $identifyRequest = $this->makeIdentifyRequest(IdentifyRequest::STATUS_APPROVED);

        $this->makeService()->approve($identifyRequest);
    }

    /**
     * @expectedException \Jihe\Domain\DomainException
     * @expectedExceptionMessage 该请求已经被处理
     */
    public function testApprove_Rejected()
    {
        $identifyRequest = $this->makeIdentifyRequest(IdentifyRequest::STATUS_REJECTED);

        $this->makeService()->approve($identifyRequest);
    }

    public function testReject_Success()
    {
        $identifyRequest = $this->makeIdentifyRequest(IdentifyRequest::STATUS_PENDING);

        $identifyRequestRepository = $this->prophesize(IdentifyRequestRepository::class);
        $identifyRequestRepository->store(Argument::that(
            function (IdentifyRequest $storedIdentifyRequest) use ($identifyRequest){
                return $storedIdentifyRequest              == $identifyRequest              &&
                       $storedIdentifyRequest->getStatus() == IdentifyRequest::STATUS_REJECTED &&
                       $storedIdentifyRequest->getCertifications()->forAll(function ($key, $certification) {
                           return $certification->getStatus() == IdentifyRequest::STATUS_REJECTED;
                       });
            })
        )->shouldBeCalled();

        $eventDispatcher = $this->prophesize(Dispatcher::class);
        $eventDispatcher->dispatch(Argument::that(function (array $events) use ($identifyRequest) {
            return count($events) == 1                                  &&
                   $events[0] instanceof IdentifyRequestRejectedEvent   &&
                   $events[0]->getIdentifyRequest() == $identifyRequest &&
                   $events[0]->getIdentifyRequest()->getCertifications()->forAll(function ($key, $certification) {
                       return $certification->getStatus() == IdentifyRequest::STATUS_REJECTED;
                   });
        }))->shouldBeCalled();

        $this->makeService($identifyRequestRepository->reveal(), null, $eventDispatcher->reveal())
            ->reject($identifyRequest);
    }

    /**
     * @expectedException \Jihe\Domain\DomainException
     * @expectedExceptionMessage 该请求已经被处理
     */
    public function testReject_Approved()
    {
        $identifyRequest = $this->makeIdentifyRequest(IdentifyRequest::STATUS_APPROVED);

        $this->makeService()->reject($identifyRequest);
    }

    /**
     * @expectedException \Jihe\Domain\DomainException
     * @expectedExceptionMessage 该请求已经被处理
     */
    public function testReject_Rejected()
    {
        $identifyRequest = $this->makeIdentifyRequest(IdentifyRequest::STATUS_REJECTED);

        $this->makeService()->reject($identifyRequest);
    }

    private function makeIdentifyRequest($status)
    {
        $identifyRequest = new IdentifyRequest();
        $identifyRequest->setStatus($status);
        $identifyRequest->setAssessedAt(null);
        $identifyRequest->setTeam(new Team());
        $certification = new Certification();
        $certification->setStatus($status);
        $certification->setTeam($identifyRequest->getTeam());
        $certification->setRequest($identifyRequest);
        $certification->setType(Certification::TYPE_ID_CARD_BACK);
        $certification->setUrl('http://test_1_certification_1_url');
        $identifyRequest->getCertifications()->add($certification);
        $requester = new User();
        $identifyRequest->setRequester($requester);

        return $identifyRequest;
    }

    private function makeService(IdentifyRequestRepository $identifyRequestRepository = null,
                                 CertificationRepository   $certificationRepository   = null,
                                 Dispatcher                $eventDispatcher           = null)
    {
        if (is_null($identifyRequestRepository)) {
            $identifyRequestRepository = $this->prophesize(IdentifyRequestRepository::class)->reveal();
        }

        $eventDispatcher = $this->morphMockedEventDispatcher($eventDispatcher);

        if (is_null($certificationRepository)) {
            $certificationRepository = $this->prophesize(CertificationRepository::class)->reveal();
        }

        return new IdentifyRequestService($identifyRequestRepository, $certificationRepository, $eventDispatcher);
    }

}