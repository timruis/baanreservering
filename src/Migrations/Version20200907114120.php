<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200907114120 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE page_visit ADD user_id INT NOT NULL, CHANGE time time DATETIME NOT NULL');
        $this->addSql('ALTER TABLE page_visit ADD CONSTRAINT FK_25FF16EFA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_25FF16EFA76ED395 ON page_visit (user_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE page_visit DROP FOREIGN KEY FK_25FF16EFA76ED395');
        $this->addSql('DROP INDEX IDX_25FF16EFA76ED395 ON page_visit');
        $this->addSql('ALTER TABLE page_visit DROP user_id, CHANGE time time TIME NOT NULL');
    }
}
