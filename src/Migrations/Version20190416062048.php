<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190416062048 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE polym_real (id INT AUTO_INCREMENT NOT NULL, polym_plannif_id INT DEFAULT NULL, nom_polym VARCHAR(15) NOT NULL, deb_polym DATETIME NOT NULL, statut VARCHAR(15) NOT NULL, fin_polym DATETIME DEFAULT NULL, articles LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', UNIQUE INDEX UNIQ_D14CD14049DEFD00 (polym_plannif_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE polym_real ADD CONSTRAINT FK_D14CD14049DEFD00 FOREIGN KEY (polym_plannif_id) REFERENCES planning (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE polym_real');
    }
}
