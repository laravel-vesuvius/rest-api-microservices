<?php

declare(strict_types=1);

namespace App\Application\Security\Voter;

use App\Domain\User\Entity\User;
use App\Domain\User\Service\PermissionManager;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class UserVoter extends Voter
{
    protected function supports($attribute, $subject): bool
    {
        return in_array($attribute, PermissionManager::USER_ACTIONS, true)
            && $subject instanceof User;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user) {
            return false;
        }

        return PermissionManager::canDo($attribute, $user, $subject);
    }
}
