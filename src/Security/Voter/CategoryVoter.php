<?php

namespace App\Security\Voter;

use Psr\Log\LoggerInterface;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class CategoryVoter extends Voter
{
    public const CREATE = 'CREATE_CATEGORY';
    public const DELETE = 'DELETE_CATEGORY';
    public const EDIT = 'EDIT_CATEGORY';
    public const VIEW = 'VIEW_CATEGORY';

    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }


    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::EDIT, self::VIEW, self::DELETE, self::CREATE])
            && $subject instanceof \App\Entity\Category;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return $attribute === self::VIEW;  // Guest may access to views
        }

        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            return true;  // Admin gets every single rights
        }


        switch ($attribute) {
            case self::VIEW:
                return true;  // Every authenticated user has access to views
            case self::EDIT:
            case self::DELETE:
            case self::CREATE:
                return in_array('ROLE_OWNER', $user->getRoles());  // The owner is the only one allowed to edit, delete or create
        }


        return false;
    }
}