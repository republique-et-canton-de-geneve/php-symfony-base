<?php

namespace App\Security;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * @extends Voter<string,mixed>
 */
class ActionVoter extends Voter
{
    protected const array AUTHORIZED = [
        Action::HOMEPAGE => [Role::ALL],
        Action::NAVBAR_ENVIRONNEMENT => [Role::ADMINISTRATEUR, Role::UTILISATEUR],
        Action::ADMIN_PAGE => [Role::ADMINISTRATEUR],
        Action::ADMIN_PARAMETER => [Role::ADMINISTRATEUR],
        Action::ADMIN_PARAMETER_WRITE => [Role::ADMINISTRATEUR],
        Action::ADMIN_MAIL_TEST => [Role::ADMINISTRATEUR],
        Action::ADMIN_LOG => [Role::ADMINISTRATEUR],
    ];
    /*
     * @var Security
     */
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    /**
     * Indicates whether an action (attribute) is handled by this voter.
     */
    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, Action::getActions(), true);
    }

    /**
     * Indicates whether the role grants the right to perform an action.
     */
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        // the user must be logged in; if not, deny permission
        if (!$user instanceof User) {
            return false;
        }
        $authorized = self::AUTHORIZED[$attribute] ?? [];
        foreach ($authorized as $authorization) {
            if ($this->security->isGranted($authorization)) {
                return true;
            }
        }

        return false;
    }
}
