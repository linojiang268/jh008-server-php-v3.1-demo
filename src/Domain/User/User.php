<?php
namespace Jihe\Domain\User;

use Brouwers\LaravelDoctrine\Auth\Authenticatable;
use Brouwers\LaravelDoctrine\Extensions\Timestamps\Timestamps;
use Brouwers\LaravelDoctrine\Extensions\SoftDeletes\SoftDeletes;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;

/**
 * @ORM\Entity
 * @Gedmo\SoftDeleteable
 * @ORM\Table(name="users")
 */
class User implements AuthenticatableContract
{
    const STATUS_INCOMPLETE = 0; // registration is done, but information is incomplete
    const STATUS_NORMAL     = 1; // 正常用户
    const STATUS_FORBIDDEN  = 2; // 封号

    const REGISTERED_BY_SELF   = 0;      // 自己注册
    const REGISTERED_BY_OTHERS = 1;      // 别人添加

    const GENDER_UNKNOWN = 0;    // 未知
    const GENDER_MALE    = 1;    // 男
    const GENDER_FEMALE  = 2;    // 女


    use Authenticatable, Timestamps, SoftDeletes;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     * @ORM\Column(type="guid")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=11)
     */
    protected $mobile;

    /**
     * @ORM\Column(type="smallint")
     */
    protected $registeredBy;

    /**
     * @ORM\Column(type="string", length=32, nullable=true)
     */
    protected $nickName;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    protected $gender;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $birthday;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $signature;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $avatarUrl;

    /**
     * @ORM\Column(type="string", length=16)
     */
    protected $salt;

    /**
     * @ORM\Column(type="smallint")
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
    public function getMobile()
    {
        return $this->mobile;
    }

    /**
     * @param mixed $mobile
     * @return User
     */
    public function setMobile($mobile)
    {
        $this->mobile = $mobile;
    }

    /**
     * @return mixed
     */
    public function getRegisteredBy()
    {
        return $this->registeredBy;
    }

    /**
     * @param mixed $registeredBy
     * @return User
     */
    public function setRegisteredBy($registeredBy)
    {
        $this->registeredBy = $registeredBy;
    }

    /**
     * @return mixed
     */
    public function getNickName()
    {
        return $this->nickName;
    }

    /**
     * @param mixed $nickName
     * @return User
     */
    public function setNickName($nickName)
    {
        $this->nickName = $nickName;
    }

    /**
     * @return mixed
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * @param mixed $gender
     * @return User
     */
    public function setGender($gender)
    {
        $this->gender = $gender;
    }

    /**
     * @return mixed
     */
    public function getBirthday()
    {
        return $this->birthday;
    }

    /**
     * @param mixed $birthday
     * @return User
     */
    public function setBirthday($birthday)
    {
        $this->birthday = $birthday;
    }

    /**
     * @return mixed
     */
    public function getSignature()
    {
        return $this->signature;
    }

    /**
     * @param mixed $signature
     * @return User
     */
    public function setSignature($signature)
    {
        $this->signature = $signature;
    }

    /**
     * @return mixed
     */
    public function getAvatarUrl()
    {
        return $this->avatarUrl;
    }

    /**
     * @param mixed $avatarUrl
     * @return User
     */
    public function setAvatarUrl($avatarUrl)
    {
        $this->avatarUrl = $avatarUrl;
    }

    /**
     * @return mixed
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * @param mixed $salt
     * @return User
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;
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
     * @return User
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }


}