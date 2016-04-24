<?php
namespace Test\Jihe\Domain\Team;

use Jihe\Domain\Team\Contact;
use Jihe\Domain\Team\EnrollmentRequest;
use Jihe\Domain\Team\EnrollmentRequestRepository;
use Jihe\Domain\Team\EnrollmentRequestService;
use Jihe\Domain\Team\Events\EnrollmentRequestRejectedEvent;
use Jihe\Domain\Team\Events\EnrollmentRequestApprovedEvent;
use Jihe\Domain\Team\Team;
use Jihe\Domain\Team\TeamRepository;
use Jihe\Domain\User\User;
use Jihe\Events\Dispatcher;
use Prophecy\Argument;
use Test\Jihe\App\DomainServiceTest;

class EnrollmentRequestServiceTest extends DomainServiceTest
{
    public function testSubmit_Success()
    {
        $enrollmentRequest = $this->makeEnrollmentRequest(EnrollmentRequest::STATUS_PENDING);

        $enrollmentRequestRepository = $this->prophesize(EnrollmentRequestRepository::class);
        $enrollmentRequestRepository->getPendingCount($enrollmentRequest->getRequester())
                                    ->shouldBeCalled()->willReturn(0);
        $enrollmentRequestRepository->getApprovedCount($enrollmentRequest->getRequester())
                                    ->shouldBeCalled()->willReturn(0);
        $enrollmentRequestRepository->store($enrollmentRequest)->shouldBeCalled();


        $this->makeService($enrollmentRequestRepository->reveal())->submit($enrollmentRequest);
    }

    /**
     * @expectedException \Jihe\Domain\DomainException
     * @expectedExceptionMessage 社团申请正在处理中，请勿重复提交
     */
    public function testSubmit_ExistPendingRequest()
    {
        $enrollmentRequest = $this->makeEnrollmentRequest(EnrollmentRequest::STATUS_PENDING);

        $enrollmentRequestRepository = $this->prophesize(EnrollmentRequestRepository::class);
        $enrollmentRequestRepository->getPendingCount($enrollmentRequest->getRequester())
                                    ->shouldBeCalled()->willReturn(1);

        $this->makeService($enrollmentRequestRepository->reveal())->submit($enrollmentRequest);
    }

    /**
     * @expectedException \Jihe\Domain\DomainException
     * @expectedExceptionMessage 您已经创建了1个社团，不能创建更多社团
     */
    public function testSubmit_ExistApprovedRequest()
    {
        $enrollmentRequest = $this->makeEnrollmentRequest(EnrollmentRequest::STATUS_PENDING);

        $enrollmentRequestRepository = $this->prophesize(EnrollmentRequestRepository::class);
        $enrollmentRequestRepository->getPendingCount($enrollmentRequest->getRequester())
                                    ->shouldBeCalled()->willReturn(0);
        $enrollmentRequestRepository->getApprovedCount($enrollmentRequest->getRequester())
                                    ->shouldBeCalled()->willReturn(1);

        $this->makeService($enrollmentRequestRepository->reveal())->submit($enrollmentRequest);
    }

    public function testApprove_Success()
    {
        $enrollmentRequest = $this->makeEnrollmentRequest(EnrollmentRequest::STATUS_PENDING);

        $enrollmentRequestRepository = $this->prophesize(EnrollmentRequestRepository::class);
        $enrollmentRequestRepository->store(Argument::that(
            function (EnrollmentRequest $storedEnrollmentRequest) use ($enrollmentRequest) {
                return $storedEnrollmentRequest              == $enrollmentRequest &&
                       $storedEnrollmentRequest->getStatus() == EnrollmentRequest::STATUS_APPROVED;
            })
        )->shouldBeCalled();

        $teamRepository = $this->prophesize(TeamRepository::class);
        $teamRepository->store(Argument::that(function (Team $team) use ($enrollmentRequest) {
            return $team === $enrollmentRequest->getTeam() &&
                   $this->isTeamEqual($team->getName(), $team->getLogoUrl(), $team->getContact(),
                                      $team->getIntroduction(), $enrollmentRequest->getTeam());
        }))->shouldBeCalled();

        $eventDispatcher = $this->prophesize(Dispatcher::class);
        $eventDispatcher->dispatch(Argument::that(function (array $events) use ($enrollmentRequest) {
            return count($events) == 1                                  &&
                   $events[0] instanceof EnrollmentRequestApprovedEvent &&
                   $events[0]->getEnrollmentRequest() === $enrollmentRequest;
        }))->shouldBeCalled();

        $this->makeService($enrollmentRequestRepository->reveal(), $teamRepository->reveal(), $eventDispatcher->reveal())
            ->approve($enrollmentRequest);
    }

    /**
     * @expectedException \Jihe\Domain\DomainException
     * @expectedExceptionMessage 该请求已经被处理
     */
    public function testApprove_Approved()
    {
        $enrollmentRequest = $this->makeEnrollmentRequest(EnrollmentRequest::STATUS_APPROVED);

        $this->makeService()->approve($enrollmentRequest);
    }

    /**
     * @expectedException \Jihe\Domain\DomainException
     * @expectedExceptionMessage 该请求已经被处理
     */
    public function testApprove_Rejected()
    {
        $enrollmentRequest = $this->makeEnrollmentRequest(EnrollmentRequest::STATUS_REJECTED);

        $this->makeService()->approve($enrollmentRequest);
    }

    public function testReject_Success()
    {
        $enrollmentRequest = $this->makeEnrollmentRequest(EnrollmentRequest::STATUS_PENDING);

        $enrollmentRequestRepository = $this->prophesize(EnrollmentRequestRepository::class);
        $enrollmentRequestRepository->store(Argument::that(
            function (EnrollmentRequest $storedEnrollmentRequest) use ($enrollmentRequest) {
                return $storedEnrollmentRequest              == $enrollmentRequest &&
                       $storedEnrollmentRequest->getStatus() == EnrollmentRequest::STATUS_REJECTED;
            })
        )->shouldBeCalled();

        $eventDispatcher = $this->prophesize(Dispatcher::class);
        $eventDispatcher->dispatch(Argument::that(function (array $events) use ($enrollmentRequest) {
            return count($events) == 1                                  &&
                   $events[0] instanceof EnrollmentRequestRejectedEvent &&
                   $events[0]->getEnrollmentRequest() === $enrollmentRequest;
        }))->shouldBeCalled();

        $this->makeService($enrollmentRequestRepository->reveal(), null, $eventDispatcher->reveal())
            ->reject($enrollmentRequest);
    }

    /**
     * @expectedException \Jihe\Domain\DomainException
     * @expectedExceptionMessage 该请求已经被处理
     */
    public function testReject_Approved()
    {
        $enrollmentRequest = $this->makeEnrollmentRequest(EnrollmentRequest::STATUS_APPROVED);

        $this->makeService()->reject($enrollmentRequest);
    }

    /**
     * @expectedException \Jihe\Domain\DomainException
     * @expectedExceptionMessage 该请求已经被处理
     */
    public function testReject_Rejected()
    {
        $enrollmentRequest = $this->makeEnrollmentRequest(EnrollmentRequest::STATUS_REJECTED);

        $this->makeService()->reject($enrollmentRequest);
    }

    private function isTeamEqual($name, $logoUrl, Contact $contact, $introduction, Team $team)
    {
        return $team->getName()          == $name         &&
               $team->getLogoUrl()       == $logoUrl      &&
               $team->getIntroduction()  == $introduction &&
               $team->getContact()       == $contact;

    }

    private function makeEnrollmentRequest($status)
    {
        $enrollmentRequest = new EnrollmentRequest();
        $enrollmentRequest->setStatus($status);
        $enrollmentRequest->setIntroduction('test_introduction');
        $enrollmentRequest->setAssessedAt(null);
        $enrollmentRequest->setName('test_name');
        $enrollmentRequest->setLogoUrl('test_logo_url');
        $contact = new Contact();
        $contact->setName('test_contact_name');
        $contact->setAddress('test_contact_address');
        $contact->setEmail('test_contact_email');
        $contact->setPhone('test_contact_phone');
        $enrollmentRequest->setContact($contact);
        $enrollmentRequest->setLogoUrl('test_logo_url');
        $requester = new User();
        $enrollmentRequest->setRequester($requester);

        return $enrollmentRequest;
    }

    private function makeService(EnrollmentRequestRepository $enrollmentRequestRepository = null,
                                 TeamRepository              $teamRepository              = null,
                                 Dispatcher                  $eventDispatcher             = null)
    {
        if (is_null($enrollmentRequestRepository)) {
            $enrollmentRequestRepository = $this->prophesize(EnrollmentRequestRepository::class)->reveal();
        }

        $eventDispatcher = $this->morphMockedEventDispatcher($eventDispatcher);

        if (is_null($teamRepository)) {
            $teamRepository = $this->prophesize(TeamRepository::class)->reveal();
        }

        return new EnrollmentRequestService($enrollmentRequestRepository, $teamRepository, $eventDispatcher);
    }

}