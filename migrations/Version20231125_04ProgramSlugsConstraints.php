<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20231125_04ProgramSlugsConstraints extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE program ALTER COLUMN slug SET NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX PROGRAM_UNIQUE_SLUG ON program (slug)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX PROGRAM_UNIQUE_SLUG');
        $this->addSql('ALTER TABLE program ALTER COLUMN slug DROP NOT NULL');
    }
}
