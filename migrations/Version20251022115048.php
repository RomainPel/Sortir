<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251022115048 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE etat DROP no_etat');
        $this->addSql('ALTER TABLE lieu DROP no_lieu');
        $this->addSql('ALTER TABLE site DROP no_site');
        $this->addSql('ALTER TABLE sortie DROP no_sortie');
        $this->addSql('ALTER TABLE ville DROP no_ville');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sortie ADD no_sortie INT NOT NULL');
        $this->addSql('ALTER TABLE etat ADD no_etat INT NOT NULL');
        $this->addSql('ALTER TABLE ville ADD no_ville INT NOT NULL');
        $this->addSql('ALTER TABLE lieu ADD no_lieu INT NOT NULL');
        $this->addSql('ALTER TABLE site ADD no_site INT NOT NULL');
    }
}
