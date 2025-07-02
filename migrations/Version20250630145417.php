<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250630145417 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE affaire (id INT AUTO_INCREMENT NOT NULL, projet_id INT DEFAULT NULL, titre VARCHAR(255) NOT NULL, type VARCHAR(20) NOT NULL, status VARCHAR(20) NOT NULL, date_debut DATE NOT NULL, date_fin DATE DEFAULT NULL, client VARCHAR(255) NOT NULL, montant DOUBLE PRECISION DEFAULT NULL, description LONGTEXT DEFAULT NULL, resultat LONGTEXT DEFAULT NULL, UNIQUE INDEX UNIQ_9C3F18EFC18272 (projet_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE projet (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, date_debut DATE NOT NULL, date_fin_prevue DATE DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE affaire ADD CONSTRAINT FK_9C3F18EFC18272 FOREIGN KEY (projet_id) REFERENCES projet (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE affaire DROP FOREIGN KEY FK_9C3F18EFC18272');
        $this->addSql('DROP TABLE affaire');
        $this->addSql('DROP TABLE projet');
    }
}
