<?php
/**
 * @copyright 2018 innovationbase
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 *
 * Contact InnovationBase:
 * E-mail: hello@innovationbase.eu
 * https://innovationbase.eu
 */

declare(strict_types = 1);

namespace HoneyComb\Translations\Models;

use Cache;
use Spatie\TranslationLoader\LanguageLine;

/**
 * Class HCFileTranslation
 * @package HoneyComb\Translations\Models
 */
class HCFileTranslation extends LanguageLine
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'hc_file_translation';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'group',
        'key',
        'text',
        'edited',
    ];

    /**
     *
     */
    public static function boot(): void
    {
        parent::boot();

        static::saved(function (HCFileTranslation $languageLine) {
            $languageLine->flushGroupCache();
        });

        static::deleted(function (HCFileTranslation $languageLine) {
            $languageLine->flushGroupCache();
        });
    }

    /**
     * Function which gets table name
     *
     * @return mixed
     */
    public static function getTableName(): string
    {
        return with(new static)->getTable();
    }

    /**
     * Function which gets fillable fields array
     *
     * @return array
     */
    public static function getFillableFields(): array
    {
        return with(new static)->getFillable();
    }

    /**
     * @param string $locale
     * @param string $group
     * @return array
     */
    public static function getTranslationsForGroup(string $locale, string $group): array
    {
        return Cache::rememberForever(static::getCacheKey($group, $locale), function () use ($group, $locale) {

            if( ! \Schema::hasTable(static::getTableName())) {
                return [];
            }

            return static::query()
                    ->where('group', $group)
                    ->get()
                    ->reduce(function ($lines, HCFileTranslation $languageLine) use ($locale) {
                        array_set($lines, $languageLine->key, $languageLine->getTranslation($locale));

                        return $lines;
                    }) ?? [];
        });
    }
}
