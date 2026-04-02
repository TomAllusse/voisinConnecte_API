<?php

namespace App\Controller;

use App\Entity\Users;
use App\Entity\RefreshTokens;
use App\Repository\RefreshTokensRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class RefreshTokenController extends AbstractController
{
    public function __construct(private RefreshTokensRepository $refreshTokensRepository) {}

    public function generateToken(Users $user, EntityManagerInterface $em): string
    {
        $tokenString = bin2hex(random_bytes(32));

        $refreshToken = new RefreshTokens();
        $refreshToken->setIdUser($user);
        $refreshToken->setTokenHash($tokenString);
        $refreshToken->setRevoked(false);
        $refreshToken->setCreatedAt(new \DateTimeImmutable());
        $refreshToken->setExpiredAt((new \DateTimeImmutable())->modify('+30 days'));

        $em->persist($refreshToken);
        $em->flush();

        return $tokenString;
    }

    #[Route('/api/user/token/refresh', name: 'app_token_refresh', methods: ['POST'])]
    public function refresh(Request $request, EntityManagerInterface $em): Response
    {
        $authHeader = $request->headers->get('Authorization');
        if (!$authHeader) return $this->json(["status" => "error", "message" => "No token"], 401);

        $tokenValue = substr($authHeader, 7);
        $token = $this->refreshTokensRepository->findOneBy(['token_hash' => $tokenValue, 'revoked' => false]);

        if (!$token || $token->getExpiredAt() < new \DateTimeImmutable()) {
            return $this->json(["status" => "error", "message" => "Invalid token"], 401);
        }

        $user = $token->getIdUser();
        $token->setRevoked(true); // On révoque l'ancien

        $newToken = $this->generateToken($user, $em);

        return $this->json([
            'status' => 'ok',
            'result' => ['token' => $newToken]
        ]);
    }
}
