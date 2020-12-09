<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201207232335 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX Conf ON charge');
        $this->addSql('ALTER TABLE charge ADD chargement_id INT DEFAULT NULL, CHANGE Designation_Pcs designation_pcs VARCHAR(24) NOT NULL, CHANGE Num_Prog num_prog VARCHAR(15) DEFAULT NULL, CHANGE Date_Creation date_creation DATETIME NOT NULL, CHANGE Date_Deb date_deb DATE DEFAULT NULL, CHANGE Date_Fin date_fin DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE charge ADD CONSTRAINT FK_556BA434B8FBE502 FOREIGN KEY (chargement_id) REFERENCES chargement (id)');
        $this->addSql('CREATE INDEX IDX_556BA434B8FBE502 ON charge (chargement_id)');
        $this->addSql('ALTER TABLE chargement ADD date_plannif DATE NOT NULL, ADD remplissage INT NOT NULL');
        $this->addSql('ALTER TABLE polym_real DROP INDEX FK_D14CD140A5AF1127, ADD UNIQUE INDEX UNIQ_D14CD140A5AF1127 (moyens_id)');
        $this->addSql('ALTER TABLE polym_real DROP INDEX FK_D14CD140A0A1C920, ADD UNIQUE INDEX UNIQ_D14CD140A0A1C920 (programmes_id)');
        $this->addSql('ALTER TABLE polym_real DROP INDEX FK_D14CD14049DEFD00, ADD UNIQUE INDEX UNIQ_D14CD14049DEFD00 (polym_plannif_id)');
        $this->addSql('ALTER TABLE recurrance_polym DROP INDEX type_recurrance, ADD UNIQUE INDEX UNIQ_3A8E16BFA8E3DFCE (type_recurrance_id)');
        $this->addSql('ALTER TABLE recurrance_polym ADD CONSTRAINT FK_3A8E16BF47631EF9 FOREIGN KEY (num_planning_id) REFERENCES planning (id)');
        $this->addSql('ALTER TABLE recurrance_polym ADD CONSTRAINT FK_3A8E16BFA8E3DFCE FOREIGN KEY (type_recurrance_id) REFERENCES type_recurrance (id)');
        $this->addSql('ALTER TABLE recurrance_polym RENAME INDEX num_planning TO UNIQ_3A8E16BF47631EF9');
        $this->addSql('ALTER TABLE type_recurrance CHANGE nbr_jour_cycle nbr_jour_cycle INT NOT NULL');
        $this->addSql('ALTER TABLE user CHANGE roles roles JSON NOT NULL COMMENT \'(DC2Type:json_array)\'');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE charge DROP FOREIGN KEY FK_556BA434B8FBE502');
        $this->addSql('DROP INDEX IDX_556BA434B8FBE502 ON charge');
        $this->addSql('ALTER TABLE charge DROP chargement_id, CHANGE designation_pcs Designation_Pcs VARCHAR(11) NOT NULL COLLATE latin1_swedish_ci, CHANGE num_prog Num_Prog VARCHAR(11) NOT NULL COLLATE latin1_swedish_ci, CHANGE date_deb Date_Deb DATE NOT NULL, CHANGE date_fin Date_Fin DATETIME NOT NULL, CHANGE date_creation Date_Creation DATETIME DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX Conf ON charge (Conf)');
        $this->addSql('ALTER TABLE chargement DROP date_plannif, DROP remplissage');
        $this->addSql('ALTER TABLE polym_real DROP INDEX UNIQ_D14CD14049DEFD00, ADD INDEX FK_D14CD14049DEFD00 (polym_plannif_id)');
        $this->addSql('ALTER TABLE polym_real DROP INDEX UNIQ_D14CD140A5AF1127, ADD INDEX FK_D14CD140A5AF1127 (moyens_id)');
        $this->addSql('ALTER TABLE polym_real DROP INDEX UNIQ_D14CD140A0A1C920, ADD INDEX FK_D14CD140A0A1C920 (programmes_id)');
        $this->addSql('ALTER TABLE recurrance_polym DROP INDEX UNIQ_3A8E16BFA8E3DFCE, ADD INDEX type_recurrance (type_recurrance_id)');
        $this->addSql('ALTER TABLE recurrance_polym DROP FOREIGN KEY FK_3A8E16BF47631EF9');
        $this->addSql('ALTER TABLE recurrance_polym DROP FOREIGN KEY FK_3A8E16BFA8E3DFCE');
        $this->addSql('ALTER TABLE recurrance_polym RENAME INDEX uniq_3a8e16bf47631ef9 TO num_planning');
        $this->addSql('ALTER TABLE type_recurrance CHANGE nbr_jour_cycle nbr_jour_cycle INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user CHANGE roles roles LONGTEXT NOT NULL COLLATE utf8mb4_unicode_ci COMMENT \'(DC2Type:array)\'');
    }
}
