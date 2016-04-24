<?php
namespace Jihe\Domain\User;

/**
 * customized hasher dedicated for securing user's password
 * 
 */
class PasswordHasher
{
    /**
     * Hash the given value.
     *
     * @param string $value hashed target value
     * @param string $salt  salt that adds randomness to the hashing result
     * @return string
     */
    public function make($value, $salt)
    {
        return strtoupper(md5(strtoupper($salt . $value)));
    }
    
    /**
     * Check the given plain value against a hash.
     *
     * @param string $value
     * @param string $hashedValue
     * @param string $salt
     * @return bool
     */
    public function check($value, $hashedValue, $salt)
    {
        return $hashedValue == $this->make($value, $salt);
    }
}