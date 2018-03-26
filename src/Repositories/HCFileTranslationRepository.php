<?php
/**
 * @copyright 2018 interactivesolutions
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
 * Contact InteractiveSolutions:
 * E-mail: info@interactivesolutions.lt
 * http://www.interactivesolutions.lt
 */

declare(strict_types = 1);

namespace HoneyComb\Translations\Repositories;

use HoneyComb\Core\Repositories\Traits\HCQueryBuilderTrait;
use HoneyComb\Starter\Repositories\HCBaseRepository;
use HoneyComb\Translations\Http\Requests\Admin\HCFileTranslationRequest;
use HoneyComb\Translations\Models\HCFileTranslation;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class HCFileTranslationRepository
 * @package HoneyComb\Translations\Repositories
 */
class HCFileTranslationRepository extends HCBaseRepository
{
    use HCQueryBuilderTrait;

    /**
     * @return string
     */
    public function model(): string
    {
        return HCFileTranslation::class;
    }

    /**
     * @param string $translationId
     * @return HCFileTranslation|Model|null
     */
    public function getById(string $translationId): ? HCFileTranslation
    {
        return $this->makeQuery()->find($translationId);
    }

    /**
     * @param HCFileTranslationRequest $request
     * @param array $with
     * @param int $perPage
     * @param array $columns
     * @return LengthAwarePaginator
     */
    public function getListPaginate(
        HCFileTranslationRequest $request,
        array $with = [],
        int $perPage = self::DEFAULT_PER_PAGE,
        array $columns = ['*']
    ): LengthAwarePaginator {

        if ($request->has('per_page')) {
            $perPage = $request->get('per_page');
        }

        return $this->createBuilderQuery($request)->with($with)->select('id', 'group', 'key', 'text')
            ->paginate($perPage, $columns)->appends($request->all());
    }

    /**
     * List search elements
     *
     * @param Builder $query
     * @param string $phrase
     * @return Builder
     */
    protected function searchQuery(Builder $query, string $phrase): Builder
    {
        $fields = $this->getModel()::getFillableFields();

        return $query->where(function (Builder $query) use ($fields, $phrase) {
            return $query->where('group', 'LIKE', '%' . $phrase . '%')
                ->orWhere('key', 'LIKE', '%' . $phrase . '%')
                ->orWhere('text', 'LIKE', '%' . $phrase . '%');
        });
    }

    /**
     * Delete translations
     *
     * @param array $ids
     * @throws \Exception
     */
    public function deleteRecord(array $ids): void
    {
        foreach ($ids as $id) {
            $this->findOrFail($id)->delete();
        }
    }

    /**
     * @param string $translationId
     * @return array
     */
    public function getRecordById(string $translationId): array
    {
        $record = $this->getById($translationId);

        $record->translations = $this->formatForMultiLang($record->text);

        return $record->toArray();
    }

    /**
     * @param array $text
     * @return array
     */
    private function formatForMultiLang(array $text): array
    {
        $response = [];

        foreach ($text as $lang => $translation) {
            $response[] = [
                'language_code' => $lang,
                'translation' => $translation,
            ];
        }

        return $response;
    }

    /**
     * @return array
     */
    public function getGroupsForFilter(): array
    {
        return $this->makeQuery()
            ->select(\DB::raw('MAX(id) as id'), 'group')
            ->groupBy('group')
            ->get()
            ->map(function ($item, $key) {
                return [
                    'id' => 'group=' . $item->group,
                    'label' => $item->group,
                ];
            })
            ->toArray();
    }
}
