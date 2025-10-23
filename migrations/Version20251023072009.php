<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251023072009 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sortie ADD date_debut DATETIME NOT NULL, ADD date_limite_inscription DATETIME NOT NULL, DROP datedebut, DROP datecloture, CHANGE nbinscriptionmax nb_inscription_max INT NOT NULL, CHANGE descriptioninfos description_infos VARCHAR(500) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sortie ADD datedebut DATETIME NOT NULL, ADD datecloture DATETIME NOT NULL, DROP date_debut, DROP date_limite_inscription, CHANGE nb_inscription_max nbinscriptionmax INT NOT NULL, CHANGE description_infos descriptioninfos VARCHAR(500) DEFAULT NULL');
    }
}
