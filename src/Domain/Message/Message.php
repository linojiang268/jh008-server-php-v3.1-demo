<?php
namespace Jihe\Domain\Message;

use Brouwers\LaravelDoctrine\Extensions\Timestamps\Timestamps;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity
 * @ORM\Table(name = "messages")
 */
class Message
{
    use Timestamps;

    /**
     * notice is sent but not checked
     * @var int
     */
    const STATUS_SENT = 1;

    /**
     * notice is sent and checked
     * @var int
     */
    const STATUS_CHECKED = 2;
    /**
     * the message of the team create request approved
     * @var int
     */
    const TYPE_TEAM_CREATE_REQUEST_APPROVED = 1;
    /**
     * the message of the team create request rejected
     * @var int
     */
    const TYPE_TEAM_CREATE_REQUEST_REJECTED = 2;
    /**
     * the message of the team update request approved
     * @var int
     */
    const TYPE_TEAM_UPDATE_REQUEST_APPROVED = 3;
    /**
     * the message of the team update request rejected
     * @var int
     */
    const TYPE_TEAM_UPDATE_REQUEST_REJECTED = 4;
    
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="guid")
     */
    protected $id;
    /**
     * @ORM\ManyToOne(targetEntity="Jihe\Domain\User\User")
     */
    protected $subscriber;
    /**
     * @ORM\Column(type="string")
     */
    protected $type;
    /**
     * @ORM\Column(type="smallint")
     */
    protected $status = self::STATUS_SENT;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     * @return Message
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getSubscriber()
    {
        return $this->subscriber;
    }

    /**
     * @param mixed $subscriber
     * @return Message
     */
    public function setSubscriber($subscriber)
    {
        $this->subscriber = $subscriber;
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
     * @return Message
     */
    public function setType($type)
    {
        $this->type = $type;
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
     * @return Message
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }
}