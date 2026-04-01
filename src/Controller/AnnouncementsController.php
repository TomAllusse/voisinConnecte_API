<?php

namespace App\Controller;

use App\Repository\AnnouncementsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Announcements;
use Doctrine\ORM\EntityManagerInterface;

final class AnnouncementsController extends AbstractController
{
    public function __construct(private AnnouncementsRepository $announcementsRepository)
    {
    }

    #[Route('/api/categories', name: 'app_categories_add', methods: 'POST')]
    public function addCategories(Request $request, EntityManagerInterface $entityManager): Response
    {

        $data = json_decode($request->getContent(), true);

        if (!$data) {
            return $this->json(["status" => "error", "message" => "donnée vide"]);
        }

        $announce = new Announcements();

        $announce->setName($data["name"]);
        $announce->setColorHex($data["color_hex"]);
        $announce->setIcon($data["icon"]);

        $entityManager->persist($announce);
        $entityManager->flush();

        return $this->json([
            "status" => "ok",
            "message" => "categories ajouter",
            "result" => $announce
        ]);
    }

    #[Route('/api/categories-update/{id}', name: 'app_categories_update', methods: 'PUT')]
    public function updateCategories(int $id, Request $request, EntityManagerInterface $entityManager): Response
    {

        $data = json_decode($request->getContent(), true);

        if (!$data) {
            return $this->json(["status" => "error", "message" => "donnée vide"]);
        }

        $announce = $this->AnnouncementsRepository->find($id);

        if (!$announce) {
            return $this->json([
                "status" => "error",
                "message" => "Employé non trouvé"
            ]);
        }

        $announce->setName($data["name"]);
        $announce->setColorHex($data["color_hex"]);
        $announce->setIcon($data["icon"]);

        $entityManager->persist($announce);
        $entityManager->flush();

        return $this->json([
            "status" => "ok",
            "message" => "categories ajouter",
            "result" => $announce
        ]);
    }

    #[Route('/api/categories-delete/{id}', name: 'app_categories_delete', methods: 'DELETE')]
    public function deleteCategories(int $id, EntityManagerInterface $entityManager): Response
    {

        $announce = $this->AnnouncementsRepository->find($id);

        if (!$announce) {
            return $this->json([
                "status" => "error",
                "message" => "Employé non trouvé"
            ]);
        }

        $entityManager->remove($announce);
        $entityManager->flush();

        return $this->json([
            "status" => "ok",
            "message" => "Employé supprimé avec succès"
        ]);
    }

    #[Route('/api/categories', name: 'app_categories_all', methods: 'GET')]
    public function getCategoriesAll(): Response
    {

        $announce = $this->AnnouncementsRepository->findAll();

        if (empty($announce)) {

            return $this->json([
                "status" => "error",
                "message" => "Aucun employé trouvé"
            ]);

        } else {

            return $this->json([
                "status" => "ok",
                "message" => "Employés récupérés avec succès",
                "result" => $announce
            ]);

        }
    }
}
