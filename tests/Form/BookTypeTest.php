<?php

namespace App\Tests\Form;

use App\Entity\Book;
use App\Form\BookType;
use Symfony\Component\Form\Test\TypeTestCase;

class BookTypeTest extends TypeTestCase
{
    public function testBuildForm(): void
    {
        $formData = [
            'title' => 'Test Book',
            'isbn' => '978-3-16-148410-0',
            'author' => 'Test Author',
            'image' => 'https://example.com/book.jpg',
        ];

        $book = new Book();

        $form = $this->factory->create(BookType::class, $book);

        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
        $this->assertEquals('Test Book', $book->getTitle());
        $this->assertEquals('978-3-16-148410-0', $book->getIsbn());
        $this->assertEquals('Test Author', $book->getAuthor());
        $this->assertEquals('https://example.com/book.jpg', $book->getImage());
    }
}
