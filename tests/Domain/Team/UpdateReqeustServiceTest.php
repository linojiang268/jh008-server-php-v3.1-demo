<?php
namespace Test\Jihe\Domain\Team;

use Jihe\Domain\Team\Contact;
use Jihe\Domain\Team\Events\UpdateRequestApprovedEvent;
use Jihe\Domain\Team\Events\UpdateRequestRejectedEvent;
use Jihe\Domain\Team\Team;
use Jihe\Domain\Team\TeamRepository;
use Jihe\Domain\Team\UpdateRequest;
use Jihe\Domain\Team\UpdateRequestRepository;
use Jihe\Domain\Team\UpdateRequestService;
use Jihe\Domain\User\User;
use Jihe\Events\Dispatcher;
use Prophecy\Argument;
use Test\Jihe\App\DomainServiceTest;

class UpdateReqeustServiceTest extends DomainServiceTest
{
    public function testSubmit_Success()
    {
        $updateRequest = $this->makeUpdateRequest(UpdateRequest::STATUS_PENDING);

        $updateRequestRepository = $this->prophesize(UpdateRequestRepository::class);
        $updateRequestRepository->getPendingCount($updateRequest->getRequester())
                                ->shouldBeCalled()->willReturn(0);
        $updateRequestRepository->getApprovedCount($updateRequest->getRequester())
                                ->shouldBeCalled()->willReturn(0);
        $updateRequestRepository->store($updateRequest)->shouldBeCalled();

        $this->makeService($updateRequestRepository->reveal())->submit($updateRequest);
    }

    /**
     * @expectedException \Jihe\Domain\DomainException
     * @expectedExceptionMessage 社团申请正在处理中，请勿重复提交
     */
    public function testSubmit_ExistPendingRequest()
    {
        $updateRequest = $this->makeUpdateRequest(UpdateRequest::STATUS_PENDING);

        $updateRequestRepository = $this->prophesize(UpdateRequestRepository::class);
        $updateRequestRepository->getPendingCount($updateRequest->getRequester())
                                    ->shouldBeCalled()->willReturn(1);

        $this->makeService($updateRequestRepository->reveal())->submit($updateRequest);
    }

    /**
     * @expectedException \Jihe\Domain\DomainException
     * @expectedExceptionMessage 社团资料修改次数已达上限
     */
    public function testSubmit_ExistApprovedRequest()
    {
        $updateRequest = $this->makeUpdateRequest(UpdateRequest::STATUS_PENDING);

        $updateRequestRepository = $this->prophesize(UpdateRequestRepository::class);
        $updateRequestRepository->getPendingCount($updateRequest->getRequester())
                                    ->shouldBeCalled()->willReturn(0);
        $updateRequestRepository->getApprovedCount($updateRequest->getRequester())
                                    ->shouldBeCalled()->willReturn(1);

        $this->makeService($updateRequestRepository->reveal())->submit($updateRequest);
    }

    public function testApprove_Success()
    {
        $updateRequest = $this->makeUpdateRequest(UpdateRequest::STATUS_PENDING);

        $updateRequestRepository = $this->prophesize(UpdateRequestRepository::class);
        $updateRequestRepository->store(Argument::that(
            function (UpdateRequest $storedUpdateRequest) use ($updateRequest) {
                return $storedUpdateRequest              == $updateRequest &&
                       $storedUpdateRequest->getStatus() == UpdateRequest::STATUS_APPROVED;
            })
        )->shouldBeCalled();

        $teamRepository = $this->prophesize(TeamRepository::class);
        $teamRepository->store(Argument::that(function (Team $team) use ($updateRequest) {
            return $team === $updateRequest->getTeam() &&
                   $this->isTeamEqual($team->getName(), $team->getLogoUrl(), $team->getContact(),
                                      $team->getIntroduction(), $updateRequest->getTeam());
        }))->shouldBeCalled();

        $eventDispatcher = $this->prophesize(Dispatcher::class);
        $eventDispatcher->dispatch(Argument::that(function (array $events) use ($updateRequest) {
            return count($events) == 1                                  &&
                   $events[0] instanceof UpdateRequestApprovedEvent &&
                   $events[0]->getUpdateRequest() === $updateRequest;
        }))->shouldBeCalled();

        $this->makeService($updateRequestRepository->reveal(), $teamRepository->reveal(), $eventDispatcher->reveal())
            ->approve($updateRequest);
    }

    /**
     * @expectedException \Jihe\Domain\DomainException
     * @expectedExceptionMessage 该请求已经被处理
     */
    public function testApprove_Approved()
    {
        $updateRequest = $this->makeUpdateRequest(UpdateRequest::STATUS_APPROVED);

        $this->makeService()->approve($updateRequest);
    }

    /**
     * @expectedException \Jihe\Domain\DomainException
     * @expectedExceptionMessage 该请求已经被处理
     */
    public function testApprove_Rejected()
    {
        $updateRequest = $this->makeUpdateRequest(UpdateRequest::STATUS_REJECTED);

        $this->makeService()->approve($updateRequest);
    }

    public function testReject_Success()
    {
        $updateRequest = $this->makeUpdateRequest(UpdateRequest::STATUS_PENDING);

        $updateRequestRepository = $this->prophesize(UpdateRequestRepository::class);
        $updateRequestRepository->store(Argument::that(
            function (UpdateRequest $storedUpdateRequest) use ($updateRequest) {
                return $storedUpdateRequest              == $updateRequest &&
                       $storedUpdateRequest->getStatus() == UpdateRequest::STATUS_REJECTED;
            })
        )->shouldBeCalled();

        $eventDispatcher = $this->prophesize(Dispatcher::class);
        $eventDispatcher->dispatch(Argument::that(function (array $events) use ($updateRequest) {
            return count($events) == 1                              &&
                   $events[0] instanceof UpdateRequestRejectedEvent &&
                   $events[0]->getUpdateRequest() === $updateRequest;
        }))->shouldBeCalled();

        $this->makeService($updateRequestRepository->reveal(), null, $eventDispatcher->reveal())
            ->reject($updateRequest);
    }

    /**
     * @expectedException \Jihe\Domain\DomainException
     * @expectedExceptionMessage 该请求已经被处理
     */
    public function testReject_Approved()
    {
        $updateRequest = $this->makeUpdateRequest(UpdateRequest::STATUS_APPROVED);

        $this->makeService()->reject($updateRequest);
    }

    /**
     * @expectedException \Jihe\Domain\DomainException
     * @expectedExceptionMessage 该请求已经被处理
     */
    public function testReject_Rejected()
    {
        $updateRequest = $this->makeUpdateRequest(UpdateRequest::STATUS_REJECTED);

        $this->makeService()->reject($updateRequest);
    }

    private function isTeamEqual($name, $logoUrl, Contact $contact, $introduction, Team $team)
    {
        return $team->getName()          == $name         &&
               $team->getLogoUrl()       == $logoUrl      &&
               $team->getIntroduction()  == $introduction &&
               $team->getContact()       == $contact;
    }

    private function makeUpdateRequest($status)
    {
        $updateRequest = new UpdateRequest();
        $updateRequest->setStatus($status);
        $updateRequest->setIntroduction('test_introduction');
        $updateRequest->setAssessedAt(null);
        $updateRequest->setName('test_name');
        $updateRequest->setLogoUrl('test_logo_url');
        $contact = new Contact();
        $contact->setName('test_contact_name');
        $contact->setAddress('test_contact_address');
        $contact->setEmail('test_contact_email');
        $contact->setPhone('test_contact_phone');
        $updateRequest->setContact($contact);
        $updateRequest->setLogoUrl('test_logo_url');
        $requester = new User();
        $updateRequest->setRequester($requester);
        $team = new Team();
        $updateRequest->setTeam($team);

        return $updateRequest;
    }

    private function makeService(UpdateRequestRepository $updateRepository = null,
                                 TeamRepository          $teamRepository = null,
                                 Dispatcher              $eventDispatcher = null)
    {
        if (is_null($updateRepository)) {
            $updateRepository = $this->prophesize(UpdateRequestRepository::class)->reveal();
        }

        $eventDispatcher = $this->morphMockedEventDispatcher($eventDispatcher);

        if (is_null($teamRepository)) {
            $teamRepository = $this->prophesize(TeamRepository::class)->reveal();
        }

        return new UpdateRequestService($updateRepository, $teamRepository, $eventDispatcher);
    }
}