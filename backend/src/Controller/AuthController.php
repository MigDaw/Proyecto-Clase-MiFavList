<?php

namespace App\Controller;

use App\Document\User;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AuthController extends AbstractController
{
    #[Route('/api/register', name: 'api_register', methods: ['POST'])]
    public function register(Request $request, DocumentManager $dm): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $username = $data['username'] ?? null;
        $email = $data['email'] ?? null;
        $password = $data['password'] ?? null;

        if (!$username || !$email || !$password) {
            return $this->json(['error' => 'Faltan campos obligatorios.'], 400);
        }

        // Verifica si ya existe el usuario o email
        if ($dm->getRepository(User::class)->findOneBy(['username' => $username])) {
            return $this->json(['error' => 'El nombre de usuario ya existe.'], 400);
        }

        if ($dm->getRepository(User::class)->findOneBy(['email' => $email])) {
            return $this->json(['error' => 'El correo ya está registrado.'], 400);
        }

        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        $user = new User();
        $user->setUsername($username);
        $user->setEmail($email);
        $user->setPassword($hashedPassword);

        $dm->persist($user);
        $dm->flush();

        return $this->json(['message' => 'Usuario registrado correctamente.'], 201);
    }
}
