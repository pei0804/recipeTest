<?php

use Candy\Base\Model;

class Recipe extends Model {

    protected $table = 'recipe';
    protected $fillable = ['title', 'clip', 'explain', 'point', 'mistake', 'member_id'];

    protected static function rules()
    {
        return [
            'title' => ['string', 'required'],
            'clip' => ['string', 'mimes:mp4'],
            'mistake' => ['string'],
        ];
    }
}
