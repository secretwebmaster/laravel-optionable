<?php

namespace Secretwebmaster\LaravelOptionable\Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use Secretwebmaster\LaravelOptionable\Models\Option;
use Secretwebmaster\LaravelOptionable\Tests\Models\TestPage;
use Secretwebmaster\LaravelOptionable\Tests\TestCase;

class HasOptionsTest extends TestCase
{
    #[Test]
    public function it_can_store_and_read_scoped_and_grouped_options(): void
    {
        $page = TestPage::create(['title' => 'Landing']);

        $page->setOption('headline', 'Hello Laravel 13', 'theme', 'hero');

        $this->assertSame('Hello Laravel 13', $page->fresh()->getOption('headline', 'theme', 'hero'));
        $this->assertSame(1, $page->fresh()->getOptions('theme', 'hero')->count());
        $this->assertDatabaseHas('options', [
            'optionable_type' => TestPage::class,
            'optionable_id' => $page->id,
            'scope' => 'theme',
            'group' => 'hero',
            'key' => 'headline',
        ]);
    }

    #[Test]
    public function it_supports_nested_json_repeatable_rows_and_fallbacks(): void
    {
        $page = TestPage::create(['title' => 'Landing']);

        $page->setOption('hero', [
            'title' => 'Welcome',
            'cta' => ['label' => 'Start'],
        ], 'theme');
        $page->setOption('image', '/a.jpg', 'theme', 'gallery', 0);
        $page->setOption('image', '/b.jpg', 'theme', 'gallery', 1);

        $freshPage = $page->fresh();

        $this->assertSame('Start', $freshPage->getOption('hero.cta.label', 'theme'));
        $this->assertSame('/b.jpg', $freshPage->getOption('image.1', 'theme', 'gallery'));
        $this->assertSame('fallback-value', $freshPage->getOption('missing', 'theme', null, 'fallback-value'));
    }

    #[Test]
    public function it_supports_legacy_snake_case_method_aliases(): void
    {
        $page = TestPage::create(['title' => 'Legacy']);

        $page->set_option('subtitle', 'Old API still works', 'theme');

        $this->assertSame('Old API still works', $page->fresh()->get_option('subtitle', 'theme'));

        $deleted = $page->delete_option('subtitle', 'theme');

        $this->assertSame(1, $deleted);
        $this->assertNull($page->fresh()->get_option('subtitle', 'theme'));
    }

    #[Test]
    public function it_clears_existing_rows_when_setting_scope_batches(): void
    {
        $page = TestPage::create(['title' => 'Batch']);

        $page->setOption('headline', 'Old headline', 'theme', 'hero');
        $page->setOptions('theme', 'hero', [
            ['key' => 'headline', 'value' => 'New headline'],
            ['key' => 'button_text', 'value' => 'Read more'],
        ]);

        $freshPage = $page->fresh();
        $rows = $freshPage->getOptions('theme', 'hero');

        $this->assertCount(2, $rows);
        $this->assertSame('New headline', $freshPage->getOption('headline', 'theme', 'hero'));
        $this->assertSame('Read more', $freshPage->getOption('button_text', 'theme', 'hero'));
        $this->assertSame(2, Option::query()->count());
    }
}
