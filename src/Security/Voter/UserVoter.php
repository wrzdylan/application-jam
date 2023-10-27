<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class UserVoter extends Voter
{
    public const CREATE = 'CREATE_USER';
    public const DELETE = 'DELETE_USER';
    public const EDIT = 'EDIT_USER';
    public const VIEW = 'VIEW_USER';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::CREATE, self::DELETE, self::EDIT, self::VIEW])
            && $subject instanceof \App\Entity\User;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        // Guests may create an account
        if (!$user instanceof UserInterface && $attribute === self::CREATE) {
            return true;
        }

        if (in_array('ROLE_ADMIN', $user->getRoles()) || in_array('ROLE_OWNER', $user->getRoles())) {
            return true;  // Admin and OWNER get any single right.
        }

        // Check if the subject is the user's account
        if ($subject instanceof UserInterface && $subject->getId() === $user->getId()) {
            return true;  // Users can do anything with their own account.
        }

        return false;
    }

}