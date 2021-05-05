<?php

namespace App\Model;

class UserManager extends AbstractManager
{
    public const TABLE = "user";

    /**
     * Insert new item in database
     */
    public function insert(array $user): int
    {
        $statement = $this->pdo->prepare("INSERT INTO " . self::TABLE . " 
        (`lastName`, `firstName`, `age`, `mail`, `password`, `adresseNumber`, 
        `adresseStreet`,`adressePostal`,`adresseCity`, `phoneNumber`, `avatar` ) 
        VALUES (:lastName, :firstName, :age, :mail, :password, :adresseNumber, 
        :adresseStreet, :adressePostal, :adresseCity, :phoneNumber, :avatar)");

        $statement->bindValue('lastName', $user['lastName'], \PDO::PARAM_STR);
        $statement->bindValue('firstName', $user['firstName'], \PDO::PARAM_STR);
        $statement->bindValue('age', $user['age'], \PDO::PARAM_STR);
        $statement->bindValue('mail', $user['mail'], \PDO::PARAM_STR);
        $statement->bindValue('password', $user['password'], \PDO::PARAM_STR);
        $statement->bindValue('adresseNumber', $user['adresseNumber'], \PDO::PARAM_INT);
        $statement->bindValue('adresseStreet', $user['adresseStreet'], \PDO::PARAM_STR);
        $statement->bindValue('adressePostal', $user['adressePostal'], \PDO::PARAM_INT);
        $statement->bindValue('adresseCity', $user['adresseCity'], \PDO::PARAM_STR);
        $statement->bindValue('phoneNumber', $user['phoneNumber'], \PDO::PARAM_INT);
        $statement->bindValue('avatar', $user['avatar'], \PDO::PARAM_STR);

        $statement->execute();
        return (int)$this->pdo->lastInsertId();
    }

    /**
     * Update user in database
     */
    public function update(array $user): bool
    {
        $statement = $this->pdo->prepare("UPDATE " . self::TABLE . " SET 
        `lastName`= :lastName, `firstName`= :firstName, `age`= :age, `mail`= :mail, `password`= :password,
        `adresseNumber`= :adresseNumber, `adresseStreet`= :adresseStreet, `adressePostal`= :adressePostal,
        `adresseCity`= :adresseCity, `phoneNumber`= :phoneNumber, `avatar`= :avatar WHERE id=:id");
        $statement->bindValue('id', $user['id'], \PDO::PARAM_INT);
        $statement->bindValue('lastName', $user['lastName'], \PDO::PARAM_STR);
        $statement->bindValue('firstName', $user['firstName'], \PDO::PARAM_STR);
        $statement->bindValue('age', $user['age'], \PDO::PARAM_STR);
        $statement->bindValue('mail', $user['mail'], \PDO::PARAM_STR);
        $statement->bindValue('password', $user['password'], \PDO::PARAM_STR);
        $statement->bindValue('adresseNumber', $user['adresseNumber'], \PDO::PARAM_INT);
        $statement->bindValue('adresseStreet', $user['adresseStreet'], \PDO::PARAM_STR);
        $statement->bindValue('adressePostal', $user['adressePostal'], \PDO::PARAM_INT);
        $statement->bindValue('adresseCity', $user['adresseCity'], \PDO::PARAM_STR);
        $statement->bindValue('phoneNumber', $user['phoneNumber'], \PDO::PARAM_INT);
        $statement->bindValue('avatar', $user['avatar'], \PDO::PARAM_STR);

        return $statement->execute();
    }

    /**
     * Search barre
     */

    public function searchByUser(string $term): array
    {
        $statement = $this->pdo->prepare("SELECT * FROM " . static::TABLE .
        " JOIN advert ON user.id = advert.user_id WHERE lastName LIKE '" . $term . "%'");

        $statement->bindValue('term', $term, \PDO::PARAM_STR);
        $statement->execute();

        return $statement->fetchAll();
    }

    /**
     * Update Badge
     */

    public function updateBadge($user)
    {
        $statement = $this->pdo->prepare("UPDATE " . self::TABLE . " SET `badge`= :badge WHERE id=:id");

        $statement->bindValue('id', $user['id'], \PDO::PARAM_INT);
        $statement->bindValue('badge', $user['badge'], \PDO::PARAM_INT);

        return $statement->execute();
    }

    public function updateRang($user)
    {
        $statement = $this->pdo->prepare("UPDATE " . self::TABLE . " SET `rang`= :rang WHERE id=:id");

        $statement->bindValue('id', $user['id'], \PDO::PARAM_INT);
        $statement->bindValue('rang', $user['rang'], \PDO::PARAM_INT);

        return $statement->execute();
    }

    public function bestUser()
    {
        $query = 'SELECT user.*, adverthelp.id advertId, ROUND(AVG(reviews.rate), 1) rate
        FROM ' . static::TABLE .
        ' JOIN adverthelp ON adverthelp.user_id = user.id
        JOIN advert ON advert.user_id = user.id
        JOIN reviews ON reviews.advertHelp_id = adverthelp.id
        GROUP by user.id
        ORDER BY rate DESC
        LIMIT 5';

        return $this->pdo->query($query)->fetchAll();
    }
}
