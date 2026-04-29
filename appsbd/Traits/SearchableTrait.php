<?php
namespace appsbd\Traits;

use appsbd\Libs\Searchable;

trait SearchableTrait {
    use Searchable;

    public static function getDefaultSearchProps() {
        return [];
    }
}
