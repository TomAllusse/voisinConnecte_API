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
use App\Repository\RefreshTokensRepository;
use App\Repository\CategoriesRepository;

final class AnnouncementsController extends AbstractController
{
    public function __construct(private AnnouncementsRepository $announcementsRepository)
    {
    }

    #[Route('/api/announces', name: 'app_announces_add', methods: ['POST'])]
    public function addAnnounce(
        Request $request,
        EntityManagerInterface $entityManager,
        RefreshTokensRepository $tokenRepo,
        CategoriesRepository $categoryRepo
    ): Response {
        $data = json_decode($request->getContent(), true);
        $authHeader = $request->headers->get('Authorization');

        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            return $this->json(["status" => "error", "message" => "Token manquant"], 401);
        }

        $tokenValue = substr($authHeader, 7);
        $tokenEntity = $tokenRepo->findOneBy(['token_hash' => $tokenValue, 'revoked' => false]);

        if (!$tokenEntity || $tokenEntity->getExpiredAt() < new \DateTimeImmutable()) {
            return $this->json(["status" => "error", "message" => "Session expirée"], 401);
        }

        $user = $tokenEntity->getIdUser();

        if (!$data) {
            return $this->json(["status" => "error", "message" => "Donnée vide"], 400);
        }

        try {
            $announce = new Announcements();
            $announce->setIdUser($user);

            $category = $categoryRepo->find($data["id_category"]);
            if (!$category) {
                return $this->json(["status" => "error", "message" => "Catégorie introuvable"], 404);
            }
            $announce->setIdCategory($category);

            $announce->setTitle($data["title"]);
            $announce->setDescription($data["description"]);
            $announce->setIsPaid($data["is_paid"] ?? false);

            if ($announce->isPaid() && isset($data['price_unit'])) {
                $announce->setPrice($data["price"]);
                $announce->setPriceUnit(Type_Unit::from($data['price_unit']));
            } else {
                $announce->setPrice(null);
                $announce->setPriceUnit(null);
            }

            $announce->setStatus(Status_Announcement::Pending);
            $announce->setIsActive(true);
            $announce->setCreatedAt(new \DateTimeImmutable());

            $entityManager->persist($announce);
            $entityManager->flush();

            return $this->json([
                "status" => "ok",
                "message" => "Annonce ajoutée avec succès",
                "result" => $announce
            ], 201, [], ['groups' => 'announcements:read']);

        } catch (\Exception $e) {
            return $this->json([
                "status" => "error",
                "message" => "Erreur : " . $e->getMessage()
            ], 500);
        }
    }

    #[Route('/api/announces-update/{id}', name: 'app_announces_update', methods: ['PUT'])]
    public function updateAnnounce(int $id, Request $request, EntityManagerInterface $entityManager): Response
    {
        $data = json_decode($request->getContent(), true);
        $user = $this->getUser();

        if (!$user) {
            return $this->json(["status" => "error", "message" => "Non autorisé"], 401);
        }

        $announce = $this->announcementsRepository->find($id);

        if (!$announce) {
            return $this->json(["status" => "error", "message" => "Annonce non trouvée"], 404);
        }

        if ($announce->getIdUser()->getId() !== $user->getId()) {
            return $this->json(["status" => "error", "message" => "Accès refusé"], 403);
        }

        $announce->setTitle($data["title"]);
        $announce->setDescription($data["description"]);
        $announce->setIsPaid($data["is_paid"]);
        $announce->setPrice($data["price"]);

        if ($data["is_paid"] && isset($data['price_unit'])) {
            $announce->setPriceUnit(Type_Unit::from($data['price_unit']));
        }

        $announce->setIsActive($data["is_active"]);
        $announce->setUpdatedAt(now());

        $entityManager->flush();

        return $this->json([
            "status" => "ok",
            "message" => "Annonce mise à jour",
            "result" => $announce
        ], 200, [], ['groups' => 'announcements:read']);
    }

    #[Route('/api/announces-delete/{id}', name: 'app_announces_delete', methods: 'DELETE')]
    public function deleteAnnounce(int $id, EntityManagerInterface $entityManager): Response
    {
        $announce = $this->announcementsRepository->find($id);

        if (!$announce) {
            return $this->json(["status" => "error", "message" => "Non trouvée"], 404);
        }

        $entityManager->remove($announce);
        $entityManager->flush();

        return $this->json(["status" => "ok", "message" => "Supprimée"]);
    }

    #[Route('/api/announces', name: 'app_announces_all', methods: 'GET')]
    public function getAnnouncesAll(): Response
    {
        $announce = $this->announcementsRepository->findAll();

        if (empty($announce)) {
            return $this->json(["status" => "error", "message" => "Aucune annonce"], 404);
        }

        return $this->json([
            "status" => "ok",
            "result" => $announce
        ], 200, [], ['groups' => 'announcements:read']);
    }

    #[Route('/api/announces/user/{id}', name: 'app_announces_user', methods: 'GET')]
    public function getAnnounceOneByUser(int $id): Response
    {
        $announce = $this->announcementsRepository->findBy(
            ['id_user' => $id],
            ['created_at' => 'DESC']
        );

        return $this->json([
            "status" => "ok",
            "result" => $announce
        ], 200, [], ['groups' => 'announcements:read']);
    }

    #[Route('/api/announces/categories/{id}', name: 'app_announces_categories', methods: 'GET')]
    public function getAnnouncesByCategory(int $id): Response
    {
        $announce = $this->announcementsRepository->findBy(
            ['id_category' => $id],
            ['created_at' => 'DESC']
        );

        return $this->json([
            "status" => "ok",
            "result" => $announce
        ], 200, [], ['groups' => 'announcements:read']);
    }

    #[Route('/api/announces/counts', name: 'app_announces_counts_by_month', methods: 'GET')]
    public function getCountsAnnouncesByMonth(): Response
    {
        $results = $this->announcementsRepository->createQueryBuilder('a')
            ->select('YEAR(a.created_at) as HIDDEN year', 'MONTH(a.created_at) as HIDDEN month', 'COUNT(a.id) as count')
            ->groupBy('year, month')
            ->orderBy('year', 'DESC')
            ->addOrderBy('month', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getArrayResult();

        $count = !empty($results) ? (int)$results[0]['count'] : 0;

        return $this->json(["status" => "ok", "result" => $count]);
    }

    #[Route('/api/announces/member_actifs', name: 'app_announces_member_actif_counts_by_month', methods: 'GET')]
    public function getMemberActifsCountsAnnouncesByMonth(): Response
    {
        $results = $this->announcementsRepository->createQueryBuilder('a')
            ->select('YEAR(a.updated_at) as HIDDEN year', 'MONTH(a.updated_at) as HIDDEN month', 'COUNT(DISTINCT a.id_user) as count')
            ->groupBy('year, month')
            ->orderBy('year', 'DESC')
            ->addOrderBy('month', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getArrayResult();

        $count = !empty($results) ? (int)$results[0]['count'] : 0;

        return $this->json(["status" => "ok", "result" => $count]);
    }

    #[Route('/api/announces/terminated', name: 'app_announces_terminated_counts_by_month', methods: 'GET')]
    public function getCountsAnnouncesByMonthTerminated(): Response
    {
        $results = $this->announcementsRepository->createQueryBuilder('a')
            ->select('YEAR(a.updated_at) as HIDDEN year', 'MONTH(a.updated_at) as HIDDEN month', 'COUNT(a.id) as count')
            ->where('a.status = :status')
            ->setParameter('status', Status_Announcement::Terminated)
            ->groupBy('year, month')
            ->orderBy('year', 'DESC')
            ->addOrderBy('month', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getArrayResult();

        $count = !empty($results) ? (int)$results[0]['count'] : 0;

        return $this->json(["status" => "ok", "result" => $count]);
    }

    #[Route('/api/announces/stats/{id}', name: 'app_announces_total_count', methods: ['GET'])]
    public function getCountsAnnouncesByUser(int $id): Response
    {
        $count = $this->announcementsRepository->createQueryBuilder('a')
            ->select('COUNT(a.id)')
            ->where('a.id_user = :idUser')
            ->setParameter('idUser', $id)
            ->getQuery()
            ->getSingleScalarResult();

        return $this->json([
            "status" => "ok",
            "result" => (int)$count
        ]);
    }

    #[Route('/api/announces/stats/terminated/{id}', name: 'app_announces_terminated_count', methods: ['GET'])]
    public function getCountsAnnouncesByUserTerminated(int $id): Response
    {
        $count = $this->announcementsRepository->createQueryBuilder('a')
            ->select('COUNT(a.id)')
            ->where('a.id_user = :idUser')
            ->andWhere('a.status = :status')
            ->setParameter('idUser', $id)
            ->setParameter('status', 'terminated')
            ->getQuery()
            ->getSingleScalarResult();

        return $this->json([
            "status" => "ok",
            "result" => (int)$count
        ]);
    }
}
