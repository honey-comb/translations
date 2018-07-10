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

namespace HoneyComb\Translations\Services;

use HoneyComb\Translations\Models\HCFileTranslation;
use HoneyComb\Translations\Repositories\HCFileTranslationRepository;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\Application;
use Illuminate\Translation\FileLoader;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Class HCFileTranslationService
 * @package HoneyComb\Translations\Services
 */
class HCFileTranslationService
{
    /**
     *
     */
    const JSON_GROUP = '_json';

    /**
     * @var HCFileTranslationRepository
     */
    protected $repository;

    /**
     * @var Application
     */
    protected $app;

    /**
     * @var Filesystem
     */
    protected $files;

    /**
     * @var FileLoader
     */
    protected $loader;

    /**
     * @var array
     */
    protected $translations = [];

    /**
     * HCFileTranslationService constructor.
     * @param HCFileTranslationRepository $repository
     * @param Application $app
     * @param Filesystem $files
     */
    public function __construct(HCFileTranslationRepository $repository, Application $app, Filesystem $files)
    {
        $this->repository = $repository;
        $this->app = $app;
        $this->files = $files;

//        $this->loader = new FileLoader($files, $this->app->langPath());
        $this->loader = app('translation.loader');
    }

    /**
     * @return HCFileTranslationRepository
     */
    public function getRepository(): HCFileTranslationRepository
    {
        return $this->repository;
    }

    /**
     * @param array $items
     * @param string $translationId
     */
    public function updateTranslation(array $items, string $translationId): void
    {
        /** @var HCFileTranslation $translation */
        $translation = $this->getRepository()->findOrFail($translationId);

        foreach ($items as $item) {
            $text = $item['translation'] ?? "";

            $translation->setTranslation($item['language_code'], $text);
        }

        $translation->edited = true;
        $translation->save();
    }

    /**
     *
     * @link https://github.com/barryvdh/laravel-translation-manager/blob/master/src/Manager.php
     * @return array
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function parseLanguageFiles(): array
    {
        $this->parseTranslations();

        $this->parseJsonTranslations();

        return $this->translations;
    }

    /**
     * @param $key
     * @param $value
     * @param $locale
     * @param $group
     * @return void
     */
    public function formatTranslation($key, $value, $locale, $group): void
    {
        // process only string values
        if (is_array($value)) {
            info('formatTranslation array return', compact('key', 'value', 'locale', 'group'));

            return;
        }

        $value = (string)$value;

        $this->translations[$group][$key][$locale] = $value;
    }

    /**
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    private function parseTranslations(): void
    {
        foreach ($this->files->directories($this->app['path.lang']) as $key => $langPath) {

            if (ends_with($langPath, 'vendor')) {

                foreach ($this->files->directories($langPath) as $package) {
                    $packageName = basename($package);

                    foreach ($this->files->directories($package) as $items) {
                        $this->parseFromFolder($items, $packageName);
                    }
                }
            }
            else {
                $this->parseFromFolder($langPath);
            }
        }
    }

    /**
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    private function parseJsonTranslations(): void
    {
        foreach ($this->files->files($this->app['path.lang']) as $jsonTranslationFile) {
            if (strpos($jsonTranslationFile, '.json') === false) {
                continue;
            }

            $locale = basename($jsonTranslationFile, '.json');

            $group = self::JSON_GROUP;

            // Retrieves JSON entries of the given locale only
            $translations = $this->loader->load($locale, '*', '*');

            if ($translations && is_array($translations)) {
                foreach ($translations as $key => $value) {
                    $this->formatTranslation($key, $value, $locale, $group);
                }
            }
        }
    }

    /**
     * @param string $langPath
     * @param null $packageName
     * @return void
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    private function parseFromFolder(string $langPath, $packageName = null): void
    {
        $locale = basename($langPath);

        /** @var SplFileInfo $file */
        foreach ($this->files->allfiles($langPath) as $file) {

            $group = $file->getBasename('.php');

            if (in_array($group, config('translation-loader.exclude_groups'))) {
                continue;
            }

            $subLangPath = str_replace($langPath . DIRECTORY_SEPARATOR, "", $file->getPath());
            $subLangPath = str_replace(DIRECTORY_SEPARATOR, "/", $subLangPath);
            $langPath = str_replace(DIRECTORY_SEPARATOR, "/", $langPath);

            if ($subLangPath != $langPath && is_null($packageName)) {
                $group = $subLangPath . '/' . $group;
            }

            $translations = $this->loader->load($locale, $group, $packageName);

            if ($translations && is_array($translations)) {
                if ($packageName) {
                    $group = $packageName . '::' . $group;
                }

                foreach (array_dot($translations) as $key => $value) {
                    $this->formatTranslation($key, $value, $locale, $group);
                }
            }
        }
    }
}
