<?php
namespace Jihe\Infrastructure\Repository\User;

use Doctrine\ORM\EntityRepository;
use Jihe\Domain\User\User;
use Jihe\Domain\User\UserRepository as UserRepositoryContract;

class UserRepository extends EntityRepository implements UserRepositoryContract
{
    /**
     * @inheritdoc
     */
    public function existsByMobile($mobile)
    {
        return null != $this->findOneBy([
            'mobile' => $mobile
        ]);
    }

    /**
     * @inheritdoc
     */
    public function store(User $user)
    {
        $this->_em->persist($user);
        $this->_em->flush($user);
    }
}