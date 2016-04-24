<?php
namespace Jihe\Domain\Team;

use Brouwers\LaravelDoctrine\Extensions\Timestamps\Timestamps;
use Brouwers\LaravelDoctrine\Extensions\SoftDeletes\SoftDeletes;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity
 * @ORM\Table(name = "teams")
 * @Gedmo\SoftDeleteable
 */
class Team
{
    use SoftDeletes, Timestamps;

    /**
     * @ORM\Id
     * @ORM\Column(type="guid")
     * @ORM\GeneratedValue(strategy="UUID")
     */
    protected $id;
    /**
     * @ORM\Column(type="string")
     */
    protected $name;
    /**
     * @ORM\Column(type="string")
     */
    protected $logoUrl;
    /**
     * @ORM\Column(type="string")
     */
    protected $introduction;
    /**
     * @ORM\Embedded(class="Contact")
     * @var Contact
     */
    protected $contact;
    /**
     * @ORM\ManyToOne(targetEntity="Jihe\Domain\City\City")
     * @var \Jihe\Domain\City\City
     */
    protected $city;
    /**
     * @ORM\ManyToOne(targetEntity="Jihe\Domain\User\User")
     * @var \Jihe\Domain\User\User
     */
    protected $creator;
    /**
     * @ORM\OneToMany(targetEntity="Request", mappedBy="team", fetch="EXTRA_LAZY")
     * @var ArrayCollection
     */
    protected $requests;
    /**
     * @ORM\OneToMany(targetEntity="Certification", mappedBy="team", fetch="EXTRA_LAZY")
     * @var ArrayCollection
     */
    protected $certifications;

    function __construct()
    {
        $this->requests = new ArrayCollection();
        $this->certifications = new ArrayCollection();
    }

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
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     * @return Team
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getLogoUrl()
    {
        return $this->logoUrl;
    }

    /**
     * @param mixed $logoUrl
     * @return Team
     */
    public function setLogoUrl($logoUrl)
    {
        $this->logoUrl = $logoUrl;
    }

    /**
     * @return mixed
     */
    public function getIntroduction()
    {
        return $this->introduction;
    }

    /**
     * @param mixed $introduction
     * @return Team
     */
    public function setIntroduction($introduction)
    {
        $this->introduction = $introduction;
    }

    /**
     * @return mixed
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param mixed $city
     * @return Team
     */
    public function setCity($city)
    {
        $this->city = $city;
    }

    /**
     * @return mixed
     */
    public function getCreator()
    {
        return $this->creator;
    }

    /**
     * @param mixed $creator
     * @return Team
     */
    public function setCreator($creator)
    {
        $this->creator = $creator;
    }

    /**
     * @return mixed
     */
    public function getContact()
    {
        return $this->contact;
    }

    /**
     * @param mixed $contact
     * @return Team
     */
    public function setContact($contact)
    {
        $this->contact = $contact;
    }

    /**
     * @return mixed
     */
    public function getRequests()
    {
        return $this->requests;
    }

    /**
     * @param mixed $requests
     * @return Team
     */
    public function setRequests($requests)
    {
        $this->requests = $requests;
    }

    /**
     * @param int $page the page index of the certifications, default 1
     * @param int $size the page size of the certifications, defalut 15
     * @return mixed
     */
    public function getCertifications($page = 1, $size = 15)
    {
        $criteria = Criteria::create()
            ->where(Criteria::expr()->eq("status", Certification::STATUS_ASSESS_APPROVED))
            ->orderBy(array("created_at" => Criteria::ASC))
            ->setFirstResult(($page - 1) * $size)
            ->setMaxResults($size);
        return $this->certifications->matching($criteria);
    }

    /**
     * @return integer
     */
    public function getCertificationsCount()
    {
        return $this->certifications->count();
    }

    /**
     * @param ArrayCollection $certifications
     * @return Team
     */
    public function setCertifications(ArrayCollection $certifications)
    {
        $this->certifications = $certifications;
    }

    /**
     * @param Certification $certification
     * @return Team
     */
    public function addCertifications(Certification $certification)
    {
        $this->certifications->add($certification);
    }

}