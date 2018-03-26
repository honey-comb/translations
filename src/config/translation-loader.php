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

return [

    /*
     * Language lines will be fetched by these loaders. You can put any class here that implements
     * the Spatie\TranslationLoader\TranslationLoaders\TranslationLoader-interface.
     */
    'translation_loaders' => [
        Spatie\TranslationLoader\TranslationLoaders\Db::class,
    ],

    /*
     * This is the model used by the Db Translation loader. You can put any model here
     * that extends Spatie\TranslationLoader\LanguageLine.
     */
    'model' => \HoneyComb\Translations\Models\HCFileTranslation::class,

    /*
     * This is the translation manager which overrides the default Laravel `translation.loader`
     */
    'translation_manager' => \HoneyComb\Translations\Services\HCTranslationLoaderManager::class,

    /**
     * Exclude specific groups from Laravel Translation Manager.
     * This is useful if, for example, you want to avoid editing the official Laravel language files.
     *
     * @type array
     *
     * 	[
     *		'pagination',
     *		'reminders',
     *		'validation',
     *	]
     */
    'exclude_groups' => [

    ],

];
