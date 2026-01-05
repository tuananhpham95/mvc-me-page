<?php

namespace App\Controller;

use App\Entity\LearningItem;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * JSON API Controller for EduTracker Learning Items.
 *
 * Provides REST-like endpoints to manage learning items:
 * - List all items
 * - Get single item by ID
 * - Create new item (supports both JSON payload and form data)
 * - Filter items by status
 * - Get statistics by status
 *
 * Base route: /proj/api
 */
#[Route('/proj/api')]
class LearningItemApiController extends AbstractController
{
    /**
     * Get all learning items.
     *
     * @param EntityManagerInterface $em Doctrine entity manager
     *
     * @return JsonResponse Array of all learning items
     */
    #[Route('/items', name: 'api_items_index', methods: ['GET'])]
    public function index(EntityManagerInterface $em): JsonResponse
    {
        $items = $em->getRepository(LearningItem::class)->findAll();

        $data = array_map(fn (LearningItem $item) => [
            'id' => $item->getId(),
            'title' => $item->getTitle(),
            'description' => $item->getDescription(),
            'status' => $item->getStatus(),
            'url' => $item->getUrl(),
            'category' => $item->getCategory() ? $item->getCategory()->getName() : null,
            'createdAt' => $item->getCreatedAt()->format('Y-m-d H:i'),
        ], $items);

        return $this->json($data);
    }

    /**
     * Get a single learning item by ID.
     *
     * @param LearningItem|null $item Item resolved by param converter
     *
     * @return JsonResponse Item details or error 404
     */
    #[Route('/items/{id}', name: 'api_items_show', methods: ['GET'])]
    public function show(?LearningItem $item): JsonResponse
    {
        if (!$item) {
            return $this->json(['error' => 'Item not found'], 404);
        }

        return $this->json([
            'id' => $item->getId(),
            'title' => $item->getTitle(),
            'description' => $item->getDescription(),
            'status' => $item->getStatus(),
            'url' => $item->getUrl(),
            'category' => $item->getCategory() ? $item->getCategory()->getName() : null,
            'createdAt' => $item->getCreatedAt()->format('Y-m-d H:i')
        ]);
    }

    /**
     * Create a new learning item.
     *
     * Accepts JSON or form data. Optional category_id to link to a category.
     *
     * @param Request                $request HTTP request
     * @param EntityManagerInterface $em      Doctrine entity manager
     *
     * @return JsonResponse Success message with new item ID (201)
     */
    #[Route('/items', name: 'api_items_create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em): JsonResponse
    {
        // Parse JSON first
        $data = json_decode($request->getContent(), true);

        // Fallback to form data if JSON invalid
        if (!is_array($data)) {
            $data = $request->request->all();
        }

        if (empty($data)) {
            return $this->json(['error' => 'No data provided'], 400);
        }

        $item = new LearningItem();

        // Safe access with type checking to satisfy PHPStan
        $title = $data['title'] ?? null;
        $item->setTitle(is_string($title) || $title === null ? $title : null);

        $description = $data['description'] ?? null;
        $item->setDescription(is_string($description) || $description === null ? $description : null);

        $url = $data['url'] ?? null;
        $item->setUrl(is_string($url) || $url === null ? $url : null);

        $status = $data['status'] ?? 'learning';
        $item->setStatus(is_string($status) ? $status : 'learning');

        // Category handling - safe int cast
        if (isset($data['category_id']) && is_numeric($data['category_id'])) {
            $categoryId = (int) $data['category_id'];
            $category = $em->getRepository(\App\Entity\Category::class)->find($categoryId);
            if ($category) {
                $item->setCategory($category);
            }
        }

        $em->persist($item);
        $em->flush();

        return $this->json([
            'message' => 'Item created',
            'id' => $item->getId(),
            'category_id' => $item->getCategory()?->getId()
        ], 201);
    }

    /**
     * Get items by status.
     *
     * @param string                 $status Status filter
     * @param EntityManagerInterface $em     Doctrine entity manager
     *
     * @return JsonResponse Filtered items
     */
    #[Route('/status/{status}', name: 'api_items_by_status', methods: ['GET'])]
    public function byStatus(string $status, EntityManagerInterface $em): JsonResponse
    {
        $items = $em->getRepository(LearningItem::class)->findBy(['status' => $status]);

        $data = array_map(fn (LearningItem $item) => [
            'id' => $item->getId(),
            'title' => $item->getTitle(),
        ], $items);

        return $this->json($data);
    }

    /**
     * Get learning statistics by status.
     *
     * @param EntityManagerInterface $em Doctrine entity manager
     *
     * @return JsonResponse Counts per status
     */
    #[Route('/stats', name: 'api_items_stats', methods: ['GET'])]
    public function stats(EntityManagerInterface $em): JsonResponse
    {
        $repo = $em->getRepository(LearningItem::class);

        return $this->json([
            'learning' => count($repo->findBy(['status' => 'learning'])),
            'learned' => count($repo->findBy(['status' => 'learned'])),
            'to_learn' => count($repo->findBy(['status' => 'to learn'])),
        ]);
    }
}
