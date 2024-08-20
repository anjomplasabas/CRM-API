<?php

namespace App\Security;

use App\Enum\UserType;
use App\Event\User\PageEvent;
use App\Repository\User\UserRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class KaljeAuthenticator extends AbstractAuthenticator
{

    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly UserRepository $userRepository,
        private EventDispatcherInterface $eventDispatcher
    ) {}

    public function supports(Request $request): ?bool
    {
        return $request->getPathInfo() == '/api/auth/login';
    }

    public function authenticate(Request $request): Passport
    {
        $credentials = $request->toArray();
        
        $user = $this->userRepository->findOneBy([
            'type' => $credentials['type'],
            'authId' => $credentials['authId']
        ]);

        if (!$user) {
            throw new AuthenticationException('Invalid user.', 401);
        }

        if (!UserType::canAuthenticateByPassword($credentials['type'])) {
            return new SelfValidatingPassport(new UserBadge($user->getUserIdentifier()));
        }

        if (!$this->passwordHasher->isPasswordValid($user, $credentials['password'])) {
            throw new AuthenticationException('Invalid password.', 401);
        }

        return new SelfValidatingPassport(new UserBadge($user->getUserIdentifier()));
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        $data = $request->toArray();
        if (isset($data['pages'])) {
            $user = $this->userRepository->findOneBy([
                'type' => $data['type'],
                'authId' => $data['authId']
            ]);

            $event = new PageEvent($data['pages'], $user->getId());
            $this->eventDispatcher->dispatch($event);
        }

        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return new JsonResponse([
            'message' => $exception->getMessage()
        ], Response::HTTP_UNAUTHORIZED);
    }
}