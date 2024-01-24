<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240119142548 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(sql: "CREATE TABLE users 
                            (
                                guid uuid NOT NULL DEFAULT uuid_generate_v4(),
                                role VARCHAR NOT NULL, 
                                name VARCHAR NOT NULL,
                                password VARCHAR NOT NULL,
                                email VARCHAR NOT NULL UNIQUE,
                                phone VARCHAR UNIQUE,
                                verified BOOLEAN NOT NULL DEFAULT false,
                                token VARCHAR,
                                created_at TIMESTAMP NOT NULL DEFAULT current_timestamp,
                                updated_at TIMESTAMP NOT NULL DEFAULT current_timestamp,
                                PRIMARY KEY(guid)
                            )"
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE users');
    }
}