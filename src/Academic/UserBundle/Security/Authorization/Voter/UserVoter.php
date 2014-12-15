<?php
// src/Academic/UserBundle/Security/Authorization/Voter/UserVoter.php
namespace Academic\UserBundle\Security\Authorization\Voter;

use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserVoter implements VoterInterface
{
    const VIEW = 'view';
    const EDIT = 'edit';
    const CREATE = 'create';

    public function supportsAttribute($attribute)
    {
        return in_array($attribute, array(
            self::VIEW,
            self::EDIT,
            self::CREATE,
        ));
    }

    public function supportsClass($class)
    {
        $supportedClass = 'Academic\UserBundle\Entity\User';

        return $supportedClass === $class || is_subclass_of($class, $supportedClass);
    }

    /**
     * @var \Academic\UserBundle\Entity\User $user
     * @return  string
     */
    public function vote(TokenInterface $token, $user, array $attributes)
    {
        // check if class of this object is supported by this voter
        if (!$this->supportsClass(get_class($user))) {
            return VoterInterface::ACCESS_ABSTAIN;
        }

        // check if the voter is used correct, only allow one attribute
        // this isn't a requirement, it's just one easy way for you to
        // design your voter
        if (1 !== count($attributes)) {
            throw new \InvalidArgumentException(
                'Only one attribute is allowed for VIEW or EDIT'
            );
        }

        // set the attribute to check against
        $attribute = $attributes[0];

        // check if the given attribute is covered by this voter
        if (!$this->supportsAttribute($attribute)) {
            return VoterInterface::ACCESS_ABSTAIN;
        }

        // get current logged in user
        $currentUser = $token->getUser();

        // make sure there is a user object (i.e. that the user is logged in)
        if (!$currentUser instanceof UserInterface) {
            return VoterInterface::ACCESS_DENIED;
        }

        switch($attribute) {
            case self::VIEW:
                if (!$currentUser->isPrivate()) {
                    return VoterInterface::ACCESS_GRANTED;
                }
                break;

            case self::EDIT:
                if ($currentUser->getId() === $user->getId() || $currentUser->getRole()->getRole() === 'ROLE_ADMIN') {
                    return VoterInterface::ACCESS_GRANTED;
                }
                break;

            case self::CREATE:
                if ($currentUser->getRole()->getRole() === 'ROLE_ADMIN') {
                    return VoterInterface::ACCESS_GRANTED;
                }
                break;
        }

        return VoterInterface::ACCESS_DENIED;
    }
}
