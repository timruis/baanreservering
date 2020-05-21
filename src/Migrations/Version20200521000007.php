<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200521000007 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE track_reservation_user DROP FOREIGN KEY FK_B08651C57A6491A5');
        $this->addSql('CREATE TABLE court_reservation (id INT AUTO_INCREMENT NOT NULL, player_id INT DEFAULT NULL, court INT NOT NULL, players INT NOT NULL, start_time DATETIME NOT NULL, play_time_amount INT NOT NULL, INDEX IDX_7D0C2F9399E6F5DF (player_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE court_reservation_user (court_reservation_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_2DCABA2582BEB7A2 (court_reservation_id), INDEX IDX_2DCABA25A76ED395 (user_id), PRIMARY KEY(court_reservation_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE court_reservation ADD CONSTRAINT FK_7D0C2F9399E6F5DF FOREIGN KEY (player_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE court_reservation_user ADD CONSTRAINT FK_2DCABA2582BEB7A2 FOREIGN KEY (court_reservation_id) REFERENCES court_reservation (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE court_reservation_user ADD CONSTRAINT FK_2DCABA25A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE track_reservation');
        $this->addSql('DROP TABLE track_reservation_user');
        $this->addSql('ALTER TABLE training CHANGE teacher_id teacher_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user CHANGE roles roles JSON NOT NULL, CHANGE profile_image profile_image VARCHAR(255) DEFAULT NULL, CHANGE background_image background_image VARCHAR(255) DEFAULT NULL, CHANGE address address VARCHAR(255) DEFAULT NULL, CHANGE mobile mobile VARCHAR(255) DEFAULT NULL, CHANGE company_role company_role VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE court_reservation_user DROP FOREIGN KEY FK_2DCABA2582BEB7A2');
        $this->addSql('CREATE TABLE track_reservation (id INT AUTO_INCREMENT NOT NULL, player_id INT DEFAULT NULL, track INT NOT NULL, players INT NOT NULL, start_time DATETIME NOT NULL, play_time_amount INT NOT NULL, INDEX IDX_376E2BB99E6F5DF (player_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE track_reservation_user (track_reservation_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_B08651C57A6491A5 (track_reservation_id), INDEX IDX_B08651C5A76ED395 (user_id), PRIMARY KEY(track_reservation_id, user_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE track_reservation ADD CONSTRAINT FK_376E2BB99E6F5DF FOREIGN KEY (player_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE track_reservation_user ADD CONSTRAINT FK_B08651C57A6491A5 FOREIGN KEY (track_reservation_id) REFERENCES track_reservation (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE track_reservation_user ADD CONSTRAINT FK_B08651C5A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE court_reservation');
        $this->addSql('DROP TABLE court_reservation_user');
        $this->addSql('ALTER TABLE training CHANGE teacher_id teacher_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user CHANGE roles roles LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_bin`, CHANGE profile_image profile_image VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE background_image background_image VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE address address VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE mobile mobile VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE company_role company_role VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
    }
}
