<?php

namespace App\Tests\Entity;

use App\Entity\Category;
use App\Entity\LearningItem;
use DateTime;
use PHPUnit\Framework\TestCase;

class LearningItemTest extends TestCase
{
    private LearningItem $item;

    protected function setUp(): void
    {
        $this->item = new LearningItem();
    }

    public function testIdIsNullByDefault(): void
    {
        $this->assertNull($this->item->getId(), 'ID should be null for new entity');
    }

    public function testTitleCanBeSetAndRetrieved(): void
    {
        $title = 'Symfony Deep Dive';
        $this->item->setTitle($title);
        $this->assertSame($title, $this->item->getTitle());
    }

    public function testTitleCanBeNull(): void
    {
        $this->item->setTitle(null);
        $this->assertNull($this->item->getTitle());
    }

    public function testDescriptionCanBeSetAndRetrieved(): void
    {
        $description = 'Advanced concepts in Symfony framework including services, events, and security.';
        $this->item->setDescription($description);
        $this->assertSame($description, $this->item->getDescription());
    }

    public function testDescriptionCanBeNull(): void
    {
        $this->item->setDescription(null);
        $this->assertNull($this->item->getDescription());
    }

    public function testUrlCanBeSetAndRetrieved(): void
    {
        $url = 'https://symfony.com/doc/current/index.html';
        $this->item->setUrl($url);
        $this->assertSame($url, $this->item->getUrl());
    }

    public function testUrlCanBeNull(): void
    {
        $this->item->setUrl(null);
        $this->assertNull($this->item->getUrl());
    }

    public function testStatusDefaultsToLearning(): void
    {
        $this->assertSame('learning', $this->item->getStatus());
    }

    public function testStatusCanBeChanged(): void
    {
        $this->item->setStatus('learned');
        $this->assertSame('learned', $this->item->getStatus());

        $this->item->setStatus('to learn');
        $this->assertSame('to learn', $this->item->getStatus());
    }

    public function testCreatedAtIsSetInConstructor(): void
    {
        $createdAt = $this->item->getCreatedAt();
        $this->assertInstanceOf(\DateTimeInterface::class, $createdAt);
        $this->assertInstanceOf(DateTime::class, $createdAt);

        $now = new DateTime();
        $diff = $now->getTimestamp() - $createdAt->getTimestamp();
        $this->assertLessThan(2, $diff, 'createdAt should be set to approximately now');
    }

    public function testSetCreatedAt(): void
    {
        $date = new DateTime('2025-01-01 12:00:00');
        $this->item->setCreatedAt($date);
        $this->assertSame($date, $this->item->getCreatedAt());
    }

    public function testCategoryCanBeSetAndRetrieved(): void
    {
        $category = new Category();
        $category->setName('Web Development');

        $this->item->setCategory($category);

        $this->assertSame($category, $this->item->getCategory());
        $this->assertSame('Web Development', $this->item->getCategory()->getName());
    }

    public function testCategoryCanBeNull(): void
    {
        $this->item->setCategory(null);
        $this->assertNull($this->item->getCategory());
    }

    public function testFluentInterface(): void
    {
        $title = 'Docker Basics';
        $status = 'to learn';
        $category = new Category();
        $category->setName('DevOps');

        $result = $this->item
            ->setTitle($title)
            ->setStatus($status)
            ->setCategory($category);

        $this->assertSame($this->item, $result, 'All setters should return $this for fluent interface');

        $this->assertSame($title, $this->item->getTitle());
        $this->assertSame($status, $this->item->getStatus());
        $this->assertSame($category, $this->item->getCategory());
    }

    public function testInstanceOfLearningItem(): void
    {
        $this->assertInstanceOf(LearningItem::class, $this->item);
    }
}
