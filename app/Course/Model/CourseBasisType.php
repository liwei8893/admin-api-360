<?php

declare (strict_types=1);
namespace App\Course\Model;

use Mine\MineModel;
/**
 * @property int $id 
 * @property string $name 
 * @property int $parent_id 
 * @property string $level 
 * @property int $states 
 * @property int $title_id 
 */
class CourseBasisType extends MineModel
{
    public $timestamps = false;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'course_basis_type';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'name', 'parent_id', 'level', 'states', 'title_id'];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['id' => 'integer', 'parent_id' => 'integer', 'states' => 'string', 'title_id' => 'integer'];
}