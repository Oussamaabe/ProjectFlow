<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250712025050 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user_groups (user_id INT NOT NULL, group_id INT NOT NULL, INDEX IDX_953F224DA76ED395 (user_id), INDEX IDX_953F224DFE54D947 (group_id), PRIMARY KEY(user_id, group_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_groups ADD CONSTRAINT FK_953F224DA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_groups ADD CONSTRAINT FK_953F224DFE54D947 FOREIGN KEY (group_id) REFERENCES app_group (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_groups DROP FOREIGN KEY FK_953F224DA76ED395');
        $this->addSql('ALTER TABLE user_groups DROP FOREIGN KEY FK_953F224DFE54D947');
        $this->addSql('DROP TABLE user_groups');
    }
}
