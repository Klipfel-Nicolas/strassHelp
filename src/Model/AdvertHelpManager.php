<?php

namespace App\Model;

class AdvertHelpManager extends AbstractManager
{
    public const TABLE = 'adverthelp';

    public function insert(array $adverthelp): int
    {
        $statement = $this->pdo->prepare("INSERT INTO " . self::TABLE .
        "(advert_id, user_id, message, date, isValidate, id_chat, id_author) 
        VALUES 
        (:advert_id, :user_id, :message, :date, :isValidate, :id_chat, :id_author)");
        $statement->bindValue('advert_id', $adverthelp['advert_id'], \PDO::PARAM_INT);
        $statement->bindValue('user_id', $adverthelp['user_id'], \PDO::PARAM_INT);
        $statement->bindValue('message', $adverthelp['message'], \PDO::PARAM_STR);
        $statement->bindValue('date', $adverthelp['date'], \PDO::PARAM_STR);
        $statement->bindValue('isValidate', $adverthelp['isValidate'], \PDO::PARAM_BOOL);
        $statement->bindValue('id_chat', $adverthelp['id_chat'], \PDO::PARAM_STR);
        $statement->bindValue('id_author', $adverthelp['id_author'], \PDO::PARAM_INT);
        $statement->execute();
        return (int)$this->pdo->lastInsertId();
    }

    public function selectAllMessageByHelp($id): array
    {
        $statement = $this->pdo->prepare("SELECT * FROM " . static::TABLE . " WHERE id_chat=:id");
        $statement->bindValue('id', $id, \PDO::PARAM_STR);
        $statement->execute();

        return $statement->fetchAll();
    }

    public function selectAllHelpByUser($id): array
    {
        //var_dump($id); die();
        $statement = $this->pdo->prepare("SELECT  
        adverthelp.advert_id, adverthelp.user_id helper,id_author, 
        reviews.user_id helped, id_chat, isValidate, category_id, title, rate
        FROM " . static::TABLE . " 
        JOIN advert ON advert.id = adverthelp.advert_id
        LEFT JOIN reviews ON adverthelp.id = reviews.advertHelp_id");

        $statement->bindValue('id', $id, \PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll();
    }
}
