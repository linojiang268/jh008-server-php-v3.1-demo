<?php
namespace Jihe\Domain\Vcode;

use Brouwers\LaravelDoctrine\Extensions\SoftDeletes\SoftDeletes;
use Brouwers\LaravelDoctrine\Extensions\Timestamps\Timestamps;
use Carbon\Carbon;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity
 * @Gedmo\SoftDeleteable
 * @ORM\Table(name="sms_vcodes")
 */
class SmsVcode
{

    /**
     * default expire interval (in seconds)
     * @var int
     */
    const DEFAULT_EXPIRE_INTERVAL  =  600;

    /**
     * defalult expire
     */
    const DEFAULT_SURVIVAL_TIME = 600;

    use Timestamps, SoftDeletes;

    /**
     * @var string
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     * @ORM\Column(type="guid")
     */
    protected $id;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    protected $mobile;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    protected $code;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime")
     */
    protected $expiredAt;

    /**
     * @var int
     * @ORM\Column(type="integer")
     * @ORM\Version
     */
    protected $version;

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }


    /**
     * @return string
     */
    public function getMobile()
    {
        return $this->mobile;
    }

    /**
     * @param string $mobile
     */
    public function setMobile($mobile)
    {
        $this->mobile = $mobile;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param string $code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * @return \DateTime
     */
    public function getExpiredAt()
    {
        return $this->expiredAt;
    }

    /**
     * @return int
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @param int $version
     */
    public function setVersion($version)
    {
        $this->version = $version;
    }

    /**
     * @param \DateTime $expiredAt
     */
    public function setExpiredAt($expiredAt)
    {
        $this->expiredAt = $expiredAt;
    }

    public function isExpired(\DateTime $deadline = null)
    {
        $deadline = $deadline ?: Carbon::now();

        return $deadline > $this->expiredAt;
    }
}