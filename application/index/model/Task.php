<?php
namespace app\index\model;

use think\Model;

class Task extends Model
{
    protected $autoWriteTimestamp = true;
    protected $createTime = "createtime";
    protected $updateTime=false;
}

