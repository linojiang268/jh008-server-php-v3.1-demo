<?php
namespace Jihe\Domain\Team;

use Brouwers\LaravelDoctrine\Extensions\Timestamps\Timestamps;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity
 * @ORM\Table(name="team_certifications")
 */
class Certification
{
    use Timestamps;

    const TYPE_ID_CARD_FRONT          = 0;// 身份证正面
    const TYPE_ID_CARD_BACK           = 1;// 身份证反面
    const TYPE_BUSSINESS_CERTIFICATES = 2;// 营业相关证件

    const STATUS_ASSESS_PENDING  = 0;// assess waiting
    const STATUS_ASSESS_APPROVED = 1;// assess approved, can be used
    const STATUS_ASSESS_REJECTED = 2;// assess rejected, is invalid

    /**
     * @ORM\Id
     * @ORM\Column(type="guid")
     * @ORM\GeneratedValue(strategy="UUID")
     */
    protected $id;
    /**
     * @ORM\Column(type="string")
     */
    protected $url;
    /**
     * @ORM\Column(type="integer")
     */
    protected $type;
    /**
     * @ORM\ManyToOne(targetEntity="Team", inversedBy="certifications")
     * @var Team
     */
    protected $team;
    /**
     * @ORM\ManyToOne(targetEntity="IdentifyRequest", inversedBy="certifications")
     * @var IdentifyRequest
     */
    protected $request;
    /**
     * @ORM\Column(type="integer")
     */
    protected $status;

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
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param mixed $url
     * @return Certification
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     * @return Certification
     */
    public function setType($type)
    {
        $this->type = $type;
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
     * @return Certification
     */
    public function setTeam($team)
    {
        $this->team = $team;
    }

    /**
     * @return mixed
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @param mixed $request
     * @return Certification
     */
    public function setRequest($request)
    {
        $this->request = $request;
    }

    /**
     * @param mixed $status
     * @return Certification
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }
}