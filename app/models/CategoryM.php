<?php

use Candy\Base\Model;

class CategoryM extends Model {

    protected $table = 'category_m';
    protected $primaryKey = ['id', 'category_l_id'];
    protected $fillable = ['category_l_id', 'name'];
}