<?php

namespace App\Service\Organization;

use App\DTO\Request\Organization\OrganizationMemberDTO;
use App\Entity\Organization\OrganizationMember;
use App\Entity\User\Profile;
use App\Entity\User\User;
use App\Enum\UserType;
use App\Repository\Organization\OrganizationMemberRepository;
use App\Repository\Organization\OrganizationRepository;
use App\Repository\User\ProfileRepository;
use App\Repository\User\UserRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class OrganizationMemberService
{

    public function __construct(
        private readonly OrganizationMemberRepository $organizationMemberRepository,
        private readonly OrganizationRepository $organizationRepository,
        private readonly ProfileRepository $profileRepository,
        private readonly Security $security,
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly UserRepository $userRepository,
    ) {}

    public function handle(OrganizationMemberDTO $organizationMemberDTO): OrganizationMember|null
    {
        // Find organization by current logged-in user
        $organization = $this->organizationRepository->findOneBy([
            'userId' => $this->security->getUser()
        ]);
        if (!$organization) {
            throw new BadRequestException('There is no existing organization for this user.', 400);
        }
        // Create user
        $user = new User(
            type: UserType::EMAIL->name,
            roles: ['ROLE_SELLER', 'ROLE_ORGANIZATION_MEMBER'],
            authId: $organizationMemberDTO->email,
            status: 'PENDING'
        );
        // Hash password
        $password = $this->passwordHasher->hashPassword($user, $organizationMemberDTO->password);
        $user->setPassword($password);
        // Persist user
        $this->userRepository->save($user);
        // Create profile
        $profile = new Profile(
            userId: $user->getId(),
            firstName: $organizationMemberDTO->firstName,
            lastName: $organizationMemberDTO->lastName,
            email: $organizationMemberDTO->email,
            mobileNumber: $organizationMemberDTO->mobileNumber,
            user: $user
        );
        // Persist profile
        $this->profileRepository->save($profile);
        // Create organization member
        $organizationMember = new OrganizationMember(
            organizationId: $organization->getId(),
            userId: $user->getId(),
            status: 'PENDING',
            metadata: [],
            user: $user
        );
        // Persist organization member
        $this->organizationMemberRepository->save($organizationMember);
        // Attach profile to user
        $user->setProfile($profile);
        // Persist user
        $this->userRepository->save($user);
        // Send email link

        return $this->organizationMemberRepository->find($organizationMember->getId());
    }

}