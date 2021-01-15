<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210113102143 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE rest DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE rest ADD PRIMARY KEY (warehouse_id,product_id)');
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE rest DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE rest ADD PRIMARY KEY (product_id)');
    }
}
