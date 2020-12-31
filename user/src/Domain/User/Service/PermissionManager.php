<?php

declare(strict_types=1);

namespace App\Domain\User\Service;

use App\Domain\User\Entity\User;
use InvalidArgumentException;

class PermissionManager
{
    public const VIEW_USERS = 'VIEW_USERS';
    public const VIEW_USER = 'VIEW_USER';
    public const CREATE_USER = 'CREATE_USER';
    public const UPDATE_USER = 'UPDATE_USER';
    public const DELETE_USER = 'DELETE_USER';

    public const USER_ACTIONS = [
        self::VIEW_USERS,
        self::VIEW_USER,
        self::CREATE_USER,
        self::UPDATE_USER,
        self::DELETE_USER,
    ];

    public static function canDo(string $action, User $user, ?object $subject = null): bool
    {
        if (in_array($action, self::USER_ACTIONS, true)) {
            return self::canDoUserAction($action, $user, $subject);
        }

        return false;
    }

    private static function canDoUserAction(string $action, User $user, ?User $subject): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        switch ($action) {
            case self::VIEW_USERS:
            case self::VIEW_USER:
                return true;
            case self::UPDATE_USER:
            case self::DELETE_USER:
                if (!$subject) {
                    throw new InvalidArgumentException('User is empty');
                }

                return $user->getId() === $subject->getId();
        }

        return false;
    }
}
