<?php

use Candy\Base\Model;

class Ingredients extends Model {

    protected $table = 'ingredients';
    public $incrementing = false;
    protected $fillable = ['id', 'ingredients_no', 'name', 'quantity'];

    protected static function rules()
    {
        return [
            'name' => ['required', 'string'],
            'quantity' => ['required', 'integer'],
        ];
    }
}