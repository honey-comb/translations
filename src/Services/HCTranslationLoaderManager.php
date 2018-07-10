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

namespace HoneyComb\Translations\Services;

use Illuminate\Translation\FileLoader;
use Spatie\TranslationLoader\TranslationLoaders\TranslationLoader;

/**
 * Class HCTranslationLoaderManager
 * @package HoneyComb\Services
 */
class HCTranslationLoaderManager extends FileLoader
{
    /**
     * Load the messages for the given locale.
     *
     * @param string $locale
     * @param string $group
     * @param string $namespace
     * @return array
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function load($locale, $group, $namespace = null): array
    {
        $fileTranslations = parent::load($locale, $group, $namespace);
        
        if (in_array($group, config('translation-loader.exclude_groups'))) {
            return $fileTranslations;
        }

        if (!is_null($namespace) && $namespace !== '*') {
            // set group name with package namespace. We expect here that $namespace == packageName
            $group = $namespace . '::' . $group;
        }

        $loaderTranslations = $this->getTranslationsForTranslationLoaders($locale, $group);

        return array_replace_recursive($fileTranslations, $loaderTranslations);
    }

    /**
     * @param string $locale
     * @param string $group
     * @return array
     */
    protected function getTranslationsForTranslationLoaders(string $locale, string $group): array
    {
        return collect(config('translation-loader.translation_loaders'))
            ->map(function (string $className) {
                return app($className);
            })
            ->mapWithKeys(function (TranslationLoader $translationLoader) use ($locale, $group) {
                return $translationLoader->loadTranslations($locale, $group);
            })
            ->toArray();
    }
}
