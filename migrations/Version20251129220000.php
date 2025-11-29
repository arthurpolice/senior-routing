<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251129220000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Rename "order" table to "orders" to avoid reserved keyword conflicts.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE "order" RENAME TO orders');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE orders RENAME TO "order"');
    }
}

