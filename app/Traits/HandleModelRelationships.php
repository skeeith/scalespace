<?php

/*
 * This file is part of laravel handle model relationships package.
 *
 * Author (a) Gether Kestrel B. Medel <gether.medel@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Copyright (c) 2024 Gether Kestrel B. Medel
 */

/*
|--------------------------------------------------------------------------
| Auto handling of laravel model relationships
|--------------------------------------------------------------------------
|
*/

namespace App\Traits;

trait HandleModelRelationships
{
    /**
     * Defined relationships of the model.
     *
     * @var array
     */
    private $definedModelRelationships = [];

    /**
     * Automatically handle model relationships.
     *
     * @param  Illuminate\Database\Eloquent\Builder &$query
     * @param  \Illuminate\Http\Request             &$request
     *
     * @return void
     */
    public function scopeHandleRelationships(&$query, &$request, $procedure = null): void
    {
        // self::getAllDefinedModelRelationships();
        $reflector = new \ReflectionClass(get_called_class());

        $this->definedModelRelationships = collect($reflector->getMethods())->filter(
            fn ($method) => ! empty($method->getReturnType())
            && str_contains(
                $method->getReturnType(),
                'Illuminate\Database\Eloquent\Relations'
            )
        )->all();

        if ($request->isMethod('post')) {
            $procedure = 'store';
        } elseif ($request->isMethod('put') || $request->isMethod('patch')) {
            $procedure = 'update';
        } elseif ($request->isMethod('delete')) {
            $procedure = 'delete';
        }

        foreach ($request->all() as $requestFieldKey => $requestValue) {
            $convertedModelRelationship = self::convertRequestFieldToModelRelationship($requestFieldKey);

            if (
                ! in_array($requestFieldKey, $this->fillable)
                && in_array($convertedModelRelationship, collect($this->definedModelRelationships)->pluck('name')->all())
            ) {
                $defineRelationship = collect($this->definedModelRelationships)->firstWhere('name', $convertedModelRelationship);
                // dump(['$defineRelationship' => $defineRelationship]);
                // dump($this->{$convertedModelRelationship});
                // dump($requestValue);
                // dump($defineRelationship);

                // Has One
                if ($defineRelationship->getReturnType()->getName() === 'Illuminate\Database\Eloquent\Relations\HasOne') {
                    dump('handle has one relationships');
                }

                // Has Many
                if ($defineRelationship->getReturnType()->getName() === 'Illuminate\Database\Eloquent\Relations\HasMany') {
                    dump('handle has many relationships');

                    // dump($this->{$convertedModelRelationship}());
                    // dump($this->{$convertedModelRelationship}()->getForeignKeyName());

                    // Create method
                    if ($procedure === 'store') {
                        $this->{$convertedModelRelationship}()->createMany($request->{$requestFieldKey});
                    }

                    // Update method
                    if ($procedure === 'update') {
                        $modelRelationshipsCount    = count($this->{$convertedModelRelationship});
                        $requestRelationshipsCount  = count($request->{$requestFieldKey});
                        $requestRelationshipsValues = collect($request->{$requestFieldKey});

                        foreach ($request[$requestFieldKey] as $requestFieldValue) {
                            // build array to be inserted
                            $data = [];
                            $fillable = $this->{$convertedModelRelationship}()->getRelated()->fillable;

                            foreach ($fillable as $relatedColumn) {
                                if ((array_key_exists('id', $requestFieldValue))) {
                                    $data['id'] = $requestFieldValue['id'];
                                    $data['created_at'] = $requestFieldValue['created_at'];
                                }

                                if (in_array($relatedColumn, array_keys($requestFieldValue))) {
                                    $data[$relatedColumn] = $requestFieldValue[$relatedColumn];
                                }
                            }

                            if (
                                array_key_exists($this->{$convertedModelRelationship}()->getRelated()->primaryKey, $requestFieldValue)
                                && (array_key_exists('created_at', $requestFieldValue) && ! is_null($data['created_at']))
                                && (array_key_exists('id', $requestFieldValue) && ! is_null($data['id']))
                            ) { // Update
                                unset($data['created_at']);

                                $this->{$convertedModelRelationship}()->upsert($data, ['id'], $fillable);
                            } elseif (
                                ! array_key_exists($this->{$convertedModelRelationship}()->getRelated()->primaryKey, $requestFieldValue)
                                && ! array_key_exists('created_at', $requestFieldValue)
                                && ! array_key_exists('id', $requestFieldValue)
                            ) { // Create
                                unset($data['id']);
                                unset($data['created_at']);

                                $this->{$convertedModelRelationship}()->create($data);
                            }
                        }

                        // Delete
                        if ($requestRelationshipsCount == 0 && $modelRelationshipsCount != 0) {
                            $this->{$convertedModelRelationship}()->delete();
                        } else {
                            $relatedForeignKey = $this->{$convertedModelRelationship}()->getForeignKeyName();

                            foreach ($this->{$convertedModelRelationship} as $modelRelationship) {
                                $requestRelationshipIndex = $requestRelationshipsValues->search(function ($requestRelationshipsValue, $key) use ($modelRelationship, $relatedForeignKey) {
                                    if (array_key_exists('id', $requestRelationshipsValue)) {
                                        return $requestRelationshipsValue['id'] === $modelRelationship['id'];
                                    }
                                });

                                if (
                                    $requestRelationshipIndex === false
                                    && is_bool($requestRelationshipIndex)
                                    && ! is_int($requestRelationshipIndex)
                                ) {
                                    $modelRelationship->forceDelete();
                                }
                            }
                        }
                    }
                }

                // Belongs To
                if ($defineRelationship->getReturnType()->getName() === 'Illuminate\Database\Eloquent\Relations\BelongsTo') {
                    dump('handle belongs to relationships');
                }

                // Has One Through
                if ($defineRelationship->getReturnType()->getName() === 'Illuminate\Database\Eloquent\Relations\HasOneThrough') {
                    dump('handle has one through relationships');
                }

                // Has Many Through
                if ($defineRelationship->getReturnType()->getName() === 'Illuminate\Database\Eloquent\Relations\HasManyThrough') {
                    dump('handle has many through relationships');
                }

                // Belongs To Many
                if ($defineRelationship->getReturnType()->getName() === 'Illuminate\Database\Eloquent\Relations\BelongsToMany') {
                    dump('handle belongs to many relationships');
                    // dump($this->{$convertedModelRelationship}->relations['pivot']['fillable']);

                    // To do - implement DB::transaction

                    // continue;
                    // dump($this->{$field}());
                    // dump($this->{$field}()->getForeignPivotKeyName());
                    // dump($this->{$field}()->getRelatedPivotKeyName());
                    // dump($this->{$field}()->getPivotColumns());

                    if ($procedure === 'store') { // This is done
                        // dump('Store Procedure');
                        foreach ($request[$field] as $requestObject) {
                            // build array to be inserted
                            $data = [];

                            foreach ($this->{$field}()->getPivotColumns() as $pivotColumn) {
                                if (in_array($pivotColumn, array_keys($requestObject))) {
                                    // dump($requestObject);
                                    $data[$pivotColumn] = $requestObject[$pivotColumn];
                                }
                            }

                            if (count($data) === 0) { // Attach only
                                $this->{$field}()->attach($requestObject[$this->{$field}()->getRelatedPivotKeyName()]);
                            } else { // Attach with extra fields
                                $this->{$field}()->attach($requestObject[$this->{$field}()->getRelatedPivotKeyName()], $data);
                            }
                        }
                    } elseif ($procedure === 'update') { // To do
                        // dump('Update Procedure');
                        $modelRelationshipsCount   = count($this->{$field});
                        $requestRelationshipsCount = count($request->{$field});
                        $requestRelationships      = collect($request->{$field});

                        // dump($modelRelationshipsCount);

                        // Attach only

                        // Attach with extra fields
                        foreach ($request[$field] as $requestObject) {
                            // build array to be inserted
                            $data = [];

                            foreach ($this->{$field}()->getPivotColumns() as $pivotColumn) {
                                if (in_array($pivotColumn, array_keys($requestObject))) {
                                    // dump($requestObject);
                                    $data[$pivotColumn] = $requestObject[$pivotColumn];
                                }
                            }

                            // Update or create
                            // dump($this->{$field}()->getRelatedPivotKeyName());
                            // dump($requestObject);

                            if (
                                ! array_key_exists('created_at', $requestObject)
                                && ! array_key_exists('updated_at', $requestObject)
                            ) { // Create // To do should be able to create without created_at and updated_at fields
                                if (count($data) === 0) {
                                    $this->{$field}()->attach($requestObject[$this->{$field}()->getRelatedPivotKeyName()]);
                                } else {
                                    $this->{$field}()->attach($requestObject[$this->{$field}()->getRelatedPivotKeyName()], $data);
                                }
                            } elseif (array_key_exists($this->{$field}()->getRelatedPivotKeyName(), $requestObject)) { // Update
                                if (count($data) === 0) {
                                    $this->{$field}()->updateExistingPivot($requestObject[$this->{$field}()->getRelatedPivotKeyName()]);
                                } else {
                                    $this->{$field}()->updateExistingPivot($requestObject[$this->{$field}()->getRelatedPivotKeyName()], $data);
                                }
                            }
                        }

                        // Delete
                        // dump('Checking DELETE');
                        if ($requestRelationshipsCount == 0 && $modelRelationshipsCount != 0) {
                            // dump('DELETING ALL');
                            foreach ($this->{$field} as $modelRelationship) {
                                if (! $modelRelationship['pivot']->forceDelete()) {
                                    // DB::rollBack();

                                    // return false;
                                }
                            }
                        } else {
                            $relatedPivotKeyName = $this->{$field}()->getRelatedPivotKeyName();
                            // dump($relatedPivotKeyName);

                            foreach ($this->{$field} as $modelRelationship) {
                                // dump($modelRelationship['pivot']);
                                $requestRelationshipIndex = $requestRelationships->search(function ($requestRelationship, $key) use ($modelRelationship, $relatedPivotKeyName) {
                                    // dump($requestRelationship);
                                    // dump($requestRelationship[$relatedPivotKeyName]);
                                    return $requestRelationship[$relatedPivotKeyName] == $modelRelationship['id'];
                                });

                                if ($requestRelationshipIndex === false && ! is_int($requestRelationshipIndex)) {
                                    dump('DELETING INDIVIDUAL');
                                    if (! array_key_exists('deleted_at', $modelRelationship['pivot'])) {
                                        dump('Do soft delete');
                                    }

                                    if (! $modelRelationship['pivot']->forceDelete()) {
                                        // DB::rollBack();

                                        // return false;
                                    }
                                }
                            }
                        }
                    }
                }

                //
            }
        }
    }

    /**
     * Check for relationships in request.
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return void
     */
    private function checkRelationships($request): void
    {
        //
    }

    private function convertRequestFieldToModelRelationship($field)
    {
        if (str_contains($field, '_')) {
            $underscorePlacement = strpos($field, '_');
            $replacementString = substr($field, $underscorePlacement + 1, 1);
            $field = str_replace('_' . $replacementString, strtoupper($replacementString), $field);

            return $field;
        }

        return $field;
    }

    public function getAllDefinedModelRelationships()
    {
        $reflector = new \ReflectionClass(get_called_class());

        $this->definedModelRelationships = collect($reflector->getMethods())->filter(
            fn ($method) => ! empty($method->getReturnType())
            && str_contains(
                $method->getReturnType(),
                'Illuminate\Database\Eloquent\Relations'
            )
        )->all();
    }
}
