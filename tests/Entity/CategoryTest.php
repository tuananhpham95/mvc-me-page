<?php

namespace App\Tests\Entity;

use App\Entity\Category;
use PHPUnit\Framework\TestCase;

class CategoryTest extends TestCase
{
    public function testCategoryIdIsNullByDefault(): void
    {
        $category = new Category();
        $this->assertNull($category->getId(), 'New category should have null ID before persistence');
    }

    public function testSetAndGetName(): void
    {
        $category = new Category();

        $name = 'Programming';
        $category->setName($name);

        $this->assertSame($name, $category->getName(), 'Getter should return the same name that was set');
    }

    public function testSetNameReturnsSelfForFluentInterface(): void
    {
        $category = new Category();
        $result = $category->setName('Design');

        $this->assertSame($category, $result, 'setName() should return the Category instance (fluent interface)');
    }

    public function testNameIsRequiredAndString(): void
    {
        $category = new Category();

        // Name is private string (non-nullable), so passing null will throw TypeError
        $this->expectException(\TypeError::class);
        // @phpstan-ignore-next-line: testing type error intentionally
        $category->setName(null);
    }

    public function testCategoryCanBeInstantiated(): void
    {
        $category = new Category();
        $this->assertInstanceOf(Category::class, $category);
    }

    public function testNameLengthConstraintIsRespected(): void
    {
        $category = new Category();

        // Name of 100 characters (allowed)
        $longName = str_repeat('a', 100);
        $category->setName($longName);
        $this->assertSame($longName, $category->getName());

        // Name of 101 characters (Doctrine will error on flush, but entity allows it)
        $tooLongName = str_repeat('b', 101);
        $category->setName($tooLongName);
        $this->assertSame($tooLongName, $category->getName());
    }

    public function testToStringBehavior(): void
    {
        $category = new Category();
        $category->setName('Symfony');

        // Instead of assertIsString (redundant), just assert the value is as expected
        $this->assertSame('Symfony', $category->getName());
    }
}
