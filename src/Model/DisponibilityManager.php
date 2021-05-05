<?php

namespace App\Model;

class DisponibilityManager extends AbstractManager
{
    public const TABLE = 'disponibility';

    public function update(array $disponibility): bool
    {
        $statement = $this->pdo->prepare("UPDATE " . self::TABLE . " SET `timetable` = :timetable WHERE id=:id");
        $statement->bindValue('id', $disponibility['id'], \PDO::PARAM_INT);
        $statement->bindValue('timetable', $disponibility['timetable'], \PDO::PARAM_STR);

        return $statement->execute();
    }

    public function insert(array $disponibility): int
    {
        $statement = $this->pdo->prepare("INSERT INTO " . self::TABLE . " (`timetable`) VALUE (:timetable)");
        $statement->bindValue('timetable', $disponibility['timetable'], \PDO::PARAM_STR);

        $statement->execute();
        return (int)$this->pdo->lastInsertId();
    }
}
