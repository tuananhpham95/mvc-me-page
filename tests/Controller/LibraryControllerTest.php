<?php

namespace App\Tests\Controller;

use App\Entity\Book;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\ORM\EntityManagerInterface;

class LibraryControllerTest extends WebTestCase
{
    public function testCreatePageLoadsSuccessfully(): void
    {
        $client = static::createClient();

        // Go to the create page
        $client->request('GET', '/library/create');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Lägg till en ny bok');

        // Check form fields exist
        $this->assertSelectorExists('input[name="form[title]"]');
        $this->assertSelectorExists('input[name="form[isbn]"]');
        $this->assertSelectorExists('input[name="form[author]"]');
        $this->assertSelectorExists('input[name="form[image]"]');
    }

    public function testCreateBookSubmit(): void
    {
        $client = static::createClient();
        $container = static::getContainer();

        /** @var EntityManagerInterface $entityManager */
        $entityManager = $container->get(EntityManagerInterface::class);

        // Ensure database is clean
        $entityManager->createQuery('DELETE FROM App\Entity\Book')->execute();

        // Load the form page
        $crawler = $client->request('GET', '/library/create');

        // Select the form
        $form = $crawler->selectButton('Create Book')->form([
            'form[title]' => 'Test Driven Development',
            'form[isbn]' => '9780321146533',
            'form[author]' => 'Kent Beck',
            'form[image]' => 'https://example.com/tdd.jpg',
        ]);

        // Submit the form
        $client->submit($form);

        // Should redirect back to "show all" page
        $this->assertResponseRedirects('/library/show');

        // Follow redirection
        $client->followRedirect();

        // Flash message check
        $this->assertSelectorExists('.alert.alert-success');
        $this->assertSelectorTextContains('.alert.alert-success', 'Book created successfully.');

        // Verify book is actually persisted
        $book = $entityManager->getRepository(Book::class)->findOneBy([
            'title' => 'Test Driven Development',
        ]);

        $this->assertNotNull($book);
        $this->assertSame('9780321146533', $book->getIsbn());
    }
}
