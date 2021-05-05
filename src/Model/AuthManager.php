<?php

namespace App\Model;

class AuthManager extends AbstractManager
{
    public const TABLE = "user";

    public function checkMail($mail)
    {
        $statement = $this->pdo->prepare("SELECT * FROM " . static::TABLE . " WHERE mail = :mail");
        $statement->bindValue('mail', $mail, \PDO::PARAM_STR);
        $statement->execute();

        return $statement->fetch();
    }
}
