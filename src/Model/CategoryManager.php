<?php

namespace App\Model;

class CategoryManager extends AbstractManager
{
    public const TABLE = 'category';

    public function update(array $category): bool
    {
        $statement = $this->pdo->prepare("UPDATE " . self::TABLE . " SET `name` = :name WHERE id=:id");
        $statement->bindValue('id', $category['id'], \PDO::PARAM_INT);
        $statement->bindValue('name', $category['name'], \PDO::PARAM_STR);

        return $statement->execute();
    }

    public function insert(array $category): int
    {
        $statement = $this->pdo->prepare("INSERT INTO " . self::TABLE . " (`name`) VALUES (:name)");
        $statement->bindValue('name', $category['name'], \PDO::PARAM_STR);

        $statement->execute();
        return (int)$this->pdo->lastInsertId();
    }
}
