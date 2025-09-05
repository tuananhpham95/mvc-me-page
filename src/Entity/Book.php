<?php

namespace App\Entity;

use App\Repository\BookRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BookRepository::class)]
class Book
{
    /**
     * The unique identifier for the book (auto-incremented by Doctrine).
     *
     * @phpstan-readonly
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    /**
     * The title of the book.
     */
    #[ORM\Column(type: 'string', length: 255)]
    private string $title = '';

    /**
     * The ISBN of the book (unique).
     */
    #[ORM\Column(type: 'string', length: 13, unique: true)]
    private string $isbn = '';

    /**
     * The author of the book.
     */
    #[ORM\Column(type: 'string', length: 255)]
    private string $author = '';

    /**
     * @var string|null The book image URL
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $image = null;

    /**
     * Gets the ID of the book.
     *
     * @return int The book ID
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Gets the title of the book.
     *
     * @return string The book title
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Sets the title of the book.
     *
     * @param string $title The book title
     */
    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Gets the ISBN of the book.
     *
     * @return string The book ISBN
     */
    public function getIsbn(): string
    {
        return $this->isbn;
    }

    /**
     * Sets the ISBN of the book.
     *
     * @param string $isbn The book ISBN
     */
    public function setIsbn(string $isbn): self
    {
        $this->isbn = $isbn;

        return $this;
    }

    /**
     * Gets the author of the book.
     *
     * @return string The book author
     */
    public function getAuthor(): string
    {
        return $this->author;
    }

    /**
     * Sets the author of the book.
     *
     * @param string $author The book author
     */
    public function setAuthor(string $author): self
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Gets the image URL of the book.
     *
     * @return string|null The book image URL or null
     */
    public function getImage(): ?string
    {
        return $this->image;
    }

    /**
     * Sets the image URL of the book.
     *
     * @param string|null $image The book image URL or null
     */
    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }
}
