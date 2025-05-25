<?php

namespace App\Controller;

use App\Document\Comment;
use App\Document\User;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class CommentController extends AbstractController
{
    #[Route('/api/profiles/{profileId}/comments', name: 'get_profile_comments', methods: ['GET'])]
    public function getComments(DocumentManager $dm, string $profileId): JsonResponse
    {
        // Busca el usuario del perfil
        $profile = $dm->getRepository(User::class)->find($profileId);
        if (!$profile) {
            return $this->json(['error' => 'Perfil no encontrado'], 404);
        }

        // Busca los comentarios referenciando ese perfil
        $comments = $dm->getRepository(Comment::class)->findBy(['userProfile' => $profile]);
        $data = [];
        foreach ($comments as $comment) {
            $data[] = [
                'id' => $comment->getId(),
                'author' => [
                    'id' => $comment->getAuthor()->getId(),
                    'username' => $comment->getAuthor()->getUsername(),
                    'profilePic' => $comment->getAuthor()->getProfilePic(),
                ],
                'message' => $comment->getMessage(),
                'commentDate' => $comment->getCommentDate()->format(\DateTime::ATOM),
            ];
        }
        return $this->json($data);
    }

    #[Route('/api/profiles/{profileId}/comments', name: 'post_profile_comment', methods: ['POST'])]
    public function postComment(DocumentManager $dm, Request $request, string $profileId): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        if (!$user) {
            return $this->json(['error' => 'No autenticado'], 401);
        }

        // No dejar comentar en el propio perfil
        if ($user->getId() === $profileId) {
            return $this->json(['error' => 'No puedes comentar en tu propio perfil'], 403);
        }

        // Busca el perfil destino
        $profile = $dm->getRepository(User::class)->find($profileId);
        if (!$profile) {
            return $this->json(['error' => 'Perfil no encontrado'], 404);
        }
        if (!$profile->isChatPublic()) {
            return $this->json(['error' => 'El chat no está habilitado en este perfil'], 403);
        }

        $data = json_decode($request->getContent(), true);
        if (!isset($data['message']) || trim($data['message']) === '') {
            return $this->json(['error' => 'El mensaje no puede estar vacío'], 400);
        }

        // Crea el comentario con referencias ODM
        $comment = new Comment();
        $comment->setUserProfile($profile);
        $comment->setAuthor($user);
        $comment->setMessage($data['message']);
        $comment->setCommentDate(new \DateTime());

        $dm->persist($comment);
        $dm->flush();

        return $this->json([
            'id' => $comment->getId(),
            'author' => [
                'id' => $user->getId(),
                'username' => $user->getUsername(),
                'profilePic' => $user->getProfilePic(),
            ],
            'message' => $comment->getMessage(),
            'commentDate' => $comment->getCommentDate()->format(\DateTime::ATOM),
        ], 201);
    }

    #[Route('/api/comments/{id}', name: 'delete_comment', methods: ['DELETE'])]
    public function deleteComment(DocumentManager $dm, string $id): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        if (!$user) {
            return $this->json(['error' => 'No autenticado'], 401);
        }

        $comment = $dm->getRepository(Comment::class)->find($id);
        if (!$comment) {
            return $this->json(['error' => 'Comentario no encontrado'], 404);
        }

        // Solo puede borrar: el autor del comentario o el dueño del perfil
        if (
            $comment->getAuthor()->getId() !== $user->getId() &&
            $comment->getUserProfile()->getId() !== $user->getId()
        ) {
            return $this->json(['error' => 'No tienes permiso para borrar este comentario'], 403);
        }

        $dm->remove($comment);
        $dm->flush();

        return $this->json(['message' => 'Comentario borrado']);
    }
}
