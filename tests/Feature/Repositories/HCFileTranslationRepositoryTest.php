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

namespace Tests\Feature\Repositories;

use HoneyComb\Translations\Models\HCFileTranslation;
use HoneyComb\Translations\Repositories\HCFileTranslationRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Class HCFileTranslationRepositoryTest
 * @package Tests\Feature\Repositories
 */
class HCFileTranslationRepositoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * @group file-translation
     */
    public function it_must_create_singleton_instance(): void
    {
        $this->assertInstanceOf(HCFileTranslationRepository::class, $this->getTestClassInstance());

        $this->assertSame($this->getTestClassInstance(), $this->getTestClassInstance());
    }

    /**
     * @test
     * @group file-translation
     */
    public function is_must_return_model_method_of_bind_model_class(): void
    {
        $this->assertEquals(HCFileTranslation::class, $this->getTestClassInstance()->model());
    }

    /**
     * @test
     * @group file-translation
     */
    public function it_must_return_record_by_id(): void
    {
        // create user via factory
        $expected = factory(HCFileTranslation::class)->create();

        // execute function getById
        $translation = $this->getTestClassInstance()->getById((string)$expected->id);

        // assert created user id is equal to returned from getById response
        $this->assertEquals($expected->id, $translation->id);
    }

    /**
     * @return HCFileTranslationRepository
     */
    private function getTestClassInstance(): HCFileTranslationRepository
    {
        return $this->app->make(HCFileTranslationRepository::class);
    }
}
