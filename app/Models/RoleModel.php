<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoleModel extends Model
{
    use HasFactory;

    /**
     * 表單名稱
     *
     * @var string
     */
    protected $table = 'role';

    /**
     * 主鍵
     *
     * @var string
     */
    protected $primaryKey = "id";
}
