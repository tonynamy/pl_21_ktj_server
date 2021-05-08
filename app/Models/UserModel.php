<?php namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model {

    protected $table      = 'user';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = true;

    protected $allowedFields = ['username', 'password'];

    protected $useTimestamps = false;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    protected $validationRules    = [];
    protected $validationMessages = [];
    protected $skipValidation     = false;

    protected $beforeInsert = ['hashPasswordEvent'];
    protected $beforeUpdate = ['hashPasswordEvent'];

    function hashPassword($password) {

        return password_hash($password, PASSWORD_DEFAULT);

    }

    protected function hashPasswordEvent(array $data) {

        if(!isset($data["data"]["password"])) return $data;

        $data["data"]["password"] = $this->hashPassword($data["data"]["password"]);

        return $data;

    }

}