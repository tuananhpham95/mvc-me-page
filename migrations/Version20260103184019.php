<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260103184019 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__learning_items AS SELECT id, title, url, status, created_at FROM learning_items');
        $this->addSql('DROP TABLE learning_items');
        $this->addSql('CREATE TABLE learning_items (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, title VARCHAR(255) NOT NULL, url VARCHAR(255) DEFAULT NULL, status VARCHAR(20) NOT NULL, created_at DATETIME NOT NULL, description CLOB DEFAULT NULL)');
        $this->addSql('INSERT INTO learning_items (id, title, url, status, created_at) SELECT id, title, url, status, created_at FROM __temp__learning_items');
        $this->addSql('DROP TABLE __temp__learning_items');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__learning_items AS SELECT id, title, url, status, created_at FROM learning_items');
        $this->addSql('DROP TABLE learning_items');
        $this->addSql('CREATE TABLE learning_items (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, title VARCHAR(255) NOT NULL, url VARCHAR(255) DEFAULT NULL, status VARCHAR(20) NOT NULL, created_at DATETIME NOT NULL, content CLOB NOT NULL)');
        $this->addSql('INSERT INTO learning_items (id, title, url, status, created_at) SELECT id, title, url, status, created_at FROM __temp__learning_items');
        $this->addSql('DROP TABLE __temp__learning_items');
    }
}
