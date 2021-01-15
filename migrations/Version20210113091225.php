<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210113091225 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql('CREATE TABLE city (id INT AUTO_INCREMENT NOT NULL, region_id INT NOT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_region_id (region_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE region (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE rest (product_id VARCHAR(255) NOT NULL, warehouse_id VARCHAR(5) NOT NULL, count INT NOT NULL, date_update DATETIME DEFAULT NULL, date_import DATETIME DEFAULT NULL, INDEX IDX_warehouse_id (warehouse_id), PRIMARY KEY(product_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE warehouse (id VARCHAR(5) NOT NULL, city_id INT NOT NULL, INDEX IDX_city_id (city_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE city ADD CONSTRAINT FK_region_id FOREIGN KEY (region_id) REFERENCES region (id)');
        $this->addSql('ALTER TABLE rest ADD CONSTRAINT FK_warehouse_id FOREIGN KEY (warehouse_id) REFERENCES warehouse (id)');
        $this->addSql('ALTER TABLE warehouse ADD CONSTRAINT FK_city_id FOREIGN KEY (city_id) REFERENCES city (id)');
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE warehouse DROP FOREIGN KEY FK_city_id');
        $this->addSql('ALTER TABLE city DROP FOREIGN KEY FK_region_id');
        $this->addSql('ALTER TABLE rest DROP FOREIGN KEY FK_warehouse_id');
        $this->addSql('DROP TABLE city');
        $this->addSql('DROP TABLE region');
        $this->addSql('DROP TABLE rest');
        $this->addSql('DROP TABLE warehouse');
    }
}
