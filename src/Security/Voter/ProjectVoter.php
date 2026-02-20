<?php

namespace App\Security\Voter;

use App\Entity\Employee;
use App\Entity\Project;
use App\Enum\EmployeeRole;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

final class ProjectVoter extends Voter
{
    const VIEW = 'view';

    public function __construct(
        private AccessDecisionManagerInterface $accessDecisionManager,
    ) {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        if ($attribute != self::VIEW) {
            return false;
        }

        if (!$subject instanceof Project) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return false;
        }

        return match($attribute) {
            self::VIEW => $this->canView($subject, $token),
            default => throw new \LogicException('This code should not be reached!')
        };
    }

    private function canView(Project $project, TokenInterface $token): bool
    {
        /** @var Employee $user */
        $user = $token->getUser();

        if ($this->accessDecisionManager->decide($token, [EmployeeRole::Manager->value])) {
            return true;
        }

        foreach ($project->getTeam() as $employee) {
            if ($employee->getId() === $user->getId()) {
                return true;
            }
        }

        return false;
    }
}
