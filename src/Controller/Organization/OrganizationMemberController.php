<?php

namespace App\Controller\Organization;

use App\DTO\Request\Organization\OrganizationMemberDTO;
use App\Repository\Organization\OrganizationMemberRepository;
use App\Repository\User\UserRepository;
use App\Service\Organization\OrganizationMemberService;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('api/members')]
class OrganizationMemberController extends AbstractController
{

    public function __construct(
        private readonly OrganizationMemberRepository $organizationMemberRepository,
        private readonly OrganizationMemberService $organizationMemberService,
        private readonly UserRepository $userRepository
    ) {}

    #[Route('/', name: 'app_organization_members', methods: ['GET'])]
    public function index(
        #[MapQueryParameter] int|null $organizationId,
        #[MapQueryParameter] int|null $page = 1,
        #[MapQueryParameter] int|null $pageSize = 10,
        #[MapQueryParameter] string|null $searchQuery = null
    ): JsonResponse
    {
        try {
            $membersQuery = $this->organizationMemberRepository->findByOrganizationId(
                $organizationId,
                $page,
                $pageSize,
                $searchQuery
            );

            return $this->json($membersQuery);
        } catch (\Exception) {
            return $this->json([
                'message' => 'Oops. Something went wrong'
            ]);
        }
    }

    #[Route('/store', name: 'app_organization_members_store', methods: ['POST'])]
    public function store(#[MapRequestPayload] OrganizationMemberDTO $organizationMemberDTO): JsonResponse
    {
        try {
            $organizationMember = $this->organizationMemberService->handle($organizationMemberDTO);

            return $this->json([
                'data' => $organizationMember
            ]);
        } catch (UniqueConstraintViolationException $exception) {
            return $this->json([
                'message' => 'User email already exists.'
            ]);
        }
    }

    #[Route('/{memberId}', name: 'app_organization_members_show', methods: ['GET'])]
    public function show(int $memberId): JsonResponse
    {
        try {
            $member = $this->organizationMemberRepository->find($memberId);
            return $this->json([
                'data' => [
                    'id' => $member->getId(),
                    'userId' => $member->getUserId(),
                    'roles' => $member->getUser()->getRoles(),
                    'authId' => $member->getUser()->getAuthId(),
                    'firstName' => $member->getUser()->getProfile()->getFirstName(),
                    'lastName' => $member->getUser()->getProfile()->getLastName(),
                    'email' => $member->getUser()->getProfile()->getEmail(),
                    'mobileNumber' => $member->getUser()->getProfile()->getMobileNumber(),
                    'memberStatus' => $member->getStatus(),
                    'userStatus' => $member->getUser()->getStatus(),
                    'createdAt' => $member->getCreatedAt(),
                    'updatedAt' => $member->getUpdatedAt(),
                ]
            ]);
        }  catch (\Exception) {
            return $this->json([
                'message' => 'Oops. Something went wrong'
            ]);
        }
    }

    #[Route('/update/{memberId}', name: 'app_organization_members_update', methods: ['PUT'])]
    public function updateStatus(Request $request, int $memberId): JsonResponse
    {
        $parameters = $request->toArray();

        if (!isset($parameters['status']) || $parameters['status'] === '') {
            return $this->json([
                'message' => 'Member status cannot be blank.'
            ], 400);
        }

        $member = $this->organizationMemberRepository->find($memberId);
        if (!$member) {
            return $this->json([
                'message' => 'Member does not exist.'
            ], 404);
        }
        $member->setStatus($parameters['status']);
        $this->organizationMemberRepository->save($member);

        $user = $member->getUser();
        $user->setStatus($parameters['status']);
        $this->userRepository->save($user);

        return $this->json([
            'data' => $member
        ]);
    }
}
