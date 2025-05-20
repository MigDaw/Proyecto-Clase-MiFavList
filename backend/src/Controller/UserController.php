<?php

namespace App\Controller;

use App\Document\User;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    #[Route('/api/users', name: 'api_users_list', methods: ['GET'])]
    public function list(DocumentManager $dm): JsonResponse
    {
        $users = $dm->getRepository(User::class)->findAll();
        $data = [];
        foreach ($users as $user) {
            $data[] = [
                'id' => $user->getId(),
                'username' => $user->getUsername(),
                'isPublic' => $user->isPerfilPublic(), // Usa el getter correcto
            ];
        }
        return $this->json($data);
    }

    #[Route('/api/users/{id}', name: 'api_user_show', methods: ['GET'])]
    public function show(DocumentManager $dm, $id): JsonResponse
    {
        $user = $dm->getRepository(User::class)->find($id);
        if (!$user) {
            return $this->json(['error' => 'Usuario no encontrado'], 404);
        }
        return $this->json([
            'id' => $user->getId(),
            'username' => $user->getUsername(),
            'email' => $user->getEmail(),
            'profilePic' => $user->getProfilePic(),
            'perfilPublic' => $user->isPerfilPublic(),
            'chatPublic' => $user->isChatPublic(),
            // añade otros campos públicos si quieres
        ]);
    }
}