<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class OrderVoter extends Voter
{
    public const CREATE = 'CREATE_ORDER';
    public const EDIT = 'EDIT_ORDER';
    public const VIEW = 'VIEW_ORDER';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::VIEW, self::EDIT, self::CREATE])
            && $subject instanceof \App\Entity\Order;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        // Verify if user object belongs to UserInterface type
        if (!$user instanceof UserInterface) {
            return false;
        }

        // Admin gets every single rights
        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            return true;
        }

        // Gets order's owner. $subject has to be the "order" and has to own a getOwner() method.
        $orderOwner = $subject->getOwner();

        switch ($attribute) {
            case self::VIEW:
                // Verify if user is order's owner, or belongs to owner role
                return $user === $orderOwner || in_array('ROLE_OWNER', $user->getRoles());
            case self::EDIT:
                // Order's owner and/or an owner role is allowed to edit the Order
                return $user === $orderOwner || in_array('ROLE_OWNER', $user->getRoles());
            case self::CREATE:
                // Everyone is allowed to create an Order
                return true;
        }

        return false;  // Return false for any other unknown attributes
    }

}