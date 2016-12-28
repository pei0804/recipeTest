<?php

use Candy\Base\Model;

class Ingredients extends Model {

    protected $table = 'recipe';
    public $incrementing = false;
    protected $fillable = ['ingredients_no', 'name', 'quantity'];
}