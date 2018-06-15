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

Route::domain(config('hc.admin_domain'))
    ->prefix(config('hc.admin_url'))
    ->namespace('Admin')
    ->middleware(['web', 'auth'])
    ->group(function () {

        Route::get('file-translations', 'HCFileTranslationController@index')
            ->name('admin.file.translations.index')
            ->middleware('acl:_file_translations_admin_list');

        Route::prefix('api/file-translations')->group(function () {

            Route::get('/', 'HCFileTranslationController@getListPaginate')
                ->name('admin.api.file.translations')
                ->middleware('acl:_file_translations_admin_list');

            Route::get('options', 'HCFileTranslationController@getOptions')
                ->name('admin.api.file.translations.options');

            Route::delete('/', 'HCFileTranslationController@deleteRecord')
                ->name('admin.api.file.translations.delete')
                ->middleware('acl:_file_translations_admin_delete');

            Route::prefix('{id}')->group(function () {

                Route::get('/', 'HCFileTranslationController@getById')
                    ->name('admin.api.file.translations.single')
                    ->middleware('acl:_file_translations_admin_list');

                Route::put('/', 'HCFileTranslationController@update')
                    ->name('admin.api.file.translations.update')
                    ->middleware('acl:_file_translations_admin_update');
            });
        });
    });
