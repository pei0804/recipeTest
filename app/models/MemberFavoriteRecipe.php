<?php

use Candy\Base\Model;

class MemberFavoriteRecipe extends Model {

    protected $table = 'member_favorite_recipe';
    protected $primaryKey = ['member_id', 'recipe_id'];
    protected $fillable = ['member_id', 'recipe_id'];
}