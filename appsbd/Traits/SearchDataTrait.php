<?php
namespace appsbd\Traits;

use appsbd\Libs\ApiDataResponse;
use appsbd\Libs\Searchable;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Log;

trait SearchDataTrait {
    use SearchableTrait,Searchable;
    public static function getGridProps() {
        return ['*'];
    }

   /* public static function getSearchData(
        &$response,
        $collection_class = JsonResource::class,
        $with = [],
        $withCount = [],
        $where = [],
        $select = null,
        $orWhere=[]
    ) {
        $offset = ($response->getPage() - 1) * $response->getLimit();
        $query = static::query();

        if (!empty($select)) {
            $query->select($select);
        } else {
            $query->select(static::getGridProps());
        }
        if (!empty($where)) {
            foreach ($where as $key => $value) {
                $query->where($key, $value);
            }
        }
        if (!empty($orWhere)) {
            $query->orWhere(function ($q) use ($orWhere) {
                foreach ($orWhere as $key => $value) {
                    // এখানে where ব্যবহার করছি যাতে ভিতরে AND হয়
                    $q->where($key, $value);
                }
            });
        }
        if(!empty($response->src_by)){
            foreach ( $response->src_by as $src_item ) {
                if('*'==$src_item['prop'] && !empty($src_item['val'])){
                    $allSeachProp=static::getDefaultSearchProps();
                    if(!empty($allSeachProp)) {
                        $query = static::search( $src_item['val'], $allSeachProp );
                        break;
                    }
                }else{
                    if (strpos($src_item['prop'], '.') !== false) {
                        [$relation, $column] = explode('.', $src_item['prop']);
                        $query->whereHas($relation, function ($q) use ($column, $src_item) {
                            if ('like' == $src_item['opr']) {
                                $q->where($column, 'like', '%' . $src_item['val'] . '%');
                            } elseif ('eq' == $src_item['opr']) {
                                $q->where($column, '=', $src_item['val']);
                            } elseif ('lt' == $src_item['opr']) {
                                $q->where($column, '<', $src_item['val']);
                            } elseif ('gt' == $src_item['opr']) {
                                $q->where($column, '>', $src_item['val']);
                            } elseif ('le' == $src_item['opr']) {
                                $q->where($column, '<=', $src_item['val']);
                            } elseif ('ge' == $src_item['opr']) {
                                $q->where($column, '>=', $src_item['val']);
                            }elseif ( 'dt' == $src_item['opr'] ) {
                                $src_item['val'];
                                if ( ! empty( $src_item['val'] ) ) {
                                    $start_date = date( 'Y-m-d 00:00:00', strtotime($src_item['val']) );
                                    $end_date    = date( 'Y-m-d 23:59:59', strtotime($src_item['val']) );
                                    if ( ! empty( $start_date ) && ! empty( $end_date ) ) {
                                        $q->whereBetween( $src_item['prop'],[$start_date,$end_date]);
                                    }
                                }
                            } elseif ( 'dr' == $src_item['opr'] ) {
                                $prop = (object) $src_item['val'];
                                if ( ! empty( $prop->start ) ) {
                                    if ( empty( $prop->end ) ) {
                                        $prop->end = $prop->start;
                                    }
                                    $start_date = date( 'Y-m-d 00:00:00', strtotime($prop->start) );
                                    $end_date    = date( 'Y-m-d 23:59:59', strtotime($prop->end) );
                                    appsbdFileLog('filter',$prop);
                                    if ( ! empty( $start_date ) && ! empty( $end_date ) ) {
                                        $q->whereBetween( $src_item['prop'],[$start_date,$end_date]);
                                    }
                                }
                            } elseif ( 'bt' == $src_item['opr'] ) {
                                $prop = (object) $src_item['val'];
                                if ( ! empty( $prop->start ) ) {
                                    if ( empty( $prop->end ) ) {
                                        $prop->end = $prop->start;
                                    }
                                    $prop->start = sanitize_text_field( $prop->start );
                                    $prop->end   = sanitize_text_field( $prop->end );

                                    if ( ! empty( $prop->start ) && ! empty( $prop->end ) ) {
                                        $q->whereBetween( $src_item['prop'],[$prop->start,$prop->end]);
                                    }
                                }
                            }
                        });
                    }else{
                        if ( 'like' == $src_item['opr'] ) {
                            $query->where($src_item['prop'],'like','%'.$src_item['val'].'%');
                        } elseif ( 'eq' == $src_item['opr'] ) {
                            $query->where($src_item['prop'],'=',$src_item['val']);
                        } elseif ( 'lt' == $src_item['opr'] ) {
                            $query->where($src_item['prop'],'<',$src_item['val']);
                        } elseif ( 'gt' == $src_item['opr'] ) {
                            $query->where($src_item['prop'],'>',$src_item['val']);
                        } elseif ( 'le' == $src_item['opr'] ) {
                            $query->where($src_item['prop'],'<=',$src_item['val']);
                        } elseif ( 'ge' == $src_item['opr'] ) {
                            $query->where($src_item['prop'],'>=',$src_item['val']);
                        } elseif ( 'dt' == $src_item['opr'] ) {
                            $src_item['val'];
                            if ( ! empty( $src_item['val'] ) ) {
                                $start_date = date( 'Y-m-d 00:00:00', strtotime($src_item['val']) );
                                $end_date    = date( 'Y-m-d 23:59:59', strtotime($src_item['val']) );
                                if ( ! empty( $start_date ) && ! empty( $end_date ) ) {
                                    $query->whereBetween( $src_item['prop'],[$start_date,$end_date]);
                                }
                            }
                        } elseif ( 'dr' == $src_item['opr'] ) {
                            $prop = (object) $src_item['val'];
                            if ( ! empty( $prop->start ) ) {
                                if ( empty( $prop->end ) ) {
                                    $prop->end = $prop->start;
                                }
                                $start_date = date( 'Y-m-d 00:00:00', strtotime($prop->start) );
                                $end_date    = date( 'Y-m-d 23:59:59', strtotime($prop->end) );
                                appsbdFileLog('filter',$prop);
                                if ( ! empty( $start_date ) && ! empty( $end_date ) ) {
                                    $query->whereBetween( $src_item['prop'],[$start_date,$end_date]);
                                }
                            }
                        } elseif ( 'bt' == $src_item['opr'] ) {
                            $prop = (object) $src_item['val'];
                            if ( ! empty( $prop->start ) ) {
                                if ( empty( $prop->end ) ) {
                                    $prop->end = $prop->start;
                                }
                                $prop->start = sanitize_text_field( $prop->start );
                                $prop->end   = sanitize_text_field( $prop->end );

                                if ( ! empty( $prop->start ) && ! empty( $prop->end ) ) {
                                    $query->whereBetween( $src_item['prop'],[$prop->start,$prop->end]);
                                }
                            }
                        }
                    }
                }
                //$query->where( 'title', 'like', 'Ma%' );
            }
        }
        if(!empty($response->sort_by)) {
            foreach ( $response->sort_by as $sort_item ) {
                if(!empty($sort_item['prop']) && !empty($sort_item['ord'])) {
                    $query->orderBy( $sort_item['prop'], $sort_item['ord'] );
                }
            }
        }

        \Illuminate\Support\Facades\Log::info($query->toSql());
        Log::info($query->getBindings());
        $count=$query->count();
        $response->setRecordCount($count);
        if($count>0){
            if (!empty($with)) {
                $query->with($with);
            }
            if(!empty($withCount)) {
                $query->withCount($withCount);
            }
            $data = $query->offset( $offset )->limit( $response->getLimit())->get();
            $response->setData($collection_class::collection( $data));
        }else{
            $response->setData([]);
        }
    }*/

    public static function getSearchData(
        &$response,
        $collection_class = JsonResource::class,
        $with = [],
        $withCount = [],
        $where = [],
        $select = null,
        $orWhere = [],
        $query=null
    ) {
        $offset = ($response->getPage() - 1) * $response->getLimit();
        if(is_null($query)) {
            $query = static::query();
        }

        // =======================
        // SELECT
        // =======================
        if (!empty($select)) {
            $query->select($select);
        } else {
            $query->select(static::getGridProps());
        }

        // =======================
        // BASE WHERE + OR WHERE (GROUPED)
        // =======================
        if (!empty($where) || !empty($orWhere)) {
            $query->where(function ($q) use ($where, $orWhere) {

                // Normal where (AND)
                if (!empty($where)) {
                    foreach ($where as $key => $value) {
                        $q->where($key, $value);
                    }
                }

                // OR where (grouped AND inside)
                if (!empty($orWhere)) {
                    $q->orWhere(function ($q2) use ($orWhere) {
                        foreach ($orWhere as $key => $value) {
                            $q2->where($key, $value);
                        }
                    });
                }

            });
        }

        // =======================
        // SEARCH / FILTER (src_by)
        // =======================
        if (!empty($response->src_by)) {
            foreach ($response->src_by as $src_item) {

                // Global search (*)
                if ('*' == $src_item['prop'] && !empty($src_item['val'])) {
                    $allSeachProp = static::getDefaultSearchProps();
                    if (!empty($allSeachProp)) {
                        $query = static::search($src_item['val'], $allSeachProp);
                        break;
                    }
                } else {

                    // =======================
                    // RELATION COLUMN FILTER
                    // =======================
                    if (strpos($src_item['prop'], '.') !== false) {
                        [$relation, $column] = explode('.', $src_item['prop']);

                        $query->whereHas($relation, function ($q) use ($column, $src_item) {
                            self::applyFilter($q, $column, $src_item);
                        });

                    }
                    // =======================
                    // NORMAL COLUMN FILTER
                    // =======================
                    else {
                        self::applyFilter($query, $src_item['prop'], $src_item);
                    }
                }
            }
        }

        // =======================
        // SORTING
        // =======================
        if (!empty($response->sort_by)) {
            foreach ($response->sort_by as $sort_item) {
                if (!empty($sort_item['prop']) && !empty($sort_item['ord'])) {
                    $query->orderBy($sort_item['prop'], $sort_item['ord']);
                }
            }
        }

        // =======================
        // COUNT & DATA
        // =======================
        $count = $query->count();
        $response->setRecordCount($count);

        if ($count > 0) {
            if (!empty($with)) {
                $query->with($with);
            }
            if (!empty($withCount)) {
                $query->withCount($withCount);
            }

            $data = $query->offset($offset)
                          ->limit($response->getLimit())
                          ->get();

            $response->setData($collection_class::collection($data));
        } else {
            $response->setData([]);
        }
    }

    private static function applyFilter($query, $column, $src_item)
    {
        if ('like' == $src_item['opr']) {
            $query->where($column, 'like', '%' . $src_item['val'] . '%');

        } elseif ('eq' == $src_item['opr']) {
            $query->where($column, '=', $src_item['val']);

        } elseif ('lt' == $src_item['opr']) {
            $query->where($column, '<', $src_item['val']);

        } elseif ('gt' == $src_item['opr']) {
            $query->where($column, '>', $src_item['val']);

        } elseif ('le' == $src_item['opr']) {
            $query->where($column, '<=', $src_item['val']);

        } elseif ('ge' == $src_item['opr']) {
            $query->where($column, '>=', $src_item['val']);

        } elseif ('dt' == $src_item['opr']) {
            if (!empty($src_item['val'])) {
                $start_date = date('Y-m-d 00:00:00', strtotime($src_item['val']));
                $end_date   = date('Y-m-d 23:59:59', strtotime($src_item['val']));
                $query->whereBetween($column, [$start_date, $end_date]);
            }

        } elseif ('dr' == $src_item['opr']) {
            $prop = (object) $src_item['val'];
            if (!empty($prop->start)) {
                if (empty($prop->end)) {
                    $prop->end = $prop->start;
                }
                $start_date = date('Y-m-d 00:00:00', strtotime($prop->start));
                $end_date   = date('Y-m-d 23:59:59', strtotime($prop->end));
                $query->whereBetween($column, [$start_date, $end_date]);
            }

        } elseif ('bt' == $src_item['opr']) {
            $prop = (object) $src_item['val'];
            if (!empty($prop->start)) {
                if (empty($prop->end)) {
                    $prop->end = $prop->start;
                }
                $prop->start = sanitize_text_field($prop->start);
                $prop->end   = sanitize_text_field($prop->end);
                $query->whereBetween($column, [$prop->start, $prop->end]);
            }
        }
    }


}
