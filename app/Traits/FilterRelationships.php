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
| Auto filtering relationships for laravel models
|--------------------------------------------------------------------------
|
| Here is where all filtering of relationships logic happens.
| Current version supports single or array value of filtering one column
| from a relationship.
|
*/

namespace App\Traits;

use Nette\Utils\ReflectionMethod;

trait FilterRelationships
{
    /**
     * Data array that will filter with relationship
     *
     * @var array
     */
    public static $filerRelationshipData = [];

    /**
     * Filter relationships.
     *
     * @param  \Illuminate\Database\Eloquent\Model  &$query
     * @param  \Illuminate\Http\Request             &$request
     *
     * @return void
     */
    public function filterRelationships(&$query, &$request)
    {
        self::checkRequestKeysForRelationships($request);

        if (! isset(self::$filerRelationshipData)) {
            return;
        }

        foreach (self::$filerRelationshipData as $column => $value) {
            $relationship = self::convertToRelationship($column);

            if ($this->checkIfQueryShouldBeStrict($request->is_strict)) {
                $query->whereHas($relationship, function ($query) use ($request, $column, $value) {
                    $query->where(function ($query) use ($column, $value) {
                        if (! is_array($value)) {
                            $query->where($this->convertToColumn($column), 'LIKE', '%' . $value . '%');
                        } else {
                            foreach ($value as $arrayValue) {
                                $query->orWhere($this->convertToColumn($column), 'LIKE', '%' . $arrayValue . '%');
                            }
                        }
                    });
                });
            } else {
                $query->orWhereHas($relationship, function ($query) use ($request, $column, $value) {
                    if (! is_array($value)) {
                        $query->where($this->convertToColumn($column), 'LIKE', '%' . $value . '%');
                    } else {
                        $query->where(function ($query) use ($column, $value) {
                            foreach ($value as $arrayValue) {
                                $query->orWhere($this->convertToColumn($column), 'LIKE', '%' . $arrayValue . '%');
                            }
                        });
                    }
                });
            }
        }
    }

    /**
     * Check request keys for relationships.
     *
     * @param  \Illimunate\Http\Request  &$request
     *
     * @return void
     */
    public function checkRequestKeysForRelationships(&$request)
    {
        foreach (array_filter($request->all(), 'strlen') as $column => $value) {
            $relationship = self::convertToRelationship($column);

            if (
                $this->checkColumns($this->convertToColumn($column))
                && (method_exists($this, $relationship) || str_contains($relationship, '.'))
                && $value !== null
                && $value !== ''
            ) {
                if (str_contains($relationship, '.')) {
                    self::$filerRelationshipData[$column] = $value;
                } else {
                    $reflection = new ReflectionMethod($this, $relationship);

                    if ($reflection->isPublic()) {
                        self::$filerRelationshipData[$column] = $value;
                    }
                }
            }
        }

        if (count(self::$filerRelationshipData) === 0) {
            return false;
        }

        return true;
    }

    /**
     * Check request value for relationship.
     *
     * @param  \Illimunate\Http\Request  &$request
     *
     * @return void
     */
    public function checkRequestValueForRelationship($value)
    {
        $relationship = self::convertToRelationship($value);

        if (
            $this->checkColumns($this->convertToColumn($value))
            && (method_exists($this, $relationship) || str_contains($relationship, '.'))
            && $value !== null
            && $value !== ''
        ) {
            $reflection = new ReflectionMethod($this, $relationship);

            if ($reflection->isPublic() || str_contains($relationship, '.')) {
                return true;
            }
        }

        return false;
    }

    /**
     * Convert key string to model relationship.
     *
     * @param  string  $column
     *
     * @return string
     */
    public function convertToRelationship($column)
    {
        $relationship = strtolower(
            preg_replace(
                '/.+?(?:FromModel)|.+?(?:Model)|.+?(?:fromModel)|.+?(?:model)|.+?(?:_from_)|.+?(?:_from_model_)/',
                '',
                $column
            )
        );

        if (strrpos($relationship, '_') !== false) {
            preg_match_all('/\_./', $relationship, $matches);

            $matches = $matches[0];

            foreach ($matches as $match) {
                $replacement = str_replace('_', '', strtoupper($match));

                $relationship = str_replace($match, $replacement, $relationship);
            }
        }

        if (strrpos($relationship, 'Then') !== false) {
            $relationships = explode('Then', $relationship);

            $relationships = array_map(function ($item) {
                return lcfirst($item);
            }, $relationships);

            $relationship = implode('.', $relationships);
        }

        return $relationship;
    }
}
