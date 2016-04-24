<?php
namespace Jihe\Domain\Team;

use Brouwers\LaravelDoctrine\Extensions\Timestamps\Timestamps;
use Brouwers\LaravelDoctrine\Extensions\SoftDeletes\SoftDeletes;
use Carbon\Carbon;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity
 * @ORM\InheritanceType(value="JOINED")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\DiscriminatorMap(value={
 *      "team_enrollment"="EnrollmentRequest",
 *      "team_update"="UpdateRequest",
 *      "team_identify"="IdentifyRequest"})
 * @ORM\Table(name="requests")
 */
abstract class Request
{
    use SoftDeletes, Timestamps;

    const STATUS_PENDING  = 0;// the request is submitted, but not assessed
    const STATUS_APPROVED = 1;// the request is approved
    const STATUS_REJECTED = 2;// the request is rejected

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="guid")
     */
    protected $id;
    /**
     * @ORM\ManyToOne(targetEntity="Jihe\Domain\User\User")
     */
    protected $requester;
    /**
     * @ORM\ManyToOne(targetEntity="Team", inversedBy="requests")
     */
    private $team;
    /**
     * @ORM\Column(type="smallint")
     */
    protected $status = self::STATUS_PENDING;
    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $assessedAt;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     * @return Request
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return mixed
     */
    public function getAssessedAt()
    {
        return $this->assessedAt;
    }

    /**
     * @param mixed $assessedAt
     * @return Request
     */
    public function setAssessedAt($assessedAt)
    {
        $this->assessedAt = $assessedAt;
    }

    /**
     * @return mixed
     */
    public function getRequester()
    {
        return $this->requester;
    }

    /**
     * @param mixed $requester
     * @return Request
     */
    public function setRequester($requester)
    {
        $this->requester = $requester;
    }

    /**
     * @return mixed
     */
    public function getTeam()
    {
        return $this->team;
    }

    /**
     * @param mixed $team
     * @return Request
     */
    public function setTeam($team)
    {
        $this->team = $team;
    }

    /**
     * check whether this request has been approved or rejected
     *
     * @return bool
     */
    public function hasBeenAssessed()
    {
        return $this->getStatus() != self::STATUS_PENDING;
    }


    public function reject()
    {
        $this->setAssessedAt(Carbon::now());
        $this->setStatus(self::STATUS_REJECTED);
    }

    public function approve()
    {
        $this->setAssessedAt(Carbon::now());
        $this->setStatus(self::STATUS_APPROVED);
    }
}
