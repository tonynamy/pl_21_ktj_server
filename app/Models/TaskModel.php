<?php namespace App\Models;

use CodeIgniter\Model;

class TaskModel extends Model {

    protected $table      = 'task';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = true;

    protected $allowedFields = ['type', 'place_id', 'facility_serial', 'team_id', 'size', 'is_square', 'manday'];

    protected $useTimestamps = false;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    protected $validationRules    = [];
    protected $validationMessages = [];
    protected $skipValidation     = false;

}