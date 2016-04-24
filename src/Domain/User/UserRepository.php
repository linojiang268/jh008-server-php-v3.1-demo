<?php
namespace Jihe\Domain\User;

interface UserRepository
{
    /**
     * check whether there is an existing user with specified mobile number
     *
     * @param string $mobile   user's mobile number
     * @return bool            true if there's one user with given mobile number. false otherwise
     */
    public function existsByMobile($mobile);

    /**
     * find a user by the id
     *
     * @param string $id   user's id
     * @return mixed
     */
    public function find($id);

    /**
     * store given user
     *
     * @param User $user   user to be stored
     */
    public function store(User $user);
}