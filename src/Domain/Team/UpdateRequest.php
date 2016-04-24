<?php
namespace Jihe\Domain\Team;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity
 * @ORM\Table(name="team_update_requests")
 */
class UpdateRequest extends Request
{
    /**
     * @ORM\Column(type="string", length=32)
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

    public function approve()
    {
        parent::approve();

        $this->getTeam()->setName($this->name);
        $this->getTeam()->setLogoUrl($this->logoUrl);
        $this->getTeam()->setContact($this->contact);
        $this->getTeam()->setIntroduction($this->introduction);
    }
}