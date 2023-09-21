<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230630115953 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE demandes_charg_fige (demandes_id INT NOT NULL, charg_fige_id INT NOT NULL, INDEX IDX_679DFBD4F49DCC2D (demandes_id), INDEX IDX_679DFBD43C866CBD (charg_fige_id), PRIMARY KEY(demandes_id, charg_fige_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE demandes_charg_fige ADD CONSTRAINT FK_679DFBD4F49DCC2D FOREIGN KEY (demandes_id) REFERENCES demandes (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE demandes_charg_fige ADD CONSTRAINT FK_679DFBD43C866CBD FOREIGN KEY (charg_fige_id) REFERENCES charg_fige (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE demandes_charg_fige DROP FOREIGN KEY FK_679DFBD4F49DCC2D');
        $this->addSql('ALTER TABLE demandes_charg_fige DROP FOREIGN KEY FK_679DFBD43C866CBD');
        $this->addSql('DROP TABLE demandes_charg_fige');
    }
}
