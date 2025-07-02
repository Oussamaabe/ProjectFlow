<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250702171236 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE document_societe (id INT AUTO_INCREMENT NOT NULL, societe_id INT NOT NULL, nom VARCHAR(255) NOT NULL, fichier VARCHAR(255) NOT NULL, INDEX IDX_BD0A9B61FCF77503 (societe_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE societe (id INT AUTO_INCREMENT NOT NULL, raison_sociale VARCHAR(255) NOT NULL, capital DOUBLE PRECISION NOT NULL, rc VARCHAR(255) NOT NULL, tp VARCHAR(255) NOT NULL, if_fiscal VARCHAR(255) NOT NULL, cnss VARCHAR(255) NOT NULL, ice VARCHAR(255) NOT NULL, banque VARCHAR(255) NOT NULL, rib VARCHAR(255) NOT NULL, site_web VARCHAR(255) DEFAULT NULL, telephone VARCHAR(50) DEFAULT NULL, fax VARCHAR(50) DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE document_societe ADD CONSTRAINT FK_BD0A9B61FCF77503 FOREIGN KEY (societe_id) REFERENCES societe (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE document_societe DROP FOREIGN KEY FK_BD0A9B61FCF77503');
        $this->addSql('DROP TABLE document_societe');
        $this->addSql('DROP TABLE societe');
    }
}
