<?php

namespace Tests\Feature;

use Tests\TestCase;

class GenericControllerGetChangeLanguageTest extends TestCase
{
    /**
     * @test
     *
     * @dataProvider supportedLanguagesProvider
     */
    public function it_redirects_to_the_chosen_locale(string $supportedLang): void
    {
        $this->get("/pt/change-language/{$supportedLang}")
            ->assertRedirect("/{$supportedLang}");
    }

    /** @test */
    public function it_falls_back_to_the_default_locale_for_unknown_languages(): void
    {
        $this->get('/en/change-language/xx')
            ->assertRedirect('/' . config('app.locale'));
    }

    public static function supportedLanguagesProvider(): array
    {
        return [
            'EN' => ['en'],
            'PT' => ['pt'],
            'ES' => ['es'],
        ];
    }
}
