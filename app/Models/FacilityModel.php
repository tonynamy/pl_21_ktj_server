<?php namespace App\Models;

use CodeIgniter\Model;

class FacilityModel extends Model
{
    protected $table      = 'facility';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = true;

    protected $allowedFields = ['place_id', 'serial', 'o_serial', 'r_num', 'type', 'super_manager', 'subcontractor', 'building', 'floor', 'spot', 'section', 'purpose', 'cube_data', 'cube_result', 'area_data', 'area_result', 'danger_data', 'danger_result', 'created_at', 'started_at', 'finished_at', 'edit_started_at', 'edit_finished_at', 'dis_started_at', 'dis_finished_at', 'expired_at', 'memo'];

    protected $useTimestamps = false;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    protected $validationRules    = [];
    protected $validationMessages = [];
    protected $skipValidation     = false;

}