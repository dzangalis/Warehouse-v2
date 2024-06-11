<?php

namespace users;

use Exception;

class UserController
{
    private array $users = [];

    public function implementUser(User $user): void
    {
        if (isset($this->users[$user->getAccessCode()])) {
            throw new Exception('User exists with the same code as this one.');
        }

        $this->users[$user->getAccessCode()] = $user;
    }

    public function login(string $accessCode): ?User
    {
        if (isset($this->users[$accessCode])) {
            return $this->users[$accessCode];
        }
        return null;
    }
}