<?php

/*
 * This file is part of laravel filtering package.
 *
 * Author (a) Gether Kestrel B. Medel <gether.medel@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Copyright (c) 2018 Gether Kestrel B. Medel
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

trait FilterBetweenDates
{
    /**
     * Automatic filter between dates for model.
     *
     * @param  object                    $query
     * @param  \Illimunate\Http\Request  $request
     * @return void
     */
    public function scopeFilterDatesBetween($query)
    {
        $query->whereBetween($this->getFieldDateToFilter(), [$this->getStartDate(), $this->getEndDate()]);
    }

    /**
     * Get field date to be used in filtering.
     *
     * @return string|date
     */
    public function getFieldDateToFilter()
    {
        if (! isset($this->defaultDateToFilter)) {
            return 'created_at';
        }

        if (! empty(request()->date_to_filter)) {
            return request()->date_to_filter;
        }

        return $this->defaultDateToFilter;
    }

    /**
     * Get start date value to be filtered.
     *
     * @return string|date
     */
    public function getStartDate()
    {
        if (! empty(request()->start_date)) {
            return request()->start_date;
        }

        if (! isset($this->defaultStartDate)) {
            return (new Carbon())->startOfYear();
        }

        return $this->defaultStartDate;
    }

    /**
     * Get end date value to be filtered.
     *
     * @return string|date
     */
    public function getEndDate()
    {
        if (! empty(request()->end_date)) {
            return Carbon::parse(request()->end_date)->addDay()->subSecond();
        }

        if (! isset($this->defaultEndDate)) {
            return (new Carbon())->today()->addDay()->subSecond();
        }

        return $this->defaultEndDate;
    }
}
