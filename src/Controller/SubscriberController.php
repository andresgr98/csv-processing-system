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
        $name = $request->query->get('name');
        $email = $request->query->get('email');
        $age = $request->query->get('age');
        $address = $request->query->get('address');

        $filters = [
            'name' => $request->query->get('name'),
            'email' => $request->query->get('email'),
            'age' => $request->query->get('age'),
            'address' => $request->query->get('address'),
        ];

        $subscribers = $this->subscriberService->getSubscribers($filters);

        $subscribers = array_map(function (Subscriber $subscriber) {
            return [
                'id' => $subscriber->getId(),
                'name' => $subscriber->getName(),
                'email' => $subscriber->getEmail(),
                'age' => $subscriber->getAge(),
                'address' => $subscriber->getAddress(),
            ];
        }, $subscribers);
        return new JsonResponse($subscribers);
    }
}
