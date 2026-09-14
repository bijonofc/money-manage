<?php

namespace appsbd\Core;

use appsbd\Traits\QueryBuilderTrait;
use appsbd\Traits\RequestFillableTrait;
use appsbd\Traits\SearchableTrait;
use appsbd\Traits\SearchDataTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * Class AppModel
 *
 * @method static static find($id)
 */
class AppModel extends Model
{
    use QueryBuilderTrait,RequestFillableTrait,SearchableTrait,SearchDataTrait;
}
