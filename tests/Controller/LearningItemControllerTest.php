<?php

namespace App\Tests\Controller;

use App\Entity\LearningItem;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LearningItemControllerTest extends WebTestCase
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

        // Clear database
        foreach ($this->em->getRepository(LearningItem::class)->findAll() as $item) {
            $this->em->remove($item);
        }
        $this->em->flush();
    }

    public function testIndexPage(): void
    {
        $this->client->request('GET', '/proj/edu/');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'EduTracker');
    }

    public function testCreateItem(): void
    {
        $crawler = $this->client->request('GET', '/proj/edu/create');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('.page-title', 'Add Learning Item');

        $form = $crawler->selectButton('Save Item')->form();
        $form['learning_item[title]'] = 'Test Item from PHPUnit';
        $form['learning_item[description]'] = 'This is a detailed description for testing create.';
        $form['learning_item[url]'] = 'https://example.com';
        $form['learning_item[status]'] = 'learning';

        $this->client->submit($form);
        $this->assertResponseRedirects('/proj/edu/');

        $crawler = $this->client->followRedirect();
        $this->assertResponseIsSuccessful();

        $this->assertSelectorTextContains(
            '.edu-card h1, .edu-card h2',
            'Test Item from PHPUnit'
        );
        $this->assertSelectorTextContains(
            '.edu-card p',
            'This is a detailed description for testing create.'
        );
    }

    public function testShowItem(): void
    {
        $item = new LearningItem();
        $item->setTitle('Show Item Test Title');
        $item->setDescription('This description is shown in the detail view');
        $item->setStatus('learning');
        $item->setCreatedAt(new \DateTime());

        $this->em->persist($item);
        $this->em->flush();

        $this->client->request('GET', '/proj/edu/' . $item->getId());
        $this->assertResponseIsSuccessful();

        $this->assertSelectorTextContains('.edu-header .page-title', $item->getTitle() ?? '');
        $this->assertSelectorTextContains('.edu-card p', $item->getDescription() ?? '');
    }

    public function testEditItem(): void
    {
        $item = new LearningItem();
        $item->setTitle('Edit Item Test Title');
        $item->setDescription('Original description before edit');
        $item->setStatus('to learn');
        $item->setCreatedAt(new \DateTime());

        $this->em->persist($item);
        $this->em->flush();

        $crawler = $this->client->request('GET', '/proj/edu/' . $item->getId() . '/edit');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('.page-title', 'Edit Learning Item');

        $form = $crawler->selectButton('Update Item')->form();
        $form['learning_item[description]'] = 'Updated description after edit';
        $this->client->submit($form);
        $this->assertResponseRedirects('/proj/edu/');

        $crawler = $this->client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('.edu-card p', 'Updated description after edit');
    }

    public function testDeleteItem(): void
    {
        $item = new LearningItem();
        $item->setTitle('Delete Item Test Title');
        $item->setDescription('This item will be deleted');
        $item->setStatus('learned');
        $item->setCreatedAt(new \DateTime());

        $this->em->persist($item);
        $this->em->flush();

        $crawler = $this->client->request('GET', '/proj/edu/');
        $this->assertResponseIsSuccessful();

        $deleteForm = $crawler
            ->filter('form[action$="/' . $item->getId() . '/delete"]')
            ->first();

        $this->assertGreaterThan(0, $deleteForm->count(), 'Delete form not found');

        $token = $deleteForm->filter('input[name="_token"]')->attr('value');

        $this->client->request('POST', '/proj/edu/' . $item->getId() . '/delete', ['_token' => $token]);
        $this->assertResponseRedirects('/proj/edu/');

        $crawler = $this->client->followRedirect();
        $this->assertResponseIsSuccessful();

        $found = $crawler->filter('.edu-card')->reduce(function ($node) {
            return str_contains($node->text(), 'Delete Item Test Title');
        });

        $this->assertCount(0, $found, 'Deleted item should not appear on index page');
    }
}
