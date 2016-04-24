<?php
namespace Test\Jihe\Domain\Team\Handlers;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Guard;
use Jihe\Domain\Team\EnrollmentRequest;
use Jihe\Domain\Team\EnrollmentRequestRepository;
use Jihe\Domain\Team\EnrollmentRequestService;
use Jihe\Domain\Team\Handlers\TeamEnrollmentRequestApproveHandler;
use Jihe\Domain\Team\Validators\TeamEnrollmentRequestApproveValidator;
use Test\Jihe\App\HandlerTest;

class TeamEnrollmentRequestApproveHandlerTest extends HandlerTest
{

    public function testHandle_Success()
    {
//        $request = $this->makeReqeust();
//
//        $enrollmentRequestRepository = $this->prophesize(EnrollmentRequestRepository::class);
//        $enrollmentRequest = new EnrollmentRequest();
//        $enrollmentRequest->setRequester();
//        $enrollmentRequestRepository->find($request['id'])->shouldBeCalled()->willReturn($enrollmentRequest);
//
//        $auth = $this->prophesize(Guard::class);
//        $authenticatable = $this->prophesize(Authenticatable::class);
//        $authenticatable->getAuthIdentifier()->shouldBeCalled()->willReturn('test_requester_id');
//        $auth->user()->shouldBeCalled()->willReturn($authenticatable->reveal());
//
//        $enrollmentRequestService = $this->prophesize(EnrollmentRequestService::class);
//        $enrollmentRequestService->approve(
//            Argument::that(function (EnrollmentRequest $approvedEnrollmentRequest) use ($enrollmentRequest) {
//                return $approvedEnrollmentRequest == $enrollmentRequest &&
//                    $approvedEnrollmentRequest->getRequester();
//            })
//        );
//
//        $this->makeHandler($enrollmentRequestRepository->reveal(), $enrollmentRequestService->reveal(), $auth->reveal())
//             ->handle($request);
    }

//    /**
//     * @expectedException \Jihe\Domain\DomainException
//     * @expectedExceptionMessage 非法城市
//     */
//    public function testHandle_CityNotExists()
//    {
//        $request = $this->makeReqeust();
//
//        $userRepository = $this->prophesize(UserRepository::class);
//        $userRepository->find($request['requester_id'])->shouldBeCalled()->willReturn(new User());
//
//        $cityRepository = $this->prophesize(CityRepository::class);
//        $cityRepository->find($request['city_id'])->shouldBeCalled()->willReturn(null);
//
//        $this->makeHandler(null, $userRepository->reveal(), $cityRepository->reveal(), null)->handle($request);
//    }

    private function makeReqeust()
    {
        return [
            'id' => 'test_team_enrollment_request_id'
        ];
    }

    private function makeHandler(EnrollmentRequestRepository          $enrollmentRequestRepository,
                                 EnrollmentRequestService             $enrollmentRequestService    =   null)
    {
        $validator = $this->prophesize(TeamEnrollmentRequestApproveValidator::class);
        $validator->validate(Argument::cetera())->shouldBeCalled();

        if (is_null($enrollmentRequestRepository)) {
            $enrollmentRequestRepository = $this->prophesize(EnrollmentRequestRepository::class)->reveal();
        }

        if (is_null($enrollmentRequestService)) {
            $enrollmentRequestService = $this->prophesize(EnrollmentRequestService::class)->reveal();
        }

        $auth = $this->prophesize(Guard::class)->reveal();

        return new TeamEnrollmentRequestApproveHandler($validator->reveal(), $enrollmentRequestRepository,
                                                       $enrollmentRequestService, $auth);
    }
}