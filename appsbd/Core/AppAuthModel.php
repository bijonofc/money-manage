<?php

namespace appsbd\Core;

use appsbd\Traits\QueryBuilderTrait;
use appsbd\Traits\RequestFillableTrait;
use appsbd\Traits\SearchableTrait;
use appsbd\Traits\SearchDataTrait;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * Class AppModel
 *
 * @method static static find($id)
 */
class AppAuthModel extends Authenticatable
{
    use QueryBuilderTrait,RequestFillableTrait,SearchableTrait,SearchDataTrait;
}
