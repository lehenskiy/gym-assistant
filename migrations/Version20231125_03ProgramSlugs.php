<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20231125_03ProgramSlugs extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE program ADD slug VARCHAR(60)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE program DROP slug');
    }
}
