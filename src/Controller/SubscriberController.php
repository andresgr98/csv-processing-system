<?php

namespace App\Controller;

use App\Entity\Subscriber;
use App\Service\SubscriberService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SubscriberController extends AbstractController
{
    public function __construct(private readonly SubscriberService $subscriberService) {}

    #[Route('/api/subscribers', name: 'get_subscribers', methods: ['GET'])]
    public function index(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $filters = [
            'name' => $request->query->get('name'),
            'email' => $request->query->get('email'),
            'age' => $request->query->get('age'),
            'address' => $request->query->get('address'),
        ];

        $page = (int) $request->query->get('page', 1);
        $limit = (int) $request->query->get('limit', 50);

        $subscribers = $this->subscriberService->getSubscribers($filters, $page, $limit);

        $totalRecords = $this->subscriberService->countSubscribers($filters);

        $subscribers = array_map(function (Subscriber $subscriber) {
            return [
                'id' => $subscriber->getId(),
                'name' => $subscriber->getName(),
                'email' => $subscriber->getEmail(),
                'age' => $subscriber->getAge(),
                'address' => $subscriber->getAddress(),
            ];
        }, $subscribers);

        $response = [
            'data' => $subscribers,
            'totalRecords' => $totalRecords,
            'page' => $page,
            'limit' => $limit,
            'totalPages' => ceil($totalRecords / $limit),
        ];

        return new JsonResponse($response);
    }
}
