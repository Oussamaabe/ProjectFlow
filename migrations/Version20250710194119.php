<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250710194119 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ressource DROP FOREIGN KEY FK_939F4544C18272');
        $this->addSql('DROP TABLE ressource');
        $this->addSql('ALTER TABLE projet DROP date_fin_reelle, DROP budget_initial, DROP budget_consomme');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE ressource (id INT AUTO_INCREMENT NOT NULL, projet_id INT NOT NULL, type VARCHAR(20) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, nom VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, quantite DOUBLE PRECISION DEFAULT NULL, heures DOUBLE PRECISION DEFAULT NULL, unite VARCHAR(50) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, cout_unitaire DOUBLE PRECISION NOT NULL, role VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, INDEX IDX_939F4544C18272 (projet_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE ressource ADD CONSTRAINT FK_939F4544C18272 FOREIGN KEY (projet_id) REFERENCES projet (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE projet ADD date_fin_reelle DATE DEFAULT NULL, ADD budget_initial DOUBLE PRECISION DEFAULT NULL, ADD budget_consomme DOUBLE PRECISION DEFAULT NULL');
    }
}
