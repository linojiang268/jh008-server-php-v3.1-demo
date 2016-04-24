<?php
namespace Test\Jihe\Domain\Team\Handlers;

use Jihe\Domain\City\City;
use Jihe\Domain\City\CityRepository;
use Jihe\Domain\Team\EnrollmentRequest;
use Jihe\Domain\Team\EnrollmentRequestService;
use Jihe\Domain\Team\Handlers\CreateTeamEnrollmentReqeustHandler;
use Jihe\Domain\User\User;
use Jihe\Domain\User\UserRepository;
use Jihe\Domain\Team\Validators\CreateTeamEnrollmentRequestValidator;
use Jihe\Infrastructure\Storage\StorageService;
use Prophecy\Argument;
use Test\Jihe\App\HandlerTest;

class CreateTeamEnrollmentRequestHandlerTest extends HandlerTest
{
    public function testHandle_Success()
    {
        $request = $this->makeReqeust();

        $userRepository = $this->prophesize(UserRepository::class);
        $userRepository->find($request['requester_id'])->shouldBeCalled()->willReturn(new User());

        $cityRepository = $this->prophesize(CityRepository::class);
        $cityRepository->find($request['city_id'])->shouldBeCalled()->willReturn(new City());

        $enrollmentRequestService = $this->prophesize(EnrollmentRequestService::class);
        $enrollmentRequestService->submit(Argument::that(function (EnrollmentRequest $enrollmentRequest) use ($request) {
            return $enrollmentRequest->getName()                  == $request['name']            &&
                   $enrollmentRequest->getLogoUrl()               == $request['logo_url']        &&
                   $enrollmentRequest->getContact()->getName()    == $request['contact_name']    &&
                   $enrollmentRequest->getContact()->getPhone()   == $request['contact_phone']   &&
                   $enrollmentRequest->getContact()->getAddress() == $request['contact_address'] &&
                   $enrollmentRequest->getIntroduction()          == $request['introduction'];
        }));

        $storageService = $this->prophesize(StorageService::class);
        $storageService->isTmp($request['logo_url'])->shouldBeCalled()->willReturn(true);
        $storageService->copy($request['logo_url'])->shouldBeCalled();

        $this->makeHandler($enrollmentRequestService->reveal(), $userRepository->reveal(),
                           $cityRepository->reveal(), $storageService->reveal())->handle($request);
    }

    /**
     * @expectedException \Jihe\Domain\DomainException
     * @expectedExceptionMessage 非法城市
     */
    public function testHandle_CityNotExists()
    {
        $request = $this->makeReqeust();

        $userRepository = $this->prophesize(UserRepository::class);
        $userRepository->find($request['requester_id'])->shouldBeCalled()->willReturn(new User());

        $cityRepository = $this->prophesize(CityRepository::class);
        $cityRepository->find($request['city_id'])->shouldBeCalled()->willReturn(null);

        $this->makeHandler(null, $userRepository->reveal(), $cityRepository->reveal(), null)->handle($request);
    }

    private function makeReqeust()
    {
        return [
            'requester_id'    => 'test_user_id',
            'city_id'         => 'test_city_id',
            'name'            => 'test_name',
            'logo_url'        => 'test_logo_url',
            'contact_name'    => 'test_contact_name',
            'contact_phone'   => 'test_contact_phone',
            'contact_address' => 'test_contact_address',
            'introduction'    => 'test_introduction'
        ];
    }

    private function makeHandler(EnrollmentRequestService $enrollmentRequestService = null,
                                 UserRepository           $userRepository,
                                 CityRepository           $cityRepository,
                                 StorageService           $storageService           = null)
    {
        $validator = $this->prophesize(CreateTeamEnrollmentRequestValidator::class);
        $validator->validate(Argument::cetera())->shouldBeCalled();

        if (is_null($enrollmentRequestService)) {
            $enrollmentRequestService = $this->prophesize(EnrollmentRequestService::class)->reveal();
        }

        if (is_null($storageService)) {
            $storageService = $this->prophesize(StorageService::class)->reveal();
        }

        return new CreateTeamEnrollmentReqeustHandler($validator->reveal(), $enrollmentRequestService,
                                         $userRepository, $cityRepository, $storageService);
    }
}