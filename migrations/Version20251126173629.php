<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251126173629 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE service_provider ADD COLUMN city VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE service_provider ADD COLUMN country VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE service_provider ADD COLUMN address VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__service_provider AS SELECT id, name FROM service_provider');
        $this->addSql('DROP TABLE service_provider');
        $this->addSql('CREATE TABLE service_provider (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL)');
        $this->addSql('INSERT INTO service_provider (id, name) SELECT id, name FROM __temp__service_provider');
        $this->addSql('DROP TABLE __temp__service_provider');
    }
}
