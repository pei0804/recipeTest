<?php

use Candy\Base\Model;

class Step extends Model {

    protected $table = 'step';
    protected $primaryKey = ['recipe_id', 'step_no'];
    public $incrementing = false;
    protected $fillable = ['recipe_id', 'step_no', 'description', 'img_url'];
}