<?php

namespace App\Tests\Controller;

use App\Entity\Category;
use App\Entity\LearningItem;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LearningItemApiControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $em;

    protected function setUp(): void
    {
        $this->client = static::createClient();

        $em = static::getContainer()->get(EntityManagerInterface::class);
        if (!$em instanceof EntityManagerInterface) {
            throw new \RuntimeException('EntityManager not found in container');
        }
        $this->em = $em;

        // Clear LearningItems
        foreach ($this->em->getRepository(LearningItem::class)->findAll() as $item) {
            $this->em->remove($item);
        }

        // Clear Categories
        foreach ($this->em->getRepository(Category::class)->findAll() as $category) {
            $this->em->remove($category);
        }

        $this->em->flush();
    }

    public function testApiIndexReturnsEmptyArrayWhenNoItems(): void
    {
        $this->client->request('GET', '/proj/api/items');
        $this->assertResponseIsSuccessful();

        $content = $this->client->getResponse()->getContent();
        if ($content === false) {
            throw new \RuntimeException('Response content is false, cannot assert JSON');
        }

        $this->assertJsonStringEqualsJsonString('[]', (string) $content);
    }

    public function testApiIndexReturnsItems(): void
    {
        $item = new LearningItem();
        $item->setTitle('API Test Item');
        $item->setDescription('Description from API test');
        $item->setStatus('learning');
        $item->setCreatedAt(new \DateTime());

        $this->em->persist($item);
        $this->em->flush();

        $this->client->request('GET', '/proj/api/items');
        $this->assertResponseIsSuccessful();

        $content = $this->client->getResponse()->getContent();
        if ($content === false) {
            throw new \RuntimeException('Response content is false, cannot decode JSON');
        }

        $data = json_decode((string) $content, true);
        $this->assertIsArray($data);
        $this->assertCount(1, $data);
        $this->assertArrayHasKey('title', $data[0]);
        $this->assertSame('API Test Item', $data[0]['title']);
        $this->assertArrayHasKey('id', $data[0]);
        $this->assertArrayHasKey('description', $data[0]);
        $this->assertArrayHasKey('createdAt', $data[0]);
    }

    public function testApiShowItem(): void
    {
        $item = new LearningItem();
        $item->setTitle('Show Item API');
        $item->setDescription('Visible in API show');
        $item->setStatus('to learn');
        $item->setCreatedAt(new \DateTime());

        $this->em->persist($item);
        $this->em->flush();

        $this->client->request('GET', '/proj/api/items/' . $item->getId());
        $this->assertResponseIsSuccessful();

        $content = $this->client->getResponse()->getContent();
        if ($content === false) {
            throw new \RuntimeException('Response content is false, cannot decode JSON');
        }

        $data = json_decode((string) $content, true);
        $this->assertIsArray($data);
        $this->assertArrayHasKey('title', $data);
        $this->assertSame('Show Item API', $data['title']);
        $this->assertArrayHasKey('description', $data);
        $this->assertSame('Visible in API show', $data['description']);
    }

    public function testApiShowNotFound(): void
    {
        $this->client->request('GET', '/proj/api/items/999999');
        $this->assertResponseStatusCodeSame(404);

        $content = $this->client->getResponse()->getContent();
        if ($content === false) {
            throw new \RuntimeException('Response content is false, cannot assert JSON');
        }

        $this->assertJsonStringEqualsJsonString(
            json_encode(['error' => 'Item not found'], JSON_THROW_ON_ERROR),
            (string) $content
        );
    }

    public function testApiCreateItemWithJson(): void
    {
        $payload = [
            'title' => 'Created via JSON API',
            'description' => 'This item was created using JSON payload',
            'status' => 'learning',
            'url' => 'https://example.com'
        ];

        $jsonPayload = json_encode($payload, JSON_THROW_ON_ERROR);

        $this->client->request(
            'POST',
            '/proj/api/items',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            $jsonPayload
        );

        $this->assertResponseStatusCodeSame(201);

        $content = $this->client->getResponse()->getContent();
        if ($content === false) {
            throw new \RuntimeException('Response content is false, cannot decode JSON');
        }

        $response = json_decode((string) $content, true);
        $this->assertIsArray($response);
        $this->assertArrayHasKey('message', $response);
        $this->assertSame('Item created', $response['message']);
        $this->assertArrayHasKey('id', $response);

        $createdItem = $this->em->find(LearningItem::class, $response['id']);
        $this->assertNotNull($createdItem);
        $this->assertSame('Created via JSON API', $createdItem->getTitle());
    }

    public function testApiCreateItemWithFormData(): void
    {
        $this->client->request(
            'POST',
            '/proj/api/items',
            [
                'title' => 'Created via form data',
                'description' => 'Testing form fallback',
                'status' => 'learned'
            ]
        );

        $this->assertResponseStatusCodeSame(201);

        $content = $this->client->getResponse()->getContent();
        if ($content === false) {
            throw new \RuntimeException('Response content is false, cannot decode JSON');
        }

        $response = json_decode((string) $content, true);
        $this->assertIsArray($response);
        $this->assertSame('Item created', $response['message']);

        $createdItem = $this->em->find(LearningItem::class, $response['id']);
        $this->assertNotNull($createdItem);
        $this->assertSame('learned', $createdItem->getStatus());
    }

    public function testApiByStatus(): void
    {
        $item1 = new LearningItem();
        $item1->setTitle('Learning Item');
        $item1->setStatus('learning');
        $item2 = new LearningItem();
        $item2->setTitle('Learned Item');
        $item2->setStatus('learned');

        $this->em->persist($item1);
        $this->em->persist($item2);
        $this->em->flush();

        $this->client->request('GET', '/proj/api/status/learning');
        $this->assertResponseIsSuccessful();

        $content = $this->client->getResponse()->getContent();
        if ($content === false) {
            throw new \RuntimeException('Response content is false, cannot decode JSON');
        }

        $data = json_decode((string) $content, true);
        $this->assertIsArray($data);
        $this->assertCount(1, $data);
        $this->assertArrayHasKey('title', $data[0]);
        $this->assertSame('Learning Item', $data[0]['title']);
    }

    public function testApiStats(): void
    {
        $item1 = new LearningItem();
        $item1->setStatus('learning');
        $item2 = new LearningItem();
        $item2->setStatus('learned');
        $item3 = new LearningItem();
        $item3->setStatus('to learn');

        $this->em->persist($item1);
        $this->em->persist($item2);
        $this->em->persist($item3);
        $this->em->flush();

        $this->client->request('GET', '/proj/api/stats');
        $this->assertResponseIsSuccessful();

        $content = $this->client->getResponse()->getContent();
        if ($content === false) {
            throw new \RuntimeException('Response content is false, cannot decode JSON');
        }

        $data = json_decode((string) $content, true);
        $this->assertIsArray($data);
        $this->assertSame(1, $data['learning']);
        $this->assertSame(1, $data['learned']);
        $this->assertSame(1, $data['to_learn']);
    }

    public function testApiCreateWithCategory(): void
    {
        $category = new Category();
        $category->setName('API Category');

        $this->em->persist($category);
        $this->em->flush();

        $payload = [
            'title' => 'Item with Category',
            'category_id' => $category->getId()
        ];

        $jsonPayload = json_encode($payload, JSON_THROW_ON_ERROR);

        $this->client->request(
            'POST',
            '/proj/api/items',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            $jsonPayload
        );

        $this->assertResponseStatusCodeSame(201);

        $content = $this->client->getResponse()->getContent();
        if ($content === false) {
            throw new \RuntimeException('Response content is false, cannot decode JSON');
        }

        $response = json_decode((string) $content, true);
        $this->assertIsArray($response);
        $this->assertArrayHasKey('category_id', $response);
        $this->assertSame($category->getId(), $response['category_id']);
    }
}
