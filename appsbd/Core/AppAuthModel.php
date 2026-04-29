<?php
namespace appsbd\Core;
use appsbd\Libs\ApiDataResponse;
use appsbd\Libs\Searchable;
use appsbd\Traits\QueryBuilderTrait;
use appsbd\Traits\RequestFillableTrait;
use appsbd\Traits\SearchableTrait;
use appsbd\Traits\SearchDataTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class AppModel
 *
 * @package appsbd\core
 * @method static static find($id)
 */
class AppAuthModel extends Authenticatable{
    use SearchableTrait,QueryBuilderTrait,RequestFillableTrait,SearchDataTrait;
}
