<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220818093415 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE charge ADD fiche_instruc VARCHAR(255) DEFAULT NULL, ADD outillage VARCHAR(255) DEFAULT NULL, ADD equipement VARCHAR(255) DEFAULT NULL');  
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE charge DROP fiche_instruc, DROP outillage, DROP equipement');
    }
}
