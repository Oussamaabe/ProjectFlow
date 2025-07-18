<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250716170953 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE articles (id INT AUTO_INCREMENT NOT NULL, fournisseur_id INT NOT NULL, reference VARCHAR(50) NOT NULL, designation VARCHAR(255) NOT NULL, prix_unitaire NUMERIC(10, 2) NOT NULL, unite_mesure VARCHAR(20) DEFAULT NULL, description LONGTEXT DEFAULT NULL, categorie VARCHAR(50) DEFAULT NULL, tva DOUBLE PRECISION DEFAULT NULL, actif TINYINT(1) DEFAULT 1 NOT NULL, date_creation DATETIME NOT NULL, date_modification DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_BFDD3168AEA34913 (reference), INDEX IDX_BFDD3168670C757F (fournisseur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE commande_achat (id INT AUTO_INCREMENT NOT NULL, fournisseur_id INT NOT NULL, numero VARCHAR(100) NOT NULL, date_commande DATETIME NOT NULL, statut VARCHAR(50) NOT NULL, total NUMERIC(10, 2) NOT NULL, INDEX IDX_1FC15B95670C757F (fournisseur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE fournisseur (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, email VARCHAR(100) NOT NULL, telephone VARCHAR(20) NOT NULL, adresse LONGTEXT NOT NULL, pays VARCHAR(100) NOT NULL, identifiant_fiscal VARCHAR(100) DEFAULT NULL, date_creation DATETIME NOT NULL, actif TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ligne_commande_achat (id INT AUTO_INCREMENT NOT NULL, commande_id INT NOT NULL, article_id INT NOT NULL, quantite INT NOT NULL, prix_unitaire NUMERIC(10, 2) NOT NULL, total NUMERIC(10, 2) NOT NULL, INDEX IDX_257F695A82EA2E54 (commande_id), INDEX IDX_257F695A7294869C (article_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE stock (id INT AUTO_INCREMENT NOT NULL, article_id INT NOT NULL, quantite INT NOT NULL, seuil_alerte INT DEFAULT NULL, updated_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_4B3656607294869C (article_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE articles ADD CONSTRAINT FK_BFDD3168670C757F FOREIGN KEY (fournisseur_id) REFERENCES fournisseur (id)');
        $this->addSql('ALTER TABLE commande_achat ADD CONSTRAINT FK_1FC15B95670C757F FOREIGN KEY (fournisseur_id) REFERENCES fournisseur (id)');
        $this->addSql('ALTER TABLE ligne_commande_achat ADD CONSTRAINT FK_257F695A82EA2E54 FOREIGN KEY (commande_id) REFERENCES commande_achat (id)');
        $this->addSql('ALTER TABLE ligne_commande_achat ADD CONSTRAINT FK_257F695A7294869C FOREIGN KEY (article_id) REFERENCES articles (id)');
        $this->addSql('ALTER TABLE stock ADD CONSTRAINT FK_4B3656607294869C FOREIGN KEY (article_id) REFERENCES articles (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE articles DROP FOREIGN KEY FK_BFDD3168670C757F');
        $this->addSql('ALTER TABLE commande_achat DROP FOREIGN KEY FK_1FC15B95670C757F');
        $this->addSql('ALTER TABLE ligne_commande_achat DROP FOREIGN KEY FK_257F695A82EA2E54');
        $this->addSql('ALTER TABLE ligne_commande_achat DROP FOREIGN KEY FK_257F695A7294869C');
        $this->addSql('ALTER TABLE stock DROP FOREIGN KEY FK_4B3656607294869C');
        $this->addSql('DROP TABLE articles');
        $this->addSql('DROP TABLE commande_achat');
        $this->addSql('DROP TABLE fournisseur');
        $this->addSql('DROP TABLE ligne_commande_achat');
        $this->addSql('DROP TABLE stock');
    }
}
