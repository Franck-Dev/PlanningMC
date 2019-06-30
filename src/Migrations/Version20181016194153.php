<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181016194153 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE prog_polym CHANGE duree duree VARCHAR(255) NOT NULL COMMENT \'(DC2Type:dateinterval)\', CHANGE tps_charg tps_charg VARCHAR(255) DEFAULT NULL COMMENT \'(DC2Type:dateinterval)\', CHANGE tps_decharg tps_decharg VARCHAR(255) DEFAULT NULL COMMENT \'(DC2Type:dateinterval)\'');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE prog_polym CHANGE duree duree TIME NOT NULL, CHANGE tps_charg tps_charg TIME DEFAULT NULL, CHANGE tps_decharg tps_decharg TIME DEFAULT NULL');
    }
}
