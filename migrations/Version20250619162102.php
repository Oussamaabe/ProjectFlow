<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Makes email column nullable
 */
final class Version20250619162102 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Makes email column nullable in user table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE user MODIFY email VARCHAR(180) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE user MODIFY email VARCHAR(180) NOT NULL');
    }
}