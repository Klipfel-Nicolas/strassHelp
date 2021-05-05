<?php

namespace App\Model;

class AdvertManager extends AbstractManager
{
    public const TABLE = 'advert';

    public function insert(array $advert): int
    {
        $statement = $this->pdo->prepare("INSERT INTO " . self::TABLE . " 
        (user_id, title, category_id, description, disponibility_id)
        VALUES 
        (:user_id, :title, :category_id, :description, :disponibility_id)");
        $statement->bindValue('user_id', $advert['user_id'], \PDO::PARAM_INT);
        $statement->bindValue('title', $advert['title'], \PDO::PARAM_STR);
        $statement->bindValue('category_id', $advert['category_id'], \PDO::PARAM_INT);
        $statement->bindValue('description', $advert['description'], \PDO::PARAM_STR);
        $statement->bindValue('disponibility_id', $advert['disponibility_id'], \PDO::PARAM_INT);

        $statement->execute();
        return (int)$this->pdo->lastInsertId();
    }

    public function update(array $advert): bool
    {
        $statement = $this->pdo->prepare("UPDATE " . self::TABLE . " 
        SET 
        `user_id`= :user_id,
        `title`= :title ,
        `category_id`= :category_id,
        `description` = :description ,
        `disponibility_id` = :disponibility_id
         WHERE id=:id");
        $statement->bindValue('user_id', $advert['user_id'], \PDO::PARAM_INT);
        $statement->bindValue('id', $advert['id'], \PDO::PARAM_INT);
        $statement->bindValue('title', $advert['title'], \PDO::PARAM_STR);
        $statement->bindValue('category_id', $advert['category_id'], \PDO::PARAM_INT);
        $statement->bindValue('description', $advert['description'], \PDO::PARAM_STR);
        $statement->bindValue('disponibility_id', $advert['disponibility_id'], \PDO::PARAM_INT);

        return $statement->execute();
    }

    public function selectByUserId(int $id): array
    {
        // prepared request
        $statement = $this->pdo->prepare("SELECT * FROM " . static::TABLE . " WHERE user_id=:user_id");
        $statement->bindValue('user_id', $id, \PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll();
    }

    public function selectByCategoryId(int $id): array
    {
        $statement = $this->pdo->prepare("SELECT * FROM " . static::TABLE . " WHERE category_id=:category_id");
        $statement->bindValue('category_id', $id, \PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll();
    }

    public function selectByDisponibility(int $id): array
    {
        $statement = $this->pdo->prepare("SELECT * FROM " . static::TABLE .
        " WHERE disponibility_id=:disponibility_id");
        $statement->bindValue('disponibility_id', $id, \PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll();
    }

    public function bestAdvert()
    {
        $query = 'SELECT advert.*, user.firstName, user.lastName, user.avatar, category.name , ROUND(AVG(reviews.rate), 1) rate, COUNT(reviews.rate) nbRate
        FROM ' . static::TABLE .
        ' JOIN user ON user.id = advert.user_id
        JOIN adverthelp ON advert.id = adverthelp.advert_id
        JOIN reviews ON reviews.advertHelp_id = adverthelp.id
        JOIN category ON advert.category_id = category.id
        GROUP by advert.id
        ORDER BY nbRate DESC, rate DESC
        LIMIT 5';

        return $this->pdo->query($query)->fetchAll();
    }
}
