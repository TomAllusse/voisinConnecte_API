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
    public function __construct(private CategoriesRepository $categoriesRepository) {}

    #[Route('/api/categories', name: 'app_categories_add', methods: 'POST')]
    public function addCategories(Request $request, EntityManagerInterface $entityManager): Response
    {
        $data = json_decode($request->getContent(), true);

        if (!$data) {
            return $this->json(
                ["status" => "error", "message" => "Donnée vide"],
                Response::HTTP_BAD_REQUEST
            );
        }

        $categories = new Categories();
        $categories->setName($data["name"]);
        $categories->setColorHex($data["color_hex"]);
        $categories->setIcon($data["icon"]);

        $entityManager->persist($categories);
        $entityManager->flush();

        return $this->json([
            "status" => "ok",
            "message" => "Catégorie ajoutée",
            "result" => $categories
        ], Response::HTTP_CREATED, [], ['groups' => ['categories:read']]);
    }

    #[Route('/api/categories-update/{id}', name: 'app_categories_update', methods: 'PUT')]
    public function updateCategories(int $id, Request $request, EntityManagerInterface $entityManager): Response
    {
        $data = json_decode($request->getContent(), true);

        if (!$data) {
            return $this->json(
                ["status" => "error", "message" => "Donnée vide"],
                Response::HTTP_BAD_REQUEST
            );
        }

        $categories = $this->categoriesRepository->find($id);

        if (!$categories) {
            return $this->json(
                ["status" => "error", "message" => "Catégorie non trouvée"],
                Response::HTTP_NOT_FOUND
            );
        }

        $categories->setName($data["name"]);
        $categories->setColorHex($data["color_hex"]);
        $categories->setIcon($data["icon"]);

        $entityManager->persist($categories);
        $entityManager->flush();

        return $this->json([
            "status" => "ok",
            "message" => "Catégorie mise à jour",
            "result" => $categories
        ], Response::HTTP_OK, [], ['groups' => ['categories:read']]);
    }

    #[Route('/api/categories-delete/{id}', name: 'app_categories_delete', methods: 'DELETE')]
    public function deleteCategories(int $id, EntityManagerInterface $entityManager): Response
    {
        $categories = $this->categoriesRepository->find($id);

        if (!$categories) {
            return $this->json(
                ["status" => "error", "message" => "Catégorie non trouvée"],
                Response::HTTP_NOT_FOUND
            );
        }

        $entityManager->remove($categories);
        $entityManager->flush();

        return $this->json([
            "status" => "ok",
            "message" => "Catégorie supprimée avec succès"
        ], Response::HTTP_OK);
    }

    #[Route('/api/categories', name: 'app_categories_all', methods: 'GET')]
    public function getCategoriesAll(): Response
    {
        $categories = $this->categoriesRepository->findBy([], ['id' => 'ASC']);

        if (empty($categories)) {
            return $this->json(
                ["status" => "error", "message" => "Aucune catégorie trouvée"],
                Response::HTTP_NOT_FOUND
            );
        }

        return $this->json([
            "status" => "ok",
            "message" => "Catégories récupérées avec succès",
            "result" => $categories
        ], Response::HTTP_OK, [], ['groups' => ['categories:read']]);
    }

    #[Route('/api/categories/tendency', name: 'app_categories_tendency', methods: 'GET')]
    public function getCategoriesTendency(): Response
    {
        $categories = $this->categoriesRepository->findTendencyWithAnnouncements();

        if (empty($categories)) {
            return $this->json(
                ["status" => "error", "message" => "Aucune tendance trouvée"],
                Response::HTTP_NOT_FOUND
            );
        }

        return $this->json([
            "status" => "ok",
            "message" => "Top 5 annonces récupérées avec succès",
            "result" => $categories
        ], Response::HTTP_OK, [], ['groups' => ['categories:read']]);
    }
}
