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

namespace HoneyComb\Translations\Console;

use HoneyComb\Translations\Models\HCFileTranslation;
use HoneyComb\Translations\Services\HCFileTranslationService;
use Illuminate\Console\Command;

/**
 * Class HCImportTranslationsFromFiles
 * @package HoneyComb\Translations\Console
 */
class HCImportTranslationsFromFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:translations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scan lang directories and move all translation keys to database. For project translations';

    /**
     * @var HCFileTranslationService
     */
    protected $fileTranslationService;

    /**
     * Create a new command instance.
     *
     * @param HCFileTranslationService $fileTranslationService
     */
    public function __construct(HCFileTranslationService $fileTranslationService)
    {
        parent::__construct();

        $this->fileTranslationService = $fileTranslationService;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $translations = $this->fileTranslationService->parseLanguageFiles();

        $this->importTranslations($translations);
    }

    /**
     * @param array $translations
     */
    private function importTranslations(array $translations): void
    {
        $editedCount = 0;
        $translationCount = 0;

        /** @var \Illuminate\Database\Eloquent\Collection $dbTranslations */
        $dbTranslations = $this->fileTranslationService->getRepository()->all();

        foreach ($translations as $group => $translations) {

            $this->info($group . ' -> ' . count($translations));

            foreach ($translations as $key => $values) {
                /** @var HCFileTranslation $dbTranslation */
                $dbTranslation = $dbTranslations->where('group', $group)
                    ->where('key', $key)
                    ->first();

                if (is_null($dbTranslation)) {
                    $dbTranslation = new HCFileTranslation([
                        'group' => $group,
                        'key' => $key,
                        'text' => [],
                        'edited' => 0,
                    ]);
                }

                if (!$dbTranslation->edited) {
                    if (array_diff($values, $dbTranslation->text)) {
                        foreach ($values as $lang => $translation) {
                            $dbTranslation->setTranslation($lang, $translation);
                        }

                        $dbTranslation->save();
                    }
                } else {
                    $editedCount++;
                }

                $translationCount++;
            }
        }

        $this->comment('Edited count: ' . $editedCount);
        $this->comment('Translations count: ' . $translationCount);
    }
}
