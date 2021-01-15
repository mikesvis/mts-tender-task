<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210114075253 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE import_stat (id INT AUTO_INCREMENT NOT NULL, time_start INT NOT NULL, time_end INT DEFAULT NULL, count_total INT DEFAULT 0 NOT NULL, count_error INT DEFAULT 0 NOT NULL, load_average NUMERIC(4, 1) NOT NULL, rows_deleted INT DEFAULT 0 NOT NULL, is_full INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE city RENAME INDEX idx_region_id TO IDX_2D5B023498260155');
        $this->addSql('ALTER TABLE remains RENAME INDEX idx_warehouse_id TO IDX_29EE350F5080ECDE');
        $this->addSql('ALTER TABLE warehouse RENAME INDEX idx_city_id TO IDX_ECB38BFC8BAC62AF');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE import_stat');
        $this->addSql('ALTER TABLE city RENAME INDEX idx_2d5b023498260155 TO IDX_region_id');
        $this->addSql('ALTER TABLE remains RENAME INDEX idx_29ee350f5080ecde TO IDX_warehouse_id');
        $this->addSql('ALTER TABLE warehouse RENAME INDEX idx_ecb38bfc8bac62af TO IDX_city_id');
    }
}
