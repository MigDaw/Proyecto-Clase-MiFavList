<?php

namespace App\Controller;

use App\Document\User;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ProfileController extends AbstractController
{
    #[Route('/api/profile', name: 'api_profile_update', methods: ['PUT'])]
    public function updateProfile(Request $request, SessionInterface $session, DocumentManager $dm): JsonResponse
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

        if (isset($data['email'])) {
            $user->setEmail($data['email']);
        }

        if (isset($data['perfilPublic'])) {
            $user->setPerfilPublic((bool) $data['perfilPublic']);
        }

        if (isset($data['chatPublic'])) {
            $user->setChatPublic((bool) $data['chatPublic']);
        }

        $dm->flush();

        return $this->json(['success' => true]);
    }

    #[Route('/api/profile/upload-picture', name: 'upload_profile_picture', methods: ['POST'])]
    public function uploadProfilePicture(
        Request $request,
        SessionInterface $session,
        DocumentManager $dm
    ): JsonResponse {
        $userId = $session->get('user_id');

        if (!$userId) {
            return $this->json(['error' => 'No hay sesión activa.'], 401);
        }

        $user = $dm->getRepository(User::class)->find($userId);
        if (!$user) {
            return $this->json(['error' => 'Usuario no encontrado.'], 404);
        }

        $file = $request->files->get('profilePic');
        if (!$file) {
            return $this->json(['error' => 'No se ha enviado ninguna imagen.'], 400);
        }

        $uploadsDir = $this->getParameter('kernel.project_dir') . '/public/uploads/';
        $filename = uniqid() . '.' . $file->guessExtension();
        $file->move($uploadsDir, $filename);

        $user->setProfilePic('/uploads/' . $filename);
        $dm->flush();

        return $this->json(['message' => 'Foto de perfil actualizada.', 'profilePic' => $user->getProfilePic()]);
    }
}
