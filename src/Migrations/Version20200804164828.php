<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200804164828 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE court_reservation ADD memo_text LONGTEXT DEFAULT NULL, ADD reservation_type INT DEFAULT NULL, CHANGE player_id player_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE training CHANGE teacher_id teacher_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user CHANGE roles roles JSON NOT NULL, CHANGE profile_image profile_image VARCHAR(255) DEFAULT NULL, CHANGE background_image background_image VARCHAR(255) DEFAULT NULL, CHANGE address address VARCHAR(255) DEFAULT NULL, CHANGE mobile mobile VARCHAR(255) DEFAULT NULL, CHANGE company_role company_role VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE court_reservation DROP memo_text, DROP reservation_type, CHANGE player_id player_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE training CHANGE teacher_id teacher_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user CHANGE roles roles LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_bin`, CHANGE profile_image profile_image VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE background_image background_image VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE address address VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE mobile mobile VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE company_role company_role VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
    }
}
