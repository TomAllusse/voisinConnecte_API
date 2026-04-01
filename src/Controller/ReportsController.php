<?php

namespace App\Controller;

use App\Repository\ReportsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Reports;
use Doctrine\ORM\EntityManagerInterface;

final class ReportsController extends AbstractController
{
    public function __construct(private ReportsRepository $reportsRepository){}

    #[Route('/api/reports', name: 'app_reports_add', methods: 'POST')]
    public function addReports(Request $request, EntityManagerInterface $entityManager): Response
    {

        $data = json_decode($request->getContent(), true);

        if (!$data){
            return $this->json(["status"=>"error", "message"=>"donnée vide"]);
        }

        $reports = new Reports();

        $reports->setIdAnnouncement($data["id_announcement"]);
        $reports->setIdReporter($data["id_reporter"]);
        $reports->setReason($data["reason"]);
        $reports->setComment($data["comment"]);
        $reports->setResolved($data["resolved"]);
        $reports->setResolvedBy($data["resolved_by"]);
        $reports->setCreatedAt($data["created_at"]);

        $entityManager->persist($reports);
        $entityManager->flush();

        return $this->json([
            "status"=>"ok",
            "message" => "Reports ajouter",
            "result" => $reports
        ]);
    }

    #[Route('/api/reports-update/{id}', name: 'app_reports_update', methods: 'PUT')]
    public function updateReports(int $id, Request $request, EntityManagerInterface $entityManager): Response
    {

        $data = json_decode($request->getContent(), true);

        if (!$data){
            return $this->json(["status"=>"error", "message"=>"donnée vide"]);
        }

        $reports = $this->reportsRepository->find($id);

        if (!$reports) {
            return $this->json([
                "status" => "error",
                "message" => "Reports non trouvé"
            ]);
        }

        $reports->setName($data["name"]);
        $reports->setColorHex($data["color_hex"]);
        $reports->setIcon($data["icon"]);

        $entityManager->persist($reports);
        $entityManager->flush();

        return $this->json([
            "status"=>"ok",
            "message" => "Reports ajouter",
            "result" => $reports
        ]);
    }

    #[Route('/api/reports-delete/{id}', name: 'app_reports_delete', methods: 'DELETE')]
    public function deleteReports(int $id, EntityManagerInterface $entityManager): Response
    {

        $reports = $this->reportsRepository->find($id);

        if (!$reports) {
            return $this->json([
                "status" => "error",
                "message" => "Reports non trouvé"
            ]);
        }

        $entityManager->remove($reports);
        $entityManager->flush();

        return $this->json([
            "status" => "ok",
            "message" => "Reports supprimé avec succès"
        ]);
    }

    #[Route('/api/reports', name: 'app_reports_all', methods: 'GET')]
    public function getReportsAll(): Response
    {

        $reports = $this->reportsRepository->findAll();

        if (empty($reports)) {

            return $this->json([
                "status" => "error",
                "message" => "Aucun reports trouvé"
            ]);

        } else {

            return $this->json([
                "status" => "ok",
                "message" => "Reports récupérés avec succès",
                "result" => $reports
            ]);

        }
    }

    #[Route('/api/reports', name: 'app_reports_counts_by_month', methods: 'GET')]
    public function getCountsReportsByMonth(): Response
    {

        $reports = $this->ReportsRepository->createQueryBuilder('a')->select('YEAR(a.created_at) as year, MONTH(a.created_at) as month, COUNT(a.id) as count')->groupBy('year, month')->orderBy('year', 'DESC')->addOrderBy('month', 'DESC')->getQuery()->getArrayResult();

        if (empty($reports)) {

            return $this->json([
                "status" => "error",
                "message" => "Aucun reports trouvé"
            ]);

        } else {

            return $this->json([
                "status" => "ok",
                "message" => "Reports récupérés avec succès",
                "result" => $reports
            ]);

        }
    }
}

