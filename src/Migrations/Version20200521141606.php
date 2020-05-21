<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200521141606 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE court_reservation (id INT AUTO_INCREMENT NOT NULL, player_id INT DEFAULT NULL, court INT NOT NULL, players INT NOT NULL, start_time DATETIME NOT NULL, INDEX IDX_7D0C2F9399E6F5DF (player_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE court_reservation_user (court_reservation_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_2DCABA2582BEB7A2 (court_reservation_id), INDEX IDX_2DCABA25A76ED395 (user_id), PRIMARY KEY(court_reservation_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE page_visit (id INT AUTO_INCREMENT NOT NULL, current_url VARCHAR(255) NOT NULL, time TIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE training (id INT AUTO_INCREMENT NOT NULL, teacher_id INT DEFAULT NULL, start_time DATETIME NOT NULL, INDEX IDX_D5128A8F41807E1D (teacher_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(255) NOT NULL, username VARCHAR(255) NOT NULL, password VARCHAR(64) NOT NULL, roles JSON NOT NULL, profile_image VARCHAR(255) DEFAULT NULL, background_image VARCHAR(255) DEFAULT NULL, firstname VARCHAR(255) NOT NULL, lastname VARCHAR(255) NOT NULL, address VARCHAR(255) DEFAULT NULL, mobile VARCHAR(255) DEFAULT NULL, description LONGTEXT DEFAULT NULL, company_role VARCHAR(255) DEFAULT NULL, activate_user TINYINT(1) NOT NULL, payed TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), UNIQUE INDEX UNIQ_2DA17977F85E0677 (username), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE court_reservation ADD CONSTRAINT FK_7D0C2F9399E6F5DF FOREIGN KEY (player_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE court_reservation_user ADD CONSTRAINT FK_2DCABA2582BEB7A2 FOREIGN KEY (court_reservation_id) REFERENCES court_reservation (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE court_reservation_user ADD CONSTRAINT FK_2DCABA25A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE training ADD CONSTRAINT FK_D5128A8F41807E1D FOREIGN KEY (teacher_id) REFERENCES user (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE court_reservation_user DROP FOREIGN KEY FK_2DCABA2582BEB7A2');
        $this->addSql('ALTER TABLE court_reservation DROP FOREIGN KEY FK_7D0C2F9399E6F5DF');
        $this->addSql('ALTER TABLE court_reservation_user DROP FOREIGN KEY FK_2DCABA25A76ED395');
        $this->addSql('ALTER TABLE training DROP FOREIGN KEY FK_D5128A8F41807E1D');
        $this->addSql('DROP TABLE court_reservation');
        $this->addSql('DROP TABLE court_reservation_user');
        $this->addSql('DROP TABLE page_visit');
        $this->addSql('DROP TABLE training');
        $this->addSql('DROP TABLE user');
    }
}
