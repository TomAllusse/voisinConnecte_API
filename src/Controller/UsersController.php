<?php

namespace App\Controller;

use App\Entity\Users;
use App\Enum\Role;
use App\Repository\UsersRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

final class UsersController extends AbstractController
{
    public function __construct(
        private UsersRepository $usersRepository,
        private RefreshTokenController $refreshTokenController,
    ) {}

    #[Route('/api/user/sign', name: 'app_auth_sign', methods: ['POST'])]
    public function sign(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $hasher): Response
    {
        $data = json_decode($request->getContent(), true);
        if (!$data) return $this->json(["status" => "error", "message" => "Invalid JSON"], 400);

        $newUser = new Users();
        $newUser->setEmail($data['email']);
        $newUser->setPasswordHash($hasher->hashPassword($newUser, $data['password']));

        $newUser->setFirstName($data['prenom'] ?? '');
        $newUser->setLastName($data['nom'] ?? '');
        $newUser->setCity($data['ville'] ?? null);

        // Valeurs obligatoires
        $newUser->setRole(Role::User);
        $newUser->setIsBanned(false);
        $newUser->setCreatedAt(new \DateTimeImmutable());
        $newUser->setUpdatedAt(new \DateTimeImmutable());

        $em->persist($newUser);
        $em->flush();

        $token = $this->refreshTokenController->generateToken($newUser, $em);

        return $this->json([
            'status' => 'ok',
            'result' => [
                'userId' => $newUser->getId(),
                'token' => $token,
                'email' => $newUser->getEmail(),
            ]
        ]);
    }

    #[Route('/api/user/login', name: 'app_auth_login', methods: ['POST'])]
    public function login(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $hasher): Response
    {
        $data = json_decode($request->getContent(), true);
        $user = $this->usersRepository->findOneBy(['email' => $data['email'] ?? '']);

        if (!$user || !$hasher->isPasswordValid($user, $data['password'])) {
            return $this->json(["status" => "error", "message" => "Invalid credentials"], 401);
        }

        $token = $this->refreshTokenController->generateToken($user, $em);

        return $this->json([
            'status' => 'ok',
            'result' => [
                'userId' => $user->getId(),
                'token' => $token,
                'email' => $user->getEmail(),
            ]
        ]);
    }
}
