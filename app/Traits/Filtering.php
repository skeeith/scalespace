<?php

/*
 * This file is part of laravel filtering package.
 *
 * Author (a) Gether Kestrel B. Medel <gether.medel@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Copyright (c) 2017 Gether Kestrel B. Medel
 */

/*
|--------------------------------------------------------------------------
| Auto filtering with pagination for laravel models
|--------------------------------------------------------------------------
|
| Here is where all the filtering logic happens, from filtering the
| current model to filtering relationships and entrust trait.
|
*/

namespace App\Traits;

use Illuminate\Support\Carbon;

trait Filtering
{
    /**
     * Retrieve archives for model.
     *
     * @param  Illuminate\Database\Eloquent\Builder $query
     *
     * @return Illuminate\Database\Eloquent\Builder array
     */
    public static function scopeArchives($query)
    {
        $query->selectRaw('year(created_at) as year, monthname(created_at) as month, count(*) as published')
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc');

        if ($month = request()->month) {
            $query->whereMonth('created_at', Carbon::parse($month)->month);
        }

        if ($year = request()->year) {
            $query->whereYear('created_at', $year);
        }

        return $query->get();
    }

    /**
     * Automatic filter for fields.
     *
     * @param  Illuminate\Database\Eloquent\Builder $query
     * @param  \Illimunate\Http\Request             $request
     *
     * @return void
     */
    public function scopeFilter($query, $request): void
    {
        if (count($request->all()) == 0) {
            return;
        }

        $query->where(function ($query) use ($request) {
            foreach (array_filter($request->all(), 'strlen') as $column => $value) {
                $column = self::convertToColumn($column);
                if (in_array($column, $this->fillable) && $value != null) {
                    if (self::checkColumns($column) && ! is_array($value)) {
                        if (self::checkIfQueryShouldBeStrict($request->is_strict)) {
                            $query->where($column, 'Like', '%' . $value . '%');
                        } else {
                            $query->orWhere($column, 'Like', '%' . $value . '%');
                        }
                    }

                    if (self::checkColumns($column) && is_array($value)) {
                        if (self::checkIfQueryShouldBeStrict($request->is_strict)) {
                            $query->where(function ($query) use ($column, $value) {
                                foreach ($value as $arrayValue) {
                                    if ($arrayValue !== null) {
                                        $query->orWhere($column, 'Like', '%' . $arrayValue . '%');
                                    }
                                }
                            });
                        } else {
                            $query->orWhere(function ($query) use ($column, $value) {
                                foreach ($value as $arrayValue) {
                                    if ($arrayValue !== null) {
                                        $query->orWhere($column, 'Like', '%' . $arrayValue . '%');
                                    }
                                }
                            });
                        }
                    }
                }
            }
        });

        self::checkTraits($query, $request);
    }

    /**
     * Check columns if available.
     *
     * @param  string  $column
     *
     * @return boolean
     */
    public function checkColumns($column): bool
    {
        if (
            strpos($column, 'searchColumn') == false
            && strpos($column, 'searchArrayColumn') == false
            && strpos($column, 'searchArray') == false
            && strpos($column, 'search') == false
            && strpos($column, 'FromModel') == false
            && strpos($column, 'Model') == false
            && strpos($column, 'frommodel') == false
            && strpos($column, 'model') == false
            && strpos($column, 'from_') == false
            && strpos($column, 'from_model_') == false
        ) {
            return true;
        }

        return false;
    }

    /**
     * Check model if it uses other filtering traits.
     *
     * @param  Illuminate\Database\Eloquent\Builder $query
     * @param  \Illimunate\Http\Request             $request
     *
     * @return void
     */
    public function checkTraits($query, $request): void
    {
        foreach (class_uses($this) as $trait) {
            if (strpos($trait, 'FilterRelationships') !== false) {
                $this->filterRelationships($query, $request);
            }

            if (strpos($trait, 'FilterEntrust') !== false) {
                $this->filterEntrust($query, $request);
            }
        }
    }

    /**
     * Check if query should be strict.
     *
     * @param  boolean $isStrict
     *
     * @return boolean
     */
    public function checkIfQueryShouldBeStrict($isStrict = false)
    {
        return ($isStrict === "true" || $isStrict === true || $isStrict === 1) ? true : false;
    }

    /**
     * Convert key string to column name.
     *
     * @param  string $column
     *
     * @return string
     */
    public function convertToColumn($column)
    {
        $column = preg_replace('/searchColumn|searchArrayColumn|searchArray|search|FromModel.+|Model.+|from_model_.+|from_.+/i', '', $column);

        preg_match_all('/[A-Z]/', $column, $matches, PREG_OFFSET_CAPTURE);

        foreach ($matches[0] as $column => $matchPair) {
            $column = str_replace($matchPair[0], '_' . lcfirst($matchPair[0]), $column);
        }

        if (strpos($column, '_') !== false && preg_match('/\_$/', $column)) {
            $column = preg_replace('/\_$/', '', $column, 1);
        }

        return $column;
    }

    /**
     * Check and clean url also remove duplicated key value pairs.
     *
     * @param  \Illimunate\Http\Request $request
     * @param  boolean                  $removePage
     *
     * @return string
     */
    public static function createPaginationUrl($request, $removePage = false): string
    {
        if (parse_url($request->path() . '?' . http_build_query(($request->all())), PHP_URL_QUERY)) {
            parse_str(parse_url($request->path() . '?' . http_build_query(($request->all())), PHP_URL_QUERY), $output);

            unset($output['_method']);
            unset($output['_token']);

            $uri = $request->path() . '?' . http_build_query($output);

            if ($removePage) {
                return preg_replace('/\?page=[\d]{1,}/', '', '/' . preg_replace('/\?&/', '?', rtrim(preg_replace('/\?page=[\d]{1,}\&page=[\d]{1,/', '', $uri), '&')));
            }

            return '/' . preg_replace('/\?&/', '?', rtrim($uri, '&'));
        }

        return '/' . $request->path();
    }
}
