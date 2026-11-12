<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicPageMetadataTest extends TestCase
{
    use RefreshDatabase;

    public function test_judul_halaman_hanya_di_escape_sekali(): void
    {
        $this->get('/profil')
            ->assertOk()
            ->assertSee('<title>Profil, Sejarah, Visi &amp; Misi | MTs NU Al Syairiyah</title>', false)
            ->assertDontSee('&amp;amp;', false);
    }

    public function test_structured_data_halaman_utama_adalah_json_yang_valid(): void
    {
        $response = $this->get('/')->assertOk();

        $html = $response->getContent();

        $this->assertMatchesRegularExpression(
            '/<script type="application\/ld\+json">\s*(\{.*?\})\s*<\/script>/s',
            $html,
        );

        preg_match('/<script type="application\/ld\+json">\s*(\{.*?\})\s*<\/script>/s', $html, $matches);

        $schema = json_decode($matches[1], true);

        $this->assertIsArray($schema);
        $this->assertSame('https://schema.org', $schema['@context']);
        $this->assertArrayHasKey('@graph', $schema);
        $this->assertStringNotContainsString('<?php', $matches[1]);
    }
}
