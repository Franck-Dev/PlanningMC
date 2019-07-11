<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190708135213 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE prog_moyens (id INT AUTO_INCREMENT NOT NULL, cate_moyen_id INT NOT NULL, nom VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, tps_theo INT NOT NULL, duree DATETIME NOT NULL, tps_chargement DATETIME NOT NULL, tps_dechargement DATETIME NOT NULL, thermocouples TINYINT(1) NOT NULL, spc TINYINT(1) DEFAULT NULL, date_creation DATETIME NOT NULL, date_modif DATETIME DEFAULT NULL, couleur VARCHAR(255) NOT NULL, INDEX IDX_2E21B459D27201D7 (cate_moyen_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE prog_moyens ADD CONSTRAINT FK_2E21B459D27201D7 FOREIGN KEY (cate_moyen_id) REFERENCES category_moyens (id)');
        $this->addSql('DROP INDEX UNIQ_D14CD14049DEFD00 ON polym_real');
        $this->addSql('DROP INDEX UNIQ_D14CD140A5AF1127 ON polym_real');
        $this->addSql('DROP INDEX UNIQ_D14CD140A0A1C920 ON polym_real');
        $this->addSql('ALTER TABLE polym_real ADD CONSTRAINT FK_D14CD14049DEFD00 FOREIGN KEY (polym_plannif_id) REFERENCES planning (id)');
        $this->addSql('ALTER TABLE polym_real ADD CONSTRAINT FK_D14CD140A5AF1127 FOREIGN KEY (moyens_id) REFERENCES moyens (id)');
        $this->addSql('ALTER TABLE polym_real ADD CONSTRAINT FK_D14CD140A0A1C920 FOREIGN KEY (programmes_id) REFERENCES prog_moyens (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D14CD14049DEFD00 ON polym_real (polym_plannif_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D14CD140A5AF1127 ON polym_real (moyens_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D14CD140A0A1C920 ON polym_real (programmes_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE demande DROP FOREIGN KEY FK_2694D7A53FEFE003');
        $this->addSql('ALTER TABLE demandes DROP FOREIGN KEY FK_BD940CBB5EC1162');
        $this->addSql('ALTER TABLE polym_real DROP FOREIGN KEY FK_D14CD140A0A1C920');
        $this->addSql('DROP TABLE prog_moyens');
        $this->addSql('ALTER TABLE polym_real DROP FOREIGN KEY FK_D14CD14049DEFD00');
        $this->addSql('ALTER TABLE polym_real DROP FOREIGN KEY FK_D14CD140A5AF1127');
        $this->addSql('DROP INDEX UNIQ_D14CD14049DEFD00 ON polym_real');
        $this->addSql('DROP INDEX UNIQ_D14CD140A5AF1127 ON polym_real');
        $this->addSql('DROP INDEX UNIQ_D14CD140A0A1C920 ON polym_real');
        $this->addSql('CREATE INDEX UNIQ_D14CD14049DEFD00 ON polym_real (polym_plannif_id)');
        $this->addSql('CREATE INDEX UNIQ_D14CD140A5AF1127 ON polym_real (moyens_id)');
        $this->addSql('CREATE INDEX UNIQ_D14CD140A0A1C920 ON polym_real (programmes_id)');
    }
}
