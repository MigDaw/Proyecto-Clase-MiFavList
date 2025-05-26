<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class ContactController extends AbstractController
{
    #[Route('/api/contact', name: 'api_contact', methods: ['POST'])]
    public function contact(Request $request, MailerInterface $mailer): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $nombre = $data['nombre'] ?? '';
        $email = $data['email'] ?? '';
        $asunto = $data['asunto'] ?? '';
        $mensaje = $data['mensaje'] ?? '';

        if (!$nombre || !$email || !$asunto || !$mensaje) {
            return $this->json(['error' => 'Faltan campos obligatorios.'], 400);
        }

        $emailMessage = (new Email())
            ->from('mifavlistcontacto@gmail.com')
            ->replyTo($email)
            ->to('mifavlistcontacto@gmail.com')
            ->subject($asunto)
            ->text("Nombre: $nombre\nEmail: $email\nMensaje:\n$mensaje");

        $mailer->send($emailMessage);

        return $this->json(['message' => 'Correo enviado correctamente.']);
    }
}