<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240119000002 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE wallet (
        guid UUID DEFAULT uuid_generate_v4() PRIMARY KEY,
        balance INT NOT NULL DEFAULT 0
                    )');

        $this->addSql('ALTER TABLE users ADD COLUMN wallet UUID NOT NULL REFERENCES wallet(guid)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE wallet');
    }
}
