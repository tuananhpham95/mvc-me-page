<?php

namespace App\Tests\Repository;

use App\Entity\Book;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class BookRepositoryTest extends TestCase
{
    private ManagerRegistry|MockObject $registry;
    private BookRepository $repository;

    protected function setUp(): void
    {
        $this->registry = $this->createMock(ManagerRegistry::class);
        $this->repository = new BookRepository($this->registry);
    }

    /**
     * Test that the repository can be instantiated.
     */
    public function testRepositoryInstantiation(): void
    {
        $this->assertInstanceOf(BookRepository::class, $this->repository);
    }
}
