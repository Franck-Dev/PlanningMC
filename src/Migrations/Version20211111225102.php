<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211111225102 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE caract_moyens (id INT AUTO_INCREMENT NOT NULL, id_cat_moyen INT NOT NULL, description VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cat_moyens (id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, nb_moyens INT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE category_moyens (id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE chargement (id INT AUTO_INCREMENT NOT NULL, nom_chargement VARCHAR(255) NOT NULL, id_planning INT NOT NULL, date_plannif DATE NOT NULL, remplissage INT NOT NULL, programme VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE conf_smenu (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, id_smenu1 VARCHAR(255) DEFAULT NULL, id_smenu2 VARCHAR(255) DEFAULT NULL, id_smenu3 VARCHAR(255) DEFAULT NULL, id_smenu4 VARCHAR(255) DEFAULT NULL, id_smenu5 VARCHAR(255) DEFAULT NULL, id_smenu6 VARCHAR(255) DEFAULT NULL, id_smenu7 VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE conf_ssmenu (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, id_smenu1 VARCHAR(255) DEFAULT NULL, id_smenu2 VARCHAR(255) DEFAULT NULL, id_smenu3 VARCHAR(255) DEFAULT NULL, id_smenu4 VARCHAR(255) DEFAULT NULL, id_smenu5 VARCHAR(255) DEFAULT NULL, id_smenu6 VARCHAR(255) DEFAULT NULL, id_smenu7 VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE confapply (id INT AUTO_INCREMENT NOT NULL, titre_menu VARCHAR(255) NOT NULL, titre_smenu VARCHAR(255) NOT NULL, titre_ssmenu VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE const_menu (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE moyens (id INT AUTO_INCREMENT NOT NULL, category_moyens_id INT NOT NULL, libelle VARCHAR(255) NOT NULL, id_service INT NOT NULL, description VARCHAR(255) DEFAULT NULL, id_types_caracteristiques INT NOT NULL, date_maintenance DATE DEFAULT NULL, operationnel TINYINT(1) NOT NULL, activitees VARCHAR(20) DEFAULT NULL, INDEX IDX_7493DDC962CFC317 (category_moyens_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE orga_eq (id INT AUTO_INCREMENT NOT NULL, nom_equipe_id INT DEFAULT NULL, type_w VARCHAR(255) DEFAULT NULL, date_debut DATETIME NOT NULL, date_fin DATETIME NOT NULL, UNIQUE INDEX UNIQ_5698C346F9776AC8 (nom_equipe_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE planning (id INT AUTO_INCREMENT NOT NULL, num_demande_id INT DEFAULT NULL, identification VARCHAR(255) NOT NULL, action VARCHAR(255) NOT NULL, debut_date DATETIME NOT NULL, fin_date DATETIME NOT NULL, statut VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_D499BFF6FFE1E93 (num_demande_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE prog_polym (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, tps_theo VARCHAR(255) NOT NULL, duree DATETIME DEFAULT NULL, tps_charg DATETIME DEFAULT NULL, tps_decharg DATETIME DEFAULT NULL, id_moyen INT NOT NULL, thermocouples TINYINT(1) NOT NULL, spc TINYINT(1) DEFAULT NULL, date_creation DATETIME DEFAULT NULL, date_modif DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE services (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, id_chef INT NOT NULL, nb_pers INT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE moyens ADD CONSTRAINT FK_7493DDC962CFC317 FOREIGN KEY (category_moyens_id) REFERENCES category_moyens (id)');
        $this->addSql('ALTER TABLE orga_eq ADD CONSTRAINT FK_5698C346F9776AC8 FOREIGN KEY (nom_equipe_id) REFERENCES nom_equipe (id)');
        $this->addSql('ALTER TABLE planning ADD CONSTRAINT FK_D499BFF6FFE1E93 FOREIGN KEY (num_demande_id) REFERENCES demandes (id)');
        $this->addSql('ALTER TABLE charg_fige ADD CONSTRAINT FK_B8CAC2A16C346E29 FOREIGN KEY (moyen_id) REFERENCES moyens (id)');
        $this->addSql('ALTER TABLE charg_fige ADD CONSTRAINT FK_B8CAC2A162BB7AEE FOREIGN KEY (programme_id) REFERENCES prog_moyens (id)');
        $this->addSql('ALTER TABLE charg_fige_outillages ADD CONSTRAINT FK_741E8F2F3C866CBD FOREIGN KEY (charg_fige_id) REFERENCES charg_fige (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE charg_fige_outillages ADD CONSTRAINT FK_741E8F2F4D6CE55C FOREIGN KEY (outillages_id) REFERENCES outillages (id)');
        $this->addSql('ALTER TABLE charge ADD CONSTRAINT FK_556BA434B8FBE502 FOREIGN KEY (chargement_id) REFERENCES chargement (id)');
        $this->addSql('ALTER TABLE charge ADD CONSTRAINT FK_556BA4346F340C96 FOREIGN KEY (polym_id) REFERENCES polym_real (id)');
        $this->addSql('ALTER TABLE demande ADD CONSTRAINT FK_2694D7A53FEFE003 FOREIGN KEY (id_polym_id) REFERENCES prog_moyens (id)');
        $this->addSql('ALTER TABLE demandes ADD CONSTRAINT FK_BD940CBB5EC1162 FOREIGN KEY (cycle_id) REFERENCES prog_moyens (id)');
        $this->addSql('ALTER TABLE demandes ADD CONSTRAINT FK_BD940CBB8D2A2FD3 FOREIGN KEY (moyen_utilise_id) REFERENCES moyens (id)');
        $this->addSql('ALTER TABLE nom_equipe ADD CONSTRAINT FK_A4862542D3B4BEBF FOREIGN KEY (orga_w_id) REFERENCES types_equipe (id)');
        $this->addSql('ALTER TABLE nom_equipe ADD CONSTRAINT FK_A4862542783E3463 FOREIGN KEY (manager_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE outillages_prog_moyens ADD CONSTRAINT FK_F0BCBBE84D6CE55C FOREIGN KEY (outillages_id) REFERENCES outillages (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE outillages_prog_moyens ADD CONSTRAINT FK_F0BCBBE8C9ECABC3 FOREIGN KEY (prog_moyens_id) REFERENCES prog_moyens (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE polym_crea ADD CONSTRAINT FK_3A5DEDF649DEFD00 FOREIGN KEY (polym_plannif_id) REFERENCES planning (id)');
        $this->addSql('ALTER TABLE polym_real ADD CONSTRAINT FK_D14CD14049DEFD00 FOREIGN KEY (polym_plannif_id) REFERENCES planning (id)');
        $this->addSql('ALTER TABLE polym_real ADD CONSTRAINT FK_D14CD140A5AF1127 FOREIGN KEY (moyens_id) REFERENCES moyens (id)');
        $this->addSql('ALTER TABLE polym_real ADD CONSTRAINT FK_D14CD140A0A1C920 FOREIGN KEY (programmes_id) REFERENCES prog_moyens (id)');
        $this->addSql('ALTER TABLE prog_moyens ADD CONSTRAINT FK_2E21B459D27201D7 FOREIGN KEY (cate_moyen_id) REFERENCES category_moyens (id)');
        $this->addSql('ALTER TABLE recurrance_polym ADD CONSTRAINT FK_3A8E16BF47631EF9 FOREIGN KEY (num_planning_id) REFERENCES planning (id)');
        $this->addSql('ALTER TABLE recurrance_polym ADD CONSTRAINT FK_3A8E16BFA8E3DFCE FOREIGN KEY (type_recurrance_id) REFERENCES type_recurrance (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649ED5CA9E6 FOREIGN KEY (service_id) REFERENCES services (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649A0905086 FOREIGN KEY (poste_id) REFERENCES postes (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649F9776AC8 FOREIGN KEY (nom_equipe_id) REFERENCES nom_equipe (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE moyens DROP FOREIGN KEY FK_7493DDC962CFC317');
        $this->addSql('ALTER TABLE prog_moyens DROP FOREIGN KEY FK_2E21B459D27201D7');
        $this->addSql('ALTER TABLE charge DROP FOREIGN KEY FK_556BA434B8FBE502');
        $this->addSql('ALTER TABLE charg_fige DROP FOREIGN KEY FK_B8CAC2A16C346E29');
        $this->addSql('ALTER TABLE demandes DROP FOREIGN KEY FK_BD940CBB8D2A2FD3');
        $this->addSql('ALTER TABLE polym_real DROP FOREIGN KEY FK_D14CD140A5AF1127');
        $this->addSql('ALTER TABLE polym_crea DROP FOREIGN KEY FK_3A5DEDF649DEFD00');
        $this->addSql('ALTER TABLE polym_real DROP FOREIGN KEY FK_D14CD14049DEFD00');
        $this->addSql('ALTER TABLE recurrance_polym DROP FOREIGN KEY FK_3A8E16BF47631EF9');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649ED5CA9E6');
        $this->addSql('DROP TABLE caract_moyens');
        $this->addSql('DROP TABLE cat_moyens');
        $this->addSql('DROP TABLE category_moyens');
        $this->addSql('DROP TABLE chargement');
        $this->addSql('DROP TABLE conf_smenu');
        $this->addSql('DROP TABLE conf_ssmenu');
        $this->addSql('DROP TABLE confapply');
        $this->addSql('DROP TABLE const_menu');
        $this->addSql('DROP TABLE moyens');
        $this->addSql('DROP TABLE orga_eq');
        $this->addSql('DROP TABLE planning');
        $this->addSql('DROP TABLE prog_polym');
        $this->addSql('DROP TABLE services');
        $this->addSql('ALTER TABLE charg_fige DROP FOREIGN KEY FK_B8CAC2A162BB7AEE');
        $this->addSql('ALTER TABLE charg_fige_outillages DROP FOREIGN KEY FK_741E8F2F3C866CBD');
        $this->addSql('ALTER TABLE charg_fige_outillages DROP FOREIGN KEY FK_741E8F2F4D6CE55C');
        $this->addSql('ALTER TABLE charge DROP FOREIGN KEY FK_556BA4346F340C96');
        $this->addSql('ALTER TABLE demande DROP FOREIGN KEY FK_2694D7A53FEFE003');
        $this->addSql('ALTER TABLE demandes DROP FOREIGN KEY FK_BD940CBB5EC1162');
        $this->addSql('ALTER TABLE nom_equipe DROP FOREIGN KEY FK_A4862542D3B4BEBF');
        $this->addSql('ALTER TABLE nom_equipe DROP FOREIGN KEY FK_A4862542783E3463');
        $this->addSql('ALTER TABLE outillages_prog_moyens DROP FOREIGN KEY FK_F0BCBBE84D6CE55C');
        $this->addSql('ALTER TABLE outillages_prog_moyens DROP FOREIGN KEY FK_F0BCBBE8C9ECABC3');
        $this->addSql('ALTER TABLE polym_real DROP FOREIGN KEY FK_D14CD140A0A1C920');
        $this->addSql('ALTER TABLE recurrance_polym DROP FOREIGN KEY FK_3A8E16BFA8E3DFCE');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649A0905086');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649F9776AC8');
    }
}
