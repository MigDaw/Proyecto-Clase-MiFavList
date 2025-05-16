<?php

namespace App\Controller;

use App\Document\Content;
use App\Document\User;
use App\Document\UserContent;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class UserContentController extends AbstractController
{
    #[Route('/api/user-content', name: 'add_user_content', methods: ['POST'])]
    public function addUserContent(Request $request, DocumentManager $dm, SessionInterface $session): JsonResponse
    {
        $userId = $session->get('user_id');
        if (!$userId) {
            return $this->json(['error' => 'No autenticado.'], 401);
        }

        $user = $dm->getRepository(User::class)->find($userId);
        if (!$user) {
            return $this->json(['error' => 'Usuario no encontrado.'], 404);
        }

        $data = json_decode($request->getContent(), true);

        $content = $dm->getRepository(Content::class)->find($data['contentId']);
        if (!$content) {
            return $this->json(['error' => 'Contenido no encontrado.'], 404);
        }

        $userContent = new UserContent();
        $userContent->setUser($user);
        $userContent->setContent($content);
        $userContent->setAddedAt(new \DateTime());
        $userContent->setStatus($data['status'] ?? 'pendiente');
        $userContent->setRating($data['rating'] ?? null);

        $dm->persist($userContent);
        $dm->flush();

        return $this->json(['message' => 'Contenido añadido al usuario.']);
    }

    #[Route('/api/user-content', name: 'get_user_contents', methods: ['GET'])]
    public function getUserContents(DocumentManager $dm, SessionInterface $session): JsonResponse
    {
        $userId = $session->get('user_id');
        if (!$userId) {
            return $this->json(['error' => 'No autenticado.'], 401);
        }

        $userContents = $dm->getRepository(UserContent::class)->findBy(['user.id' => $userId]);

        $results = array_map(function (UserContent $uc) {
            return [
                'id' => $uc->getId(),
                'contentId' => $uc->getContent()->getId(),
                'userId' => $uc->getUser()->getId(),
                'title' => $uc->getContent()->getTitle(),
                'image' => $uc->getContent()->getImage(),
                'genre' => $uc->getContent()->getGenre(),
                'type' => $uc->getContent()->getType(),
                'status' => $uc->getStatus(),
                'rating' => $uc->getRating(),
                'addedAt' => $uc->getAddedAt()->format('Y-m-d H:i:s'),
            ];
        }, $userContents);

        return $this->json($results);
    }

    //#[Route('/api/user-content/{userId}', name: 'get_user_content_by_user', methods: ['GET'])]
    
    #[Route('/api/user-content/{id}', name: 'update_user_content', methods: ['PUT'])]
    public function updateUserContent(
        string $id,
        Request $request,
        DocumentManager $dm,
        SessionInterface $session
    ): JsonResponse {
        $userId = $session->get('user_id');
        if (!$userId) {
            return $this->json(['error' => 'No autenticado.'], 401);
        }

        /** @var \App\Document\UserContent|null $userContent */
        $userContent = $dm->getRepository(UserContent::class)->find($id);
        if (!$userContent) {
            return $this->json(['error' => 'Contenido de usuario no encontrado.'], 404);
        }

        // Opcional: verifica que el userId coincida con el del UserContent
        if ($userContent->getUser()->getId() !== $userId) {
            return $this->json(['error' => 'No autorizado.'], 403);
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['status'])) {
            $userContent->setStatus($data['status']);
        }
        if (array_key_exists('rating', $data)) {
            $userContent->setRating($data['rating']);
        }

        $dm->flush();

        return $this->json(['message' => 'Contenido de usuario actualizado.']);
    }

    #[Route('/api/user-content/{id}', name: 'delete_user_content', methods: ['DELETE'])]
    public function deleteUserContent(
        string $id,
        DocumentManager $dm,
        SessionInterface $session
    ): JsonResponse {
        $userId = $session->get('user_id');
        if (!$userId) {
            return $this->json(['error' => 'No autenticado.'], 401);
        }

        /** @var \App\Document\UserContent|null $userContent */
        $userContent = $dm->getRepository(UserContent::class)->find($id);
        if (!$userContent) {
            return $this->json(['error' => 'Contenido de usuario no encontrado.'], 404);
        }

        // Verifica que el contenido pertenezca al usuario autenticado
        if ($userContent->getUser()->getId() !== $userId) {
            return $this->json(['error' => 'No autorizado.'], 403);
        }

        $dm->remove($userContent);
        $dm->flush();

        return $this->json(['message' => 'Contenido de usuario eliminado.']);
    }
}
