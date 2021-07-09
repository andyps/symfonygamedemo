<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210710185007 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(
            'INSERT INTO prize (type, name, description, is_convertable, convertion_rate, amount_from, amount_to) VALUES' .
            "('points', 'Points', 'Bonus points', false, 1, 1, 1000)," .
            "('money', 'Money', 'Some EUR', true, 2, 10, 500)," .
            "('physical', 'Car', 'Some car', false, 0, 1, 1)," .
            "('physical', 'Microwave oven', 'Microwave oven', false, 0, 1, 1)," .
            "('physical', 'Book', 'Book for children', false, 0, 1, 1)"
        );
    }

    public function down(Schema $schema): void
    {
    }
}
