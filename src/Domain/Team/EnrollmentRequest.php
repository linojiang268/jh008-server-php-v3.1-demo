<?php
namespace Jihe\Domain\Team;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity
 * @ORM\Table(name="team_enrollment_requests")
 */
class EnrollmentRequest extends Request
{
    /**
     * @ORM\ManyToOne(targetEntity="Jihe\Domain\City\City")
     */
    protected $city;
    /**
     * @ORM\Column(type="string")
     */
    protected $name;
    /**
     * @ORM\Column(type="string")
     */
    protected $logoUrl;
    /**
     * @ORM\Embedded(class="Contact")
     */
    protected $contact;
    /**
     * @ORM\Column(type="string")
     */
    protected $introduction;

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     * @return EnrollmentRequest
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
     * @return EnrollmentRequest
     */
    public function setLogoUrl($logoUrl)
    {
        $this->logoUrl = $logoUrl;
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
     * @return EnrollmentRequest
     */
    public function setContact($contact)
    {
        $this->contact = $contact;
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
     * @return EnrollmentRequest
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
     * @return EnrollmentRequest
     */
    public function setCity($city)
    {
        $this->city = $city;
    }

    public function approve()
    {
        parent::approve();

        $created = new Team();
        $created->setName($this->name);
        $created->setLogoUrl($this->logoUrl);
        $created->setContact($this->contact);
        $created->setIntroduction($this->introduction);
        $created->setCity($this->city);
        $created->setCreator($this->requester);
        $this->setTeam($created);
    }
}