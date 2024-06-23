<?php

namespace App\Controller\Auth;

use App\DTO\Request\Auth\ResetPasswordDTO;
use App\Entity\User\PasswordResetToken;
use App\Repository\User\PasswordResetTokenRepository;
use App\Repository\User\UserRepository;
use Carbon\Carbon;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/auth')]
class PasswordController extends AbstractController
{

    public function __construct(
        private readonly PasswordResetTokenRepository $passwordResetTokenRepository,
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly UserRepository $userRepository
    ) {
    }

    #[Route('/forgot-password', name: 'app_auth_forgot_password', methods: ['POST'])]
    public function forgot(Request $request): JsonResponse
    {
        $parameters = $request->toArray();
        if (!isset($parameters['authId']) || $parameters['authId'] === '') {
            throw new BadRequestHttpException('Invalid email or mobile number.');
        }
        $user = $this->userRepository->findOneBy(['authId' => $parameters['authId']]);
        if (!$user) {
            throw new NotFoundHttpException('User not found.');
        }
        if (!$user->getPasswordResetToken() || $user->getPasswordResetToken()->getStatus() != 'ACTIVE') {
            $passwordReset = new PasswordResetToken(
                userId: $user->getId(),
                email: $user->getProfile()->getEmail(),
                token: md5($parameters['authId'] . time()),
                status: 'ACTIVE',
                user: $user,
                expiredAt: Carbon::now()->addMinute(5)->toDateTime()
            );
            $this->passwordResetTokenRepository->save($passwordReset);
            // TODO: Send email for password link via queue
        }

        return $this->json([
            'message' => 'Password reset token created.!'
        ]);
    }

    #[Route('/validate-token', name: 'app_auth_validate_password_token', methods: ['POST'])]
    public function validate(Request $request): JsonResponse
    {
        $parameters = $request->toArray();
        if (!isset($parameters['token']) || $parameters['token'] === '') {
            throw new BadRequestHttpException('Invalid token.');
        }
        $passwordToken = $this->passwordResetTokenRepository->findOneBy([
            'token' => $parameters['token']
        ]);
        if (!$passwordToken) {
            throw new NotFoundHttpException('Password token not found.');
        }
        if ($passwordToken->getStatus() != 'ACTIVE') {
            throw new NotFoundHttpException('Invalid reset password link.');
        }
        $this->checkIfExpiredToken($passwordToken);

        return $this->json([
            'message' => 'Password token is valid.'
        ]);
    }

    #[Route('/reset-password', name: 'app_auth_reset_password', methods: ['PUT'])]
    public function reset(#[MapRequestPayload] ResetPasswordDTO $resetPasswordDTO): JsonResponse
    {
        $passwordToken = $this->passwordResetTokenRepository->findOneBy([
            'token' => $resetPasswordDTO->token
        ]);
        if (!$passwordToken) {
            throw new NotFoundHttpException('Password token link not found.');
        }
        $this->checkIfExpiredToken($passwordToken);
        // Update new password
        $user = $passwordToken->getUser();
        $user->setPassword(
            $this->passwordHasher->hashPassword($user, $resetPasswordDTO->password)
        );
        $this->userRepository->save($user);
        // Set password token to USED
        $passwordToken->setStatus('USED');
        $this->passwordResetTokenRepository->save($passwordToken);

        return $this->json([
            'message' => 'Password successfully updated.'
        ]);
    }

    private function checkIfExpiredToken(PasswordResetToken $passwordToken): JsonResponse|null
    {
        if (Carbon::now()->lessThan($passwordToken->getExpiredAt())) {
            return null;
        }
        // Set password token status to expired
        $passwordToken->setStatus('EXPIRED');
        // Update password token
        $this->passwordResetTokenRepository->save($passwordToken);

        throw new BadRequestHttpException('Password token is expired.');
    }
}
