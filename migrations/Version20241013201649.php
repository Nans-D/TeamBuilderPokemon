<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241013201649 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE damage_relation DROP FOREIGN KEY FK_100A8D778C9334FB');
        $this->addSql('ALTER TABLE damage_relation DROP FOREIGN KEY FK_100A8D77E2435F8');
        $this->addSql('DROP TABLE damage_relation');
        $this->addSql('DROP TABLE type');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE damage_relation (id INT AUTO_INCREMENT NOT NULL, source_type_id INT DEFAULT NULL, target_type_id INT DEFAULT NULL, relation_type VARCHAR(50) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, INDEX IDX_100A8D77E2435F8 (target_type_id), INDEX IDX_100A8D778C9334FB (source_type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE type (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(50) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, url VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE damage_relation ADD CONSTRAINT FK_100A8D778C9334FB FOREIGN KEY (source_type_id) REFERENCES type (id)');
        $this->addSql('ALTER TABLE damage_relation ADD CONSTRAINT FK_100A8D77E2435F8 FOREIGN KEY (target_type_id) REFERENCES type (id)');
    }
}
