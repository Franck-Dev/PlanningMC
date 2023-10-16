<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231016153004 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE datas_moulage (id INT AUTO_INCREMENT NOT NULL, eq_out_id INT DEFAULT NULL, prog_laser_id INT DEFAULT NULL, prog_polym_id INT DEFAULT NULL, programme_id INT DEFAULT NULL, art_gamm VARCHAR(50) NOT NULL, descriptif_phase VARCHAR(255) DEFAULT NULL, cycle_moulage VARCHAR(255) NOT NULL COMMENT \'(DC2Type:dateinterval)\', active TINYINT(1) NOT NULL, deb_val DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', zone_trav VARCHAR(50) DEFAULT NULL, INDEX IDX_4A4FF866F05721CC (eq_out_id), INDEX IDX_4A4FF866EDAD7FF (prog_laser_id), INDEX IDX_4A4FF8662A1B05C2 (prog_polym_id), INDEX IDX_4A4FF86662BB7AEE (programme_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE eq_ot (id INT AUTO_INCREMENT NOT NULL, art_out_id INT DEFAULT NULL, ref INT NOT NULL, descriptif VARCHAR(255) DEFAULT NULL, polymss_trait SMALLINT DEFAULT NULL, statut VARCHAR(12) NOT NULL, date_deb_hs DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', date_fin_hs DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', created_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_969E63194BB01386 (art_out_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE eq_ot_prog_moyens (eq_ot_id INT NOT NULL, prog_moyens_id INT NOT NULL, INDEX IDX_4EF6A21D2CD1DB7E (eq_ot_id), INDEX IDX_4EF6A21DC9ECABC3 (prog_moyens_id), PRIMARY KEY(eq_ot_id, prog_moyens_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE datas_moulage ADD CONSTRAINT FK_4A4FF866F05721CC FOREIGN KEY (eq_out_id) REFERENCES eq_ot (id)');
        $this->addSql('ALTER TABLE datas_moulage ADD CONSTRAINT FK_4A4FF866EDAD7FF FOREIGN KEY (prog_laser_id) REFERENCES prog_moyens (id)');
        $this->addSql('ALTER TABLE datas_moulage ADD CONSTRAINT FK_4A4FF8662A1B05C2 FOREIGN KEY (prog_polym_id) REFERENCES prog_moyens (id)');
        $this->addSql('ALTER TABLE datas_moulage ADD CONSTRAINT FK_4A4FF86662BB7AEE FOREIGN KEY (programme_id) REFERENCES prog_avions (id)');
        $this->addSql('ALTER TABLE eq_ot ADD CONSTRAINT FK_969E63194BB01386 FOREIGN KEY (art_out_id) REFERENCES outillages (id)');
        $this->addSql('ALTER TABLE eq_ot_prog_moyens ADD CONSTRAINT FK_4EF6A21D2CD1DB7E FOREIGN KEY (eq_ot_id) REFERENCES eq_ot (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE eq_ot_prog_moyens ADD CONSTRAINT FK_4EF6A21DC9ECABC3 FOREIGN KEY (prog_moyens_id) REFERENCES prog_moyens (id) ON DELETE CASCADE');
        }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE datas_moulage DROP FOREIGN KEY FK_4A4FF866F05721CC');
        $this->addSql('ALTER TABLE datas_moulage DROP FOREIGN KEY FK_4A4FF866EDAD7FF');
        $this->addSql('ALTER TABLE datas_moulage DROP FOREIGN KEY FK_4A4FF8662A1B05C2');
        $this->addSql('ALTER TABLE datas_moulage DROP FOREIGN KEY FK_4A4FF86662BB7AEE');
        $this->addSql('ALTER TABLE eq_ot DROP FOREIGN KEY FK_969E63194BB01386');
        $this->addSql('ALTER TABLE eq_ot_prog_moyens DROP FOREIGN KEY FK_4EF6A21D2CD1DB7E');
        $this->addSql('ALTER TABLE eq_ot_prog_moyens DROP FOREIGN KEY FK_4EF6A21DC9ECABC3');
        $this->addSql('DROP TABLE datas_moulage');
        $this->addSql('DROP TABLE eq_ot');
        $this->addSql('DROP TABLE eq_ot_prog_moyens');
        }
}
