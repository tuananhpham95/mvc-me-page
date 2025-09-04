<?php

namespace App\Tests\Entity;

use App\Entity\Book;
use PHPUnit\Framework\TestCase;

class BookTest extends TestCase
{
    /**
     * Test creating a Book object.
     */
    public function testCreateBook(): void
    {
        $book = new Book();
        $this->assertInstanceOf(Book::class, $book);
    }

    /**
     * Test setting and getting the title.
     */
    public function testTitle(): void
    {
        $book = new Book();
        $book->setTitle('To Kill a Mockingbird');
        $this->assertSame('To Kill a Mockingbird', $book->getTitle());
    }

    /**
     * Test setting and getting the ISBN.
     */
    public function testIsbn(): void
    {
        $book = new Book();
        $book->setIsbn('9780446310789');
        $this->assertSame('9780446310789', $book->getIsbn());
    }

    /**
     * Test setting and getting the author.
     */
    public function testAuthor(): void
    {
        $book = new Book();
        $book->setAuthor('Harper Lee');
        $this->assertSame('Harper Lee', $book->getAuthor());
    }

    /**
     * Test setting and getting the image.
     */
    public function testImage(): void
    {
        $book = new Book();
        $book->setImage('/assets/images/to-kill-a-mockingbird.jpeg');
        $this->assertSame('/assets/images/to-kill-a-mockingbird.jpeg', $book->getImage());

        $book->setImage(null);
        $this->assertNull($book->getImage());
    }

    /**
     * Test getting the ID (set by Doctrine).
     */
    public function testId(): void
    {
        $book = new Book();

        // Simulate Doctrine setting an ID (using reflection to set private property)
        $reflection = new \ReflectionClass($book);
        $property = $reflection->getProperty('id');
        $property->setAccessible(true);
        $property->setValue($book, 1);
        $this->assertSame(1, $book->getId());
    }
}
