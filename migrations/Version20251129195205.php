<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251129195205 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__orders AS SELECT id, date, order_size, created_at, client_id, service_provider_id FROM orders');
        $this->addSql('DROP TABLE orders');
        $this->addSql('CREATE TABLE orders (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, date DATETIME NOT NULL, order_size INTEGER NOT NULL, created_at DATETIME NOT NULL, client_id INTEGER NOT NULL, service_provider_id INTEGER NOT NULL, meal_type VARCHAR(255) NOT NULL, CONSTRAINT FK_F529939819EB6921 FOREIGN KEY (client_id) REFERENCES client (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_F5299398C6C98E06 FOREIGN KEY (service_provider_id) REFERENCES service_provider (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO orders (id, date, order_size, created_at, client_id, service_provider_id) SELECT id, date, order_size, created_at, client_id, service_provider_id FROM __temp__orders');
        $this->addSql('DROP TABLE __temp__orders');
        $this->addSql('CREATE INDEX IDX_E52FFDEE19EB6921 ON orders (client_id)');
        $this->addSql('CREATE INDEX IDX_E52FFDEEC6C98E06 ON orders (service_provider_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__orders AS SELECT id, date, order_size, created_at, client_id, service_provider_id FROM orders');
        $this->addSql('DROP TABLE orders');
        $this->addSql('CREATE TABLE orders (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, date DATETIME NOT NULL, order_size INTEGER NOT NULL, created_at DATETIME NOT NULL, client_id INTEGER NOT NULL, service_provider_id INTEGER NOT NULL, CONSTRAINT FK_E52FFDEE19EB6921 FOREIGN KEY (client_id) REFERENCES client (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_E52FFDEEC6C98E06 FOREIGN KEY (service_provider_id) REFERENCES service_provider (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO orders (id, date, order_size, created_at, client_id, service_provider_id) SELECT id, date, order_size, created_at, client_id, service_provider_id FROM __temp__orders');
        $this->addSql('DROP TABLE __temp__orders');
        $this->addSql('CREATE INDEX IDX_F5299398C6C98E06 ON orders (service_provider_id)');
        $this->addSql('CREATE INDEX IDX_F529939819EB6921 ON orders (client_id)');
    }
}
