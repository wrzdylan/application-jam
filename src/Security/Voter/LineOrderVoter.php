<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class LineOrderVoter extends Voter
{

    public const CREATE = 'CREATE_LINE_ORDER';
    public const EDIT = 'EDIT_LINE_ORDER';
    public const VIEW = 'VIEW_LINE_ORDER';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::CREATE, self::EDIT, self::VIEW])
            && $subject instanceof \App\Entity\LineOrder;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        // Verify if user object belongs to UserInterface type.
        if (!$user instanceof UserInterface) {
            return false;
        }

        // Admin gets any single right.
        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            return true;
        }

        // Gets associated order from the LineOrder object.
        $order = $subject->getOrderAssociated();

        // Gets order's owner.
        $orderOwner = $order->getOwner();

        switch ($attribute) {
            case self::CREATE:
                // Order's owner and/or an OWNER role may create a LineOrder.
                return $user === $orderOwner || in_array('ROLE_OWNER', $user->getRoles());
            case self::EDIT:
                // Order's owner and/or an OWNER role is allowed to edit the LineOrder.
                return $user === $orderOwner || in_array('ROLE_OWNER', $user->getRoles());
            case self::VIEW:
                // Verify if user is order's owner, or belongs to OWNER role.
                return $user === $orderOwner || in_array('ROLE_OWNER', $user->getRoles());
        }

        return false;  // Return false for any other unknown attributes.
    }

}