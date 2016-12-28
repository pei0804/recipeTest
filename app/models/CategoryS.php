<?php

use Candy\Base\Model;

class CategoryS extends Model {

    protected $table = 'category_s';
    protected $primaryKey = ['id', 'category_l_id', 'category_m_id'];
    protected $fillable = ['category_l_id', 'category_m_id', 'title', 'clip', 'point', 'mistake', 'member_id'];
}