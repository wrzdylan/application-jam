<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class CategoryVoter extends Voter
{
    public const CREATE = 'CREATE_CATEGORY';
    public const DELETE = 'DELETE_CATEGORY';
    public const EDIT = 'EDIT_CATEGORY';
    public const VIEW = 'VIEW_CATEGORY';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::CREATE, self::DELETE, self::EDIT, self::VIEW])
            && $subject instanceof \App\Entity\Category;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return $attribute === self::VIEW;  // Guests may access to views.
        }

        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            return true;  // Admin gets any single right.
        }

        switch ($attribute) {
            case self::VIEW:
                return true;  // Every authenticated user has access to views.
            case self::CREATE:
            case self::DELETE:
            case self::EDIT:
                return in_array('ROLE_OWNER', $user->getRoles());  // The OWNER is the only one allowed to create, delete or edit.
        }

        return false;
    }
}