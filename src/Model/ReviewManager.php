<?php

namespace App\Model;

class ReviewManager extends AbstractManager
{

    public const TABLE = 'reviews';
    /**
     * Insert new item in database
     */
    public function insert(array $help): int
    {
        $statement = $this->pdo->prepare("INSERT INTO " . self::TABLE . " 
        ( `advert_id`, `user_id`, `advertHelp_id`, `rate`, `comment`, `date` ) 
        VALUES (:advert_id, :user_id, :advertHelp_id, :rate, :comment, :date)");

        $statement->bindValue('advert_id', $help['advert_id'], \PDO::PARAM_INT);
        $statement->bindValue('user_id', $help['user_id'], \PDO::PARAM_INT);
        $statement->bindValue('advertHelp_id', $help['advertHelp_id'], \PDO::PARAM_INT);
        $statement->bindValue('rate', $help['rate'], \PDO::PARAM_INT);
        $statement->bindValue('comment', $help['comment'], \PDO::PARAM_STR);
        $statement->bindValue('date', $help['date'], \PDO::PARAM_STR);

        $statement->execute();
        return (int)$this->pdo->lastInsertId();
    }

    /**
     * edit review
     */
    public function edit($id)
    {
        $statement = $this->pdo->prepare("UPDATE " . self::TABLE . " SET 
        `advert_id`= :advert_id, `user_id`= :user_id, `advertHelp_id`= :advertHelp_id, `rate`= :rate,
         `comment`= :comment, `date`= :date WHERE id=:id");

        $statement->bindValue('advert_id', $id['advert_id'], \PDO::PARAM_INT);
        $statement->bindValue('user_id', $id['user_id'], \PDO::PARAM_INT);
        $statement->bindValue('advertHelp_id', $id['advertHelp_id'], \PDO::PARAM_INT);
        $statement->bindValue('rate', $id['rate'], \PDO::PARAM_INT);
        $statement->bindValue('comment', $id['comment'], \PDO::PARAM_STR);
        $statement->bindValue('date', $id['date'], \PDO::PARAM_STR);
    }

    /**
     * Select by
     */
    public function allReviews()
    {
        $query = 'SELECT * FROM ' . static::TABLE;

        return $this->pdo->query($query)->fetchAll();
    }

     /**
     * Get one row from database by ID.
     *
     */
    public function selectOneByHelpId(int $id)
    {
        $statement = $this->pdo->prepare("SELECT * FROM " . static::TABLE . " WHERE advertHelp_id=:advertHelp_id");
        $statement->bindValue('advertHelp_id', $id, \PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetch();
    }

    /**
     * the average ant the count by advert
     */
    public function averageCount()
    {
        $query = 'SELECT advert_id advert, user_id helper, ROUND(AVG(rate), 1) average, COUNT(*) nbRate 
        FROM ' . static::TABLE . ' GROUP BY advert_id';
        return $this->pdo->query($query)->fetchAll();
    }

   public function averageByUser($id)
   {
    $query = 'SELECT adverthelp.user_id user, adverthelp.id, ROUND(AVG(rate), 1) average, COUNT(*) nbRate 
    FROM ' . static::TABLE . '
    JOIN adverthelp ON adverthelp.id = reviews.advertHelp_id
    WHERE adverthelp.user_id = '. $id ;
    return $this->pdo->query($query)->fetchAll();
   }
}
