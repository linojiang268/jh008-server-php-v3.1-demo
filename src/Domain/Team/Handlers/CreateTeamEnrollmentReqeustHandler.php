<?php
namespace Jihe\Domain\Team\Handlers;

use Jihe\Domain\ActionHandler;
use Jihe\Domain\City\CityRepository;
use Jihe\Domain\DomainException;
use Jihe\Domain\Team\Contact;
use Jihe\Domain\Team\EnrollmentRequest;
use Jihe\Domain\Team\EnrollmentRequestService;
use Jihe\Domain\User\UserRepository;
use Jihe\Domain\Team\Validators\CreateTeamEnrollmentRequestValidator;
use Jihe\Infrastructure\Storage\StorageService;

class CreateTeamEnrollmentReqeustHandler implements ActionHandler
{
    /**
     * @var CreateTeamEnrollmentRequestValidator
     */
    private $validator;
    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var CityRepository
     */
    private $cityRepository;
    /**
     * @var EnrollmentRequestService
     */
    private $enrollmentRequestService;
    /**
     * @var StorageService
     */
    private $storageService;

    function __construct(CreateTeamEnrollmentRequestValidator  $validator,
                         EnrollmentRequestService $enrollmentRequestService,
                         UserRepository           $userRepository,
                         CityRepository           $cityRepository,
                         StorageService           $storageService)
    {
        $this->validator                = $validator;
        $this->enrollmentRequestService = $enrollmentRequestService;
        $this->userRepository           = $userRepository;
        $this->cityRepository           = $cityRepository;
        $this->storageService           = $storageService;
    }

    /**
     * Before actually get a team enrolled into the system, a request for that should be issued
     * by a team leader (and thus becomes the creator of that team once the request is approved).
     * This service receives the request for new team creation.
     *
     * @param array $request  detail of a request for enrollment, keys taken:
     *                                  - requester_id       (int) who initiates this request
     *                                  - city_id            (int) the team will be in which city
     *                                  - name               (string) name of the team
     *                                  - logo_url           (string) url of the team's logo
     *                                  - contact_address    (string) detailed address of the team
     *                                  - contact_phone      (string) contact number
     *                                  - contact_name       (string) name of the contact
     *                                  - introduction       (string) brief introduction
     *
     * @throws \Exception         if the enrollment request is rejected immediately
     *
     * @return int                id of the accepted enrollment request
     */
    public function handle(array $request = [])
    {
        $this->validator->validate($request);

        $enrollmentRequest = new EnrollmentRequest();
        $requester = $this->userRepository->find($request['requester_id']);
        $enrollmentRequest->setRequester($requester);
        $city = $this->cityRepository->find($request['city_id']);
        if ($city == null) {
            throw new DomainException('非法城市');
        }
        $enrollmentRequest->setCity($city);
        $enrollmentRequest->setName($request['name']);
        $logoUrl = $this->morphLogUrl($request['logo_url']);
        $enrollmentRequest->setLogoUrl($logoUrl);
        $contact = new Contact();
        $contact->setName($request['contact_name']);
        $contact->setPhone($request['contact_phone']);
        $contact->setAddress($request['contact_address']);
        $enrollmentRequest->setContact($contact);
        $enrollmentRequest->setIntroduction($request['introduction']);

        $this->enrollmentRequestService->submit($enrollmentRequest);
    }

    private function morphLogUrl($logoUrl)
    {
        if (!is_null($logoUrl) && $this->storageService->isTmp($logoUrl)) {
            $logoUrl = $this->storageService->copy($logoUrl);
        }
        return $logoUrl;
    }
}