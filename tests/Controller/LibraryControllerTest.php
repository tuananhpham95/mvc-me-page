<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

class LibraryControllerTest extends WebTestCase
{
    /** @var KernelBrowser */
    private $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->client->followRedirects();
    }

    public function testLibraryIndex(): void
    {
        $this->client->request('GET', '/library/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('.library-hero h1', 'Välkommen till Vårt Digitala Bibliotek');
    }

    public function testCreateBook(): void
    {
        $crawler = $this->client->request('GET', '/library/create');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('.library-form-container h1', 'Lägg till en ny bok');

        $form = $crawler->selectButton('Create Book')->form();

        $values = $form->getPhpValues();
        $formName = array_keys($values)[0];

        $values[$formName]['title'] = 'Ny testbok';
        $values[$formName]['isbn'] = '978-91-123-4567-8';
        $values[$formName]['author'] = 'Test Författare';
        $values[$formName]['image'] = 'https://example.com/book.jpg';

        $this->client->request($form->getMethod(), $form->getUri(), $values, $form->getPhpFiles());

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('.alert.alert-success');
        $this->assertSelectorTextContains('.alert.alert-success', 'Book created successfully.');
        $this->assertSelectorTextContains('body', 'Ny testbok');
    }

    public function testShowAllBooks(): void
    {
        $this->createTestBook();

        $this->client->request('GET', '/library/show');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('.library-books h1', 'Alla Böcker');
        $this->assertSelectorTextContains('body', 'Ny testbok');
    }

    public function testUpdateBook(): void
    {
        $this->createTestBook();

        $crawler = $this->client->request('GET', '/library/show');

        $editLink = $crawler->filter('a:contains("Redigera")')->first()->link();
        $crawler = $this->client->click($editLink);

        $this->assertResponseIsSuccessful();

        $form = $crawler->selectButton('Update Book')->form();

        $values = $form->getPhpValues();
        $formName = array_keys($values)[0];

        $values[$formName]['title'] = 'Uppdaterad testbok';

        $this->client->request($form->getMethod(), $form->getUri(), $values, $form->getPhpFiles());

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('.alert.alert-success');
        $this->assertSelectorTextContains('.alert.alert-success', 'Book updated successfully.');
        $this->assertSelectorTextContains('body', 'Uppdaterad testbok');
    }

    public function testDeleteBook(): void
    {
        $this->createTestBook();

        $crawler = $this->client->request('GET', '/library/show');

        $deleteForm = $crawler->filter('button:contains("Radera")')->first()->form();

        $this->client->submit($deleteForm);

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('.alert.alert-success');
        $this->assertSelectorTextContains('.alert.alert-success', 'Book deleted successfully.');
    }

    public function testResetDatabase(): void
    {
        $this->client->request('GET', '/library/reset');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('.alert.alert-success');
        $this->assertSelectorTextContains('.alert.alert-success', 'Database reset successfully.');
        $this->assertSelectorTextContains('body', 'To Kill a Mockingbird');
    }

    private function createTestBook(): void
    {
        $crawler = $this->client->request('GET', '/library/create');

        $form = $crawler->selectButton('Create Book')->form();

        $values = $form->getPhpValues();
        $formName = array_keys($values)[0];

        $values[$formName]['title'] = 'Ny testbok';
        $values[$formName]['isbn'] = '978-91-123-4567-8';
        $values[$formName]['author'] = 'Test Författare';
        $values[$formName]['image'] = 'https://example.com/book.jpg';

        $this->client->request($form->getMethod(), $form->getUri(), $values, $form->getPhpFiles());
    }
}
