<?php

namespace App\Controller;

use App\Document\User;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Component\Security\Http\Authenticator\FormLoginAuthenticator;

class AuthController extends AbstractController
{
    //registro
    #[Route('/api/register', name: 'api_register', methods: ['POST'])]
    public function register(
        Request $request,
        DocumentManager $dm,
        UserPasswordHasherInterface $passwordHasher
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        $username = $data['username'] ?? null;
        $email = $data['email'] ?? null;
        $plainPassword = $data['password'] ?? null;

        if (!$username || !$email || !$plainPassword) {
            return $this->json(['error' => 'Faltan campos obligatorios.'], 400);
        }

        if ($dm->getRepository(User::class)->findOneBy(['username' => $username])) {
            return $this->json(['error' => 'El nombre de usuario ya existe.'], 400);
        }

        if ($dm->getRepository(User::class)->findOneBy(['email' => $email])) {
            return $this->json(['error' => 'El correo ya está registrado.'], 400);
        }

        $user = new User();
        $user->setUsername($username);
        $user->setEmail($email);
        $user->setPassword($passwordHasher->hashPassword($user, $plainPassword));

        $dm->persist($user);
        $dm->flush();

        return $this->json(['message' => 'Usuario registrado correctamente.'], 201);
    }

    //login
    #[Route('/api/login', name: 'api_login', methods: ['POST'])]
    public function login(Request $request, DocumentManager $dm, SessionInterface $session): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $username = $data['username'] ?? null;
        $password = $data['password'] ?? null;

        if (!$username || !$password) {
            return $this->json(['error' => 'Faltan campos obligatorios.'], 400);
        }

        $user = $dm->getRepository(User::class)->findOneBy(['username' => $username]);

        if (!$user) {
            return $this->json(['error' => 'Usuario no encontrado.'], 404);
        }

        if (!password_verify($password, $user->getPassword())) {
            return $this->json(['error' => 'Contraseña incorrecta.'], 401);
        }

        $session->set('user_id', $user->getId());

        return $this->json(['message' => 'Inicio de sesión exitoso.']);
    }

    //cerrar sesión
    #[Route('/api/logout', name: 'api_logout', methods: ['POST'])]
    public function logout(SessionInterface $session): JsonResponse
    {
        if (!$session->has('user_id')) {
            return $this->json(['error' => 'No hay sesión activa.'], 401);
        }

        $session->invalidate();

        return $this->json(['message' => 'Sesión cerrada.']);
    }


    //comprobar quien está iniciado o si no hay nadie
    #[Route('/api/me', name: 'api_me', methods: ['GET'])]
    public function me(SessionInterface $session, DocumentManager $dm): JsonResponse
    {
        $userId = $session->get('user_id');

        if (!$userId) {
            return $this->json(['error' => 'No hay sesión activa.'], 401);
        }

        $user = $dm->getRepository(User::class)->find($userId);

        if (!$user) {
            return $this->json(['error' => 'Usuario no encontrado.'], 404);
        }

        return $this->json([
            'id' => $user->getId(),
            'username' => $user->getUsername(),
        ]);
    }
}
