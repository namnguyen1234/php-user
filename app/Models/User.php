<?php

namespace App\Models;

use App\Models\Model;
use PDO;

class User extends Model
{
    public $id;
    public $username;
    public $password;
    public $first_name;
    public $last_name;
    public $created_at;
    public $updated_at;

    public $str_search;

    public function hasUser($data)
    {
        $obj_select = $this->connection 
            ->prepare("SELECT username, first_name, last_name FROM users WHERE username = :username and password =:password");
            $arr_update = [
                ':username' => $data['username'],
                ':password' =>  $data['password'],
            ];
            $obj_select->execute($arr_update);
            $users = $obj_select->fetchAll(PDO::FETCH_ASSOC);

            return $users;
    }

    public function getAll($perPage,$startAt)
    {
        $obj_select = $this->connection
            ->prepare("SELECT * FROM users ORDER BY updated_at DESC, created_at DESC LIMIT $perPage OFFSET $startAt");
        $obj_select->execute();
        $users = $obj_select->fetchAll(PDO::FETCH_ASSOC);

        return $users;
    }

    public function totalUser() {
        $obj_select = $this->connection
        ->prepare("SELECT COUNT(*) FROM users ORDER BY id ");
        $obj_select->execute();
        $totalUsers = $obj_select->fetchColumn();

        return $totalUsers;
    }

    public function getById($id)
    {
        $obj_select = $this->connection
            ->prepare("SELECT * FROM users WHERE id =:id");
        $select = [
            ':id' => $id
        ];
        $obj_select->execute($select);

        return $obj_select->fetch(PDO::FETCH_ASSOC);
    }

    public function insert($data)
    {
        $obj_insert = $this->connection
            ->prepare("INSERT INTO users(username, password, first_name, last_name)
                             VALUES(:username , :password , :first_name , :last_name)");
        $arr_insert = [
            ':username' =>  $data['username'],
            ':password' =>  $data['password'],
            ':first_name' =>  $data['first_name'],
            ':last_name' =>  $data['last_name'],
        ];

        return $obj_insert->execute($arr_insert);
    }

    public function update($id, $data)
    {
        $obj_update = $this->connection
            ->prepare("UPDATE users SET username =:username , password =:password
             WHERE id =:id");
        $arr_update = [
            ':username' => $data['username'],
            ':password' =>  $data['password'],
            ':id' => $id,
        ];
        $obj_update->execute($arr_update);

        return $obj_update->execute($arr_update);
    }

    public function delete($id)
    {
        $obj_delete = $this->connection
            ->prepare("DELETE FROM users WHERE id =:id");
            $arr_delete = [
                 ':id' => $id,
            ];
        $obj_delete->execute($arr_delete);

        return $obj_delete->execute($arr_delete);
    }
}
