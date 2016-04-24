<?php
namespace Jihe\Domain\Team;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity
 * @ORM\Table(name="team_identify_requests")
 */
class IdentifyRequest extends Request
{
    /**
     * @ORM\OneToMany(targetEntity="Certification", mappedBy="request")
     * @var ArrayCollection
     */
    protected $certifications;

    function __construct()
    {
        $this->certifications = new ArrayCollection();
    }

    /**
     * @return ArrayCollection
     */
    public function getCertifications()
    {
        return $this->certifications;
    }

    /**
     * @param ArrayCollection $certifications
     * @return IdentifyRequest
     */
    public function setCertifications($certifications)
    {
        $this->certifications = $certifications;
    }

    public function reject()
    {
        parent::reject();

        foreach($this->certifications as $certification) {
            $certification->setStatus(Certification::STATUS_ASSESS_REJECTED);
        }
    }

    public function approve()
    {
        parent::approve();

        foreach ($this->certifications as $certification) {
            $certification->setStatus(Certification::STATUS_ASSESS_APPROVED);
        }
    }
}