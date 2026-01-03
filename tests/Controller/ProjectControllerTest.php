<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ProjectControllerTest extends WebTestCase
{
    public function testProjectHomePage(): void
    {
        $client = static::createClient();
        $client->request('GET', '/proj/home');

        $this->assertResponseIsSuccessful();

        $this->assertSelectorTextContains('.hero h1', 'Welcome to EduTracker');
        $this->assertSelectorTextContains('.hero p', 'Track your learning progress effectively');
        $this->assertSelectorExists('.cta-buttons a:contains("View Learning Items")');
        $this->assertSelectorExists('.cta-buttons a:contains("Add New Item")');
        $this->assertSelectorExists('.feature-card');
    }

    public function testProjectAboutPage(): void
    {
        $client = static::createClient();
        $client->request('GET', '/proj/about');

        $this->assertResponseIsSuccessful();

        $this->assertSelectorTextContains('.about-hero h1', 'About EduTracker');
        $this->assertSelectorTextContains('.about-hero p', 'EduTracker is an educational tracking application');
        $this->assertSelectorExists('a:contains("View Learning Items")');
        $this->assertSelectorExists('ul li:contains("Organize and track learning goals efficiently")');
        $this->assertSelectorTextContains('.about-details p', 'Symfony, Twig, Doctrine ORM');
    }
} 