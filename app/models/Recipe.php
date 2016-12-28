<?php

use Candy\Base\Model;

class Recipe extends Model {

    protected $table = 'recipe';
    protected $fillable = ['title', 'clip' ,'thumb','one_person_minutes', 'explain', 'point', 'mistake', 'member_id'];

    protected static function rules()
    {
        return [
            'title' => ['required', 'string'],
            'one_person_minutes' => ['required', 'string'],
            'explain' => ['required', 'string'],
            'mistake' => ['string'],
        ];
    }
}
