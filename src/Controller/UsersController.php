<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Users;
use App\Repository\UsersRepository;
use Doctrine\ORM\EntityManagerInterface;

final class UsersController extends AbstractController
{

    public function __construct(private UsersRepository $usersRepository) {}

    #[Route('/user/sign', name: 'app_auth_sign', methods: ['POST'])]
    public function sign(Request $request, EntityManagerInterface $entityManager): Response
    {

        $data = json_decode($request->getContent(), true);

        if (!$data) {
            return $this->json(["status" => "error", "message" => "Invalid JSON data"]);
        }

        $newUser = new Users();

        $newUser->setEmail($data['email']);
        $newUser->getPasswordHash(md5($data['password']));

        $token = md5(uniqid());
        $newUser->setToken($token);

        $entityManager->persist($newUser);
        $entityManager->flush();

        return $this->json([
            'status' => 'ok',
            'message' => 'Sign in successful',
            'result' => [ 'userId' => $newUser->getId(), 'name' => $newUser->getDisplayName(), 'token' => $token, 'email' => $newUser->getEmail() ]
        ]);
    }

    #[Route('/user/login', name: 'app_auth_login', methods: ['POST'])]
    public function login(Request $request, EntityManagerInterface $entityManager): Response
    {

        $data = json_decode($request->getContent(), true);

        if (!$data) {
            return $this->json(["status" => "error", "message" => "Invalid JSON data"]);
        }

        $user = $this->usersRepository->findOneBy(['email' => $data['email']]);

        if (!$user) {
            return $this->json(["status" => "error", "message" => "User not found"]);
        }

        if ($user->getPassword() !== md5($data['password'])) {
            return $this->json(["status" => "error", "message" => "Invalid password"]);
        } else {
            return $this->json([
                'status' => 'ok',
                'message' => 'Login in successful',
                'result' => [ 'userId' => $user->getId(),'token' => $user->getToken(), 'email' => $user->getEmail() ]
            ]);
        }
    }

    #[Route('/user/token', name: 'app_auth_token', methods: ['GET'])]
    public function token(Request $request, EntityManagerInterface $entityManager): Response {
        $token = $request->headers->get('Authorization');
        if (!$token) {
            return $this->json(["status" => "error", "message" => "Token not provided"]);
        }

        $token = substr($token, 7); // Enleve le prefix 'Bearer '

        $user = $this->usersRepository->findOneBy(['token' => $token]);

        if (!$user) {
            return $this->json(["status" => "error", "message" => "Invalid token"]);
        }

        return $this->json([
            'status' => 'ok',
            'message' => 'Token is valid',
            'result' => [ 'userId' => $user->getId(), 'token' => $user->getToken(), 'email' => $user->getEmail() ]
        ]);
    }
}
