<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190613161457 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE polym_crea (id INT AUTO_INCREMENT NOT NULL, polym_plannif_id INT DEFAULT NULL, moyens VARCHAR(15) NOT NULL, num_polym VARCHAR(15) NOT NULL, date_exe DATETIME NOT NULL, date_valid DATETIME NOT NULL, of1 VARCHAR(15) DEFAULT NULL, of2 VARCHAR(15) DEFAULT NULL, of3 VARCHAR(15) DEFAULT NULL, of4 VARCHAR(15) DEFAULT NULL, of5 VARCHAR(15) DEFAULT NULL, of6 VARCHAR(15) DEFAULT NULL, of7 VARCHAR(15) DEFAULT NULL, of8 VARCHAR(15) DEFAULT NULL, of9 VARCHAR(15) DEFAULT NULL, of10 VARCHAR(15) DEFAULT NULL, of11 VARCHAR(15) DEFAULT NULL, of12 VARCHAR(15) DEFAULT NULL, of13 VARCHAR(15) DEFAULT NULL, of14 VARCHAR(15) DEFAULT NULL, of15 VARCHAR(15) DEFAULT NULL, of16 VARCHAR(15) DEFAULT NULL, of17 VARCHAR(15) DEFAULT NULL, of18 VARCHAR(15) DEFAULT NULL, of19 VARCHAR(15) DEFAULT NULL, of20 VARCHAR(15) DEFAULT NULL, of21 VARCHAR(15) DEFAULT NULL, of22 VARCHAR(15) DEFAULT NULL, of23 VARCHAR(15) DEFAULT NULL, of24 VARCHAR(15) DEFAULT NULL, of25 VARCHAR(15) DEFAULT NULL, commentaires VARCHAR(255) DEFAULT NULL, programme VARCHAR(10) NOT NULL, validation VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_3A5DEDF649DEFD00 (polym_plannif_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE polym_crea ADD CONSTRAINT FK_3A5DEDF649DEFD00 FOREIGN KEY (polym_plannif_id) REFERENCES planning (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE polym_crea');
    }
}
