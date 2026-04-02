<?php

namespace App\Controller;

use App\Repository\AnnouncementsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Announcements;
use App\Enum\Status_Announcement;
use App\Enum\Type_Unit;
use function Symfony\Component\Clock\now;
use Doctrine\ORM\EntityManagerInterface;

final class AnnouncementsController extends AbstractController
{
    public function __construct(private AnnouncementsRepository $announcementsRepository)
    {
    }

    #[Route('/api/announces', name: 'app_announces_add', methods: 'POST')]
    public function addCategories(Request $request, EntityManagerInterface $entityManager): Response
    {

        $data = json_decode($request->getContent(), true);

        if (!$data) {
            return $this->json(["status" => "error", "message" => "donnée vide"]);
        }

        $announce = new Announcements();

        /*$user = $this->getUser();*/

        $announce->setIdUser(/*$user->getId()*/0);
        $announce->setIdCategory($data["id_category"]);
        $announce->setTitle($data["title"]);
        $announce->setDescription($data["description"]);
        $announce->setIsPaid($data["is_paid"]);
        $announce->setPrice($data["price"]);
        $announce->setPriceUnit(Type_Unit::from($data['price_unit']));
        $announce->setStatus(Status_Announcement::Pending);
        $announce->setIsActive(true);
        $announce->setCreatedAt(now());

        $entityManager->persist($announce);
        $entityManager->flush();

        return $this->json([
            "status" => "ok",
            "message" => "Annonces ajouter",
            "result" => $announce
        ]);
    }

    #[Route('/api/announces-update/{id}', name: 'app_announces_update', methods: 'PUT')]
    public function updateCategories(int $id, Request $request, EntityManagerInterface $entityManager): Response
    {

        $data = json_decode($request->getContent(), true);

        if (!$data) {
            return $this->json(["status" => "error", "message" => "donnée vide"]);
        }

        $announce = $this->announcementsRepository->find($id);

        if (!$announce) {
            return $this->json([
                "status" => "error",
                "message" => "Annonces non trouvé"
            ]);
        }

        $announce->setIdUser($data["id_user"]);
        $announce->setIdCategory($data["id_category"]);
        $announce->setTitle($data["title"]);
        $announce->setDescription($data["description"]);
        $announce->setIsPaid($data["is_paid"]);
        $announce->setPrice($data["price"]);
        $announce->setPriceUnit($data["price_unit"]);
        $announce->setStatus($data["status"]);
        $announce->setIsActive($data["is_active"]);
        $announce->setCreatedAt($data["created_at"]);
        $announce->setUpdatedAt($data["updated_at"]);

        $entityManager->persist($announce);
        $entityManager->flush();

        return $this->json([
            "status" => "ok",
            "message" => "Annonces ajouter",
            "result" => $announce
        ]);
    }

    #[Route('/api/announces-delete/{id}', name: 'app_announces_delete', methods: 'DELETE')]
    public function deleteCategories(int $id, EntityManagerInterface $entityManager): Response
    {

        $announce = $this->announcementsRepository->find($id);

        if (!$announce) {
            return $this->json([
                "status" => "error",
                "message" => "Annonces non trouvé"
            ]);
        }

        $entityManager->remove($announce);
        $entityManager->flush();

        return $this->json([
            "status" => "ok",
            "message" => "Annonces supprimé avec succès"
        ]);
    }

    #[Route('/api/announces', name: 'app_announces_all', methods: 'GET')]
    public function getCategoriesAll(): Response
    {

        $announce = $this->announcementsRepository->findAll();

        if (empty($announce)) {

            return $this->json([
                "status" => "error",
                "message" => "Aucun annonces trouvé"
            ]);

        } else {

            return $this->json([
                "status" => "ok",
                "message" => "Annonces récupérés avec succès",
                "result" => $announce
            ]);

        }
    }

    #[Route('/api/announces/user/{id}', name: 'app_announces_user', methods: 'GET')]
    public function getCategoriesOneByUser(int $id, EntityManagerInterface $entityManager): Response
    {

        $announce = $this->announcementsRepository->findBy(['id_user' => $id],['created_at' => 'DESC']);

        if (empty($announce)) {

            return $this->json([
                "status" => "error",
                "message" => "Aucun annonces trouvé"
            ]);

        } else {

            return $this->json([
                "status" => "ok",
                "message" => "Annonces récupérés avec succès",
                "result" => $announce
            ]);

        }
    }

    #[Route('/api/announces/categories/{id}', name: 'app_announces_categories', methods: 'GET')]
    public function getCategoriesOneByCategory(int $id, EntityManagerInterface $entityManager): Response
    {

        $announce = $this->announcementsRepository->findBy(['id_category' => $id],['created_at' => 'DESC']);

        if (empty($announce)) {

            return $this->json([
                "status" => "error",
                "message" => "Aucun annonces trouvé"
            ]);

        } else {

            return $this->json([
                "status" => "ok",
                "message" => "Annonces récupérés avec succès",
                "result" => $announce
            ]);

        }
    }

    #[Route('/api/announces/counts', name: 'app_announces_counts_by_month', methods: 'GET')]
    public function getCountsAnnouncesByMonth(): Response
    {

        $announce = $this->announcementsRepository->createQueryBuilder('a')->select('YEAR(a.created_at) as year, MONTH(a.created_at) as month, COUNT(a.id) as count')->groupBy('year, month')->orderBy('year', 'DESC')->addOrderBy('month', 'DESC')->getQuery()->getArrayResult();

        if (empty($announce)) {

            return $this->json([
                "status" => "error",
                "message" => "Aucun annonces trouvé"
            ]);

        } else {

            return $this->json([
                "status" => "ok",
                "message" => "Annonces récupérés avec succès",
                "result" => $announce
            ]);

        }
    }

    #[Route('/api/announces/member_actifs', name: 'app_announces_member_actif_counts_by_month', methods: 'GET')]
    public function getMemberActifsCountsAnnouncesByMonthTerminated(): Response
    {

        $announce = $this->announcementsRepository->createQueryBuilder('a')->select('YEAR(a.updated_at) as year, MONTH(a.updated_at) as month, COUNT(DISTINCT a.id_user) as count')->groupBy('year, month')->orderBy('year', 'DESC')->addOrderBy('month', 'DESC')->getQuery()->getArrayResult();

        if (empty($announce)) {

            return $this->json([
                "status" => "error",
                "message" => "Aucun annonces trouvé"
            ]);

        } else {

            return $this->json([
                "status" => "ok",
                "message" => "Annonces récupérés avec succès",
                "result" => $announce
            ]);

        }
    }

    #[Route('/api/announces/terminated', name: 'app_announces_terminated_counts_by_month', methods: 'GET')]
    public function getCountsAnnouncesByMonthTerminated(): Response
    {

        $announce = $this->announcementsRepository->createQueryBuilder('a')->select('YEAR(a.updated_at) as year, MONTH(a.updated_at) as month, COUNT(a.id) as count')->where('a.status = :status')->setParameter('status', 'terminated')->groupBy('year, month')->orderBy('year', 'DESC')->addOrderBy('month', 'DESC')->getQuery()->getArrayResult();

        if (empty($announce)) {

            return $this->json([
                "status" => "error",
                "message" => "Aucun annonces trouvé"
            ]);

        } else {

            return $this->json([
                "status" => "ok",
                "message" => "Annonces récupérés avec succès",
                "result" => $announce
            ]);

        }
    }
}
