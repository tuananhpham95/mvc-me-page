<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

class LibraryControllerTest extends WebTestCase
{
    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function testIndexLoads(): void
    {
        $crawler = $this->client->request('GET', '/library/');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('.intro h1', 'Digital Library');
        $this->assertSelectorExists('a.btn.btn-primary');
        $this->assertSelectorExists('a.btn.btn-outline');
    }

    public function testShowAllLoads(): void
    {
        $this->client->request('GET', '/library/reset');
        $crawler = $this->client->request('GET', '/library/show');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('section.library-books h1', 'Alla Böcker');
        $this->assertSelectorExists('.book-grid');
    }

    public function testCreateBookForm(): void
    {
        $crawler = $this->client->request('GET', '/library/create');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('form[name="form"]');
        $this->assertSelectorExists('input[name="form[title]"]');
    }

    public function testUpdateBookForm(): void
    {
        $this->client->request('GET', '/library/reset');
        $crawler = $this->client->request('GET', '/library/show');

        $editLinks = $crawler->filter('a[href*="/update/"]');
        $this->assertGreaterThan(0, $editLinks->count(), 'No edit link found');

        $editLink = $editLinks->first()->link();
        $crawler = $this->client->click($editLink);
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('form');
    }

    public function testShowBookDetails(): void
    {
        $this->client->request('GET', '/library/reset');
        $crawler = $this->client->request('GET', '/library/show');

        $detailLinks = $crawler->filter('a[href*="/show/"]:not(a[href*="/update"]):not(a[href*="/delete"])');
        $this->assertGreaterThan(0, $detailLinks->count(), 'No detail link found');

        $detailLink = $detailLinks->first()->link();
        $crawler = $this->client->click($detailLink);
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('section.book-details');
        $this->assertSelectorExists('section.book-details h1');
        $this->assertSelectorTextContains('section.book-details', 'ISBN');
        $this->assertSelectorTextContains('section.book-details', 'Författare');
    }

    public function testCreateAndDeleteBook(): void
    {
        $this->client->request('GET', '/library/reset');
        $crawler = $this->client->request('GET', '/library/create');

        $form = $crawler->selectButton('Create Book')->form();
        $form['form[title]'] = 'Ny bok skapad av PHPUnit';
        $form['form[author]'] = 'PHPUnit Författare';
        $form['form[isbn]'] = '7777777777777';

        $this->client->submit($form);
        $this->assertResponseRedirects('/library/show');

        $crawler = $this->client->followRedirect();
        $this->assertSelectorTextContains('.book-grid', 'Ny bok skapad av PHPUnit');

        $deleteForm = $crawler->filter('form[action*="/delete"]')->last()->form();
        $this->client->submit($deleteForm);

        $this->assertResponseRedirects('/library/show');
        $this->client->followRedirect();
        $this->assertSelectorTextNotContains('.book-grid', 'Ny bok skapad av PHPUnit');
    }
}
