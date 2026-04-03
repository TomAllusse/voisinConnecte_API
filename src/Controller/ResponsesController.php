<?php

namespace App\Controller;

use App\Repository\ResponsesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Responses;
use Doctrine\ORM\EntityManagerInterface;

final class ResponsesController extends AbstractController
{
    public function __construct(private ResponsesRepository $responsesRepository){}

    #[Route('/api/responses', name: 'app_responses_add', methods: 'POST')]
    public function addResponses(Request $request, EntityManagerInterface $entityManager): Response
    {

        $data = json_decode($request->getContent(), true);

        if (!$data){
            return $this->json(["status"=>"error", "message"=>"donnée vide"]);
        }

        $responses = new Responses();

        $responses->setIdAnnouncement($data["id_announcement"]);
        $responses->setIdReporter($data["id_reporter"]);
        $responses->setReason($data["reason"]);
        $responses->setComment($data["comment"]);
        $responses->setResolved($data["resolved"]);
        $responses->setResolvedBy($data["resolved_by"]);
        $responses->setCreatedAt($data["created_at"]);

        $entityManager->persist($responses);
        $entityManager->flush();

        return $this->json([
            "status"=>"ok",
            "message" => "Responses ajouter",
            "result" => $responses
        ]);
    }

    #[Route('/api/responses-update/{id}', name: 'app_responses_update', methods: 'PUT')]
    public function updateResponses(int $id, Request $request, EntityManagerInterface $entityManager): Response
    {

        $data = json_decode($request->getContent(), true);

        if (!$data){
            return $this->json(["status"=>"error", "message"=>"donnée vide"]);
        }

        $responses = $this->responsesRepository->find($id);

        if (!$responses) {
            return $this->json([
                "status" => "error",
                "message" => "Responses non trouvé"
            ]);
        }

        $responses->setName($data["name"]);
        $responses->setColorHex($data["color_hex"]);
        $responses->setIcon($data["icon"]);

        $entityManager->persist($responses);
        $entityManager->flush();

        return $this->json([
            "status"=>"ok",
            "message" => "Responses ajouter",
            "result" => $responses
        ]);
    }

    #[Route('/api/responses-delete/{id}', name: 'app_responses_delete', methods: 'DELETE')]
    public function deleteResponses(int $id, EntityManagerInterface $entityManager): Response
    {

        $responses = $this->responsesRepository->find($id);

        if (!$responses) {
            return $this->json([
                "status" => "error",
                "message" => "Responses non trouvé"
            ]);
        }

        $entityManager->remove($responses);
        $entityManager->flush();

        return $this->json([
            "status" => "ok",
            "message" => "Responses supprimé avec succès"
        ]);
    }

    #[Route('/api/responses', name: 'app_responses_all', methods: 'GET')]
    public function getResponsesAll(): Response
    {

        $responses = $this->responsesRepository->findAll();

        if (empty($responses)) {

            return $this->json([
                "status" => "error",
                "message" => "Aucun responses trouvé"
            ]);

        } else {

            return $this->json([
                "status" => "ok",
                "message" => "Responses récupérés avec succès",
                "result" => $responses
            ]);

        }
    }
}

