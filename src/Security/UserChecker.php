<?php

namespace App\Security;

use App\Entity\Participant;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user): void
    {
        if (!$user instanceof Participant) {
            return;
        }

        if (!$user->isActif()) {
            throw new CustomUserMessageAccountStatusException('Votre compte est désactivé.');
        }
    }

    public function checkPostAuth(UserInterface $user): void
    {
    }
}

