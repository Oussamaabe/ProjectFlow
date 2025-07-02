<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250630181610 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE affaire DROP FOREIGN KEY FK_9C3F18EFC18272');
        $this->addSql('DROP INDEX UNIQ_9C3F18EFC18272 ON affaire');
        $this->addSql('ALTER TABLE affaire DROP projet_id');
        $this->addSql('ALTER TABLE projet ADD affaire_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE projet ADD CONSTRAINT FK_50159CA9F082E755 FOREIGN KEY (affaire_id) REFERENCES affaire (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_50159CA9F082E755 ON projet (affaire_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE projet DROP FOREIGN KEY FK_50159CA9F082E755');
        $this->addSql('DROP INDEX UNIQ_50159CA9F082E755 ON projet');
        $this->addSql('ALTER TABLE projet DROP affaire_id');
        $this->addSql('ALTER TABLE affaire ADD projet_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE affaire ADD CONSTRAINT FK_9C3F18EFC18272 FOREIGN KEY (projet_id) REFERENCES projet (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_9C3F18EFC18272 ON affaire (projet_id)');
    }
}
