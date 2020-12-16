<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 查询单词
 *
 * Class CartItem
 * @package App\Models
 */
class SearchWord extends Model
{
    /**
     * 查询单词
     *
     * @var array
     */
    protected $fillable = ['user_id','word','times','is_deleted'];


}
