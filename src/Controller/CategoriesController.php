<?php

namespace App\Controller;

use App\Repository\CategoriesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Categories;
use Doctrine\ORM\EntityManagerInterface;

final class CategoriesController extends AbstractController
{
    public function __construct(private CategoriesRepository $categoriesRepository){}

    #[Route('/api/categories', name: 'app_categories_add', methods: 'POST')]
    public function addCategories(Request $request, EntityManagerInterface $entityManager): Response
    {

        $data = json_decode($request->getContent(), true);

        if (!$data){
            return $this->json(["status"=>"error", "message"=>"donnée vide"]);
        }

        $categories = new Categories();

        $categories->setName($data["name"]);
        $categories->setColorHex($data["color_hex"]);
        $categories->setIcon($data["icon"]);

        $entityManager->persist($categories);
        $entityManager->flush();

        return $this->json([
            "status"=>"ok",
            "message" => "categories ajouter",
            "result" => $categories
        ]);
    }

    #[Route('/api/categories-update/{id}', name: 'app_categories_update', methods: 'PUT')]
    public function updateCategories(int $id, Request $request, EntityManagerInterface $entityManager): Response
    {

        $data = json_decode($request->getContent(), true);

        if (!$data){
            return $this->json(["status"=>"error", "message"=>"donnée vide"]);
        }

        $categories = $this->categoriesRepository->find($id);

        if (!$categories) {
            return $this->json([
                "status" => "error",
                "message" => "Employé non trouvé"
            ]);
        }

        $categories->setName($data["name"]);
        $categories->setColorHex($data["color_hex"]);
        $categories->setIcon($data["icon"]);

        $entityManager->persist($categories);
        $entityManager->flush();

        return $this->json([
            "status"=>"ok",
            "message" => "categories ajouter",
            "result" => $categories
        ]);
    }

    #[Route('/api/categories-delete/{id}', name: 'app_categories_delete', methods: 'DELETE')]
    public function deleteCategories(int $id, EntityManagerInterface $entityManager): Response
    {

        $categories = $this->categoriesRepository->find($id);

        if (!$categories) {
            return $this->json([
                "status" => "error",
                "message" => "Employé non trouvé"
            ]);
        }

        $entityManager->remove($categories);
        $entityManager->flush();

        return $this->json([
            "status" => "ok",
            "message" => "Employé supprimé avec succès"
        ]);
    }

    #[Route('/api/categories', name: 'app_categories_all', methods: 'GET')]
    public function getCategoriesAll(): Response
    {

        $categories = $this->categoriesRepository->findAll();

        if (empty($categories)) {

            return $this->json([
                "status" => "error",
                "message" => "Aucun employé trouvé"
            ]);

        } else {

            return $this->json([
                "status" => "ok",
                "message" => "Employés récupérés avec succès",
                "result" => $categories
            ]);

        }
    }
}
