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

namespace HoneyComb\Translations\Providers;

use HoneyComb\Starter\Providers\HCBaseServiceProvider;
use HoneyComb\Translations\Console\HCImportTranslationsFromFiles;
use HoneyComb\Translations\Repositories\HCFileTranslationRepository;
use HoneyComb\Translations\Services\HCFileTranslationService;

/**
 * Class HCTranslationServiceProvider
 * @package HoneyComb\Translations\Providers
 */
class HCTranslationServiceProvider extends HCBaseServiceProvider
{
    /**
     * @var string
     */
    protected $homeDirectory = __DIR__;

    /**
     * Console commands
     *
     * @var array
     */
    protected $commands = [
        HCImportTranslationsFromFiles::class,
    ];

    /**
     * Controller namespace
     *
     * @var string
     */
    protected $namespace = 'HoneyComb\Translations\Http\Controllers';

    /**
     * Provider name
     *
     * @var string
     */
    protected $packageName = 'HCTranslation';

    /**
     *
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            $this->packagePath('config/translation-loader.php'), 'translation-loader'
        );

        $this->registerRepositories();

        $this->registerServices();
    }

    /**
     *
     */
    private function registerRepositories(): void
    {
        $this->app->singleton(HCFileTranslationRepository::class);
    }

    /**
     *
     */
    private function registerServices(): void
    {
        $this->app->singleton(HCFileTranslationService::class);
    }
}
