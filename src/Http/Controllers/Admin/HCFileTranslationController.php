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

namespace HoneyComb\Translations\Http\Controllers\Admin;

use HoneyComb\Core\Http\Controllers\HCBaseController;
use HoneyComb\Core\Http\Controllers\Traits\HCAdminListHeaders;
use HoneyComb\Starter\Helpers\HCFrontendResponse;
use HoneyComb\Translations\Http\Requests\Admin\HCFileTranslationRequest;
use HoneyComb\Translations\Services\HCFileTranslationService;
use Illuminate\Database\Connection;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

/**
 * Class HCFileTranslationController
 * @package HoneyComb\Translations\Http\Controllers\Admin
 */
class HCFileTranslationController extends HCBaseController
{
    use HCAdminListHeaders;

    /**
     * @var HCFileTranslationService
     */
    protected $service;

    /**
     * @var Connection
     */
    protected $connection;

    /**
     * @var HCFrontendResponse
     */
    protected $response;

    /**
     * HCFileTranslationController constructor.
     * @param Connection $connection
     * @param HCFrontendResponse $response
     * @param HCFileTranslationService $service
     */
    public function __construct(Connection $connection, HCFrontendResponse $response, HCFileTranslationService $service)
    {
        $this->connection = $connection;
        $this->response = $response;
        $this->service = $service;
    }

    /**
     * Admin panel page view
     *
     * @return View
     */
    public function index(): View
    {
        $config = [
            'title' => trans('HCTranslation::file_translations.page_title'),
            'url' => route('admin.api.file.translations'),
            'form' => route('admin.api.form-manager', ['file.translations']),
            'headers' => $this->getTableColumns(),
            'actions' => $this->getActions('honey_comb_translations_file_translations'),
            'filters' => $this->getFilters(),
        ];

        return view('HCCore::admin.service.index', ['config' => $config]);
    }

    /**
     * Get admin page table columns settings
     *
     * @return array
     */
    public function getTableColumns(): array
    {
        $columns = [
            'group' => $this->headerText(trans('HCTranslation::file_translations.group')),
            'key' => $this->headerText(trans('HCTranslation::file_translations.key')),
            'text' => $this->headerText(trans('HCTranslation::file_translations.text')),
        ];

        return $columns;
    }

    /**
     * @param string $recordId
     * @return JsonResponse
     */
    public function getById(string $recordId): JsonResponse
    {
        return response()->json(
            $this->service->getRepository()->getRecordById($recordId)
        );
    }

    /**
     * @param HCFileTranslationRequest $request
     * @return JsonResponse
     */
    public function getOptions(HCFileTranslationRequest $request): JsonResponse
    {
        return response()->json(
            $this->service->getRepository()->getOptions($request)
        );
    }

    /**
     * Creating data list
     * @param HCFileTranslationRequest $request
     * @return JsonResponse
     */
    public function getListPaginate(HCFileTranslationRequest $request): JsonResponse
    {
        $translations = $this->service->getRepository()->getListPaginate($request);

        foreach ($translations as &$translation) {
            if (is_array($translation->text)) {
                $text = '';

                foreach ($translation->text as $lang => $value) {
                    $text .= sprintf('%s -> %s |', $lang, $value);
                }

                $translation->text = $text;
            }
        }

        return response()->json($translations);
    }

    /**
     * Update record
     *
     * @param HCFileTranslationRequest $request
     * @param string $translationId
     * @return JsonResponse
     */
    public function update(HCFileTranslationRequest $request, string $translationId): JsonResponse
    {
        $this->service->updateTranslation($request->getTranslations(), $translationId);

        return $this->response->success("Updated");
    }

    /**
     * Soft delete record
     *
     * @param HCFileTranslationRequest $request
     * @return JsonResponse
     * @throws \Throwable
     */
    public function deleteRecord(HCFileTranslationRequest $request): JsonResponse
    {
        $this->connection->beginTransaction();

        try {
            $this->service->getRepository()->deleteRecord($request->getListIds());

            $this->connection->commit();
        } catch (\Throwable $exception) {
            $this->connection->rollBack();

            return $this->response->error($exception->getMessage());
        }

        return $this->response->success('Successfully deleted');
    }

    /**
     * Getting allowed actions for admin view
     *
     * @param string $prefix
     * @param array $except
     * @return array
     */
    protected function getActions(string $prefix, array $except = []): array
    {
        $actions[] = 'search';

        if (!in_array('_update', $except) && auth()->user()->can($prefix . '_update')) {
            $actions[] = 'update';
        }

        if (!in_array('_delete', $except) && auth()->user()->can($prefix . '_delete')) {
            $actions[] = 'delete';
        }

        return $actions;
    }

    /**
     * @return array
     */
    private function getFilters(): array
    {
        return [
            'groups' => [
                'type' => 'dropDownList',
                'options' => $this->service->getRepository()->getGroupsForFilter(),
            ],
        ];
    }
}
