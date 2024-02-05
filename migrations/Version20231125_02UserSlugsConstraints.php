<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20231125_02UserSlugsConstraints extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE "user" ALTER COLUMN slug SET NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX USER_UNIQUE_SLUG ON "user" (slug)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX USER_UNIQUE_SLUG');
        $this->addSql('ALTER TABLE "user" ALTER COLUMN slug DROP NOT NULL');
    }
}
