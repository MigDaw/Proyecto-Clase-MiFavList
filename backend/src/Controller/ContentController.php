<?php
// src/Controller/ContentController.php
namespace App\Controller;

use App\Document\Content;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;


class ContentController extends AbstractController
{
    #[Route('/api/content', name: 'add_content', methods: ['POST'])]
    public function addContent(Request $request, DocumentManager $dm): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $content = new Content();
        $content->setTitle($data['title']);
        $content->setGenre($data['genre']);
        $content->setType($data['type']);
        $content->setImage($data['image'] ?? null);

        $dm->persist($content);
        $dm->flush();

        return $this->json(['message' => 'Contenido creado', 'id' => $content->getId()]);
    }

    #[Route('/api/content/search', name: 'search_content', methods: ['GET'])]
    public function searchContent(Request $request, DocumentManager $dm): JsonResponse
    {
        $query = $request->query->get('query');
        $results = $dm->getRepository(Content::class)->createQueryBuilder()
            ->field('title')->equals(new \MongoDB\BSON\Regex($query, 'i'))
            ->limit(10)
            ->getQuery()
            ->execute()
            ->toArray();

        return $this->json($results);
    }

    #[Route('/api/content/upload-image', name: 'upload_content_image', methods: ['POST'])]
    public function uploadImage(Request $request): JsonResponse
    {
        /** @var UploadedFile|null $file */
        $file = $request->files->get('image');

        if (!$file) {
            return $this->json(['error' => 'No se ha enviado ninguna imagen.'], 400);
        }

        $uploadsDir = $this->getParameter('kernel.project_dir') . '/public/uploads/content/';
        if (!is_dir($uploadsDir)) {
            mkdir($uploadsDir, 0755, true);
        }

        $filename = uniqid() . '.' . $file->guessExtension();
        $file->move($uploadsDir, $filename);

        $imagePath = '/uploads/content/' . $filename;

        return $this->json(['message' => 'Imagen subida correctamente.', 'image' => $imagePath]);
    }

}
