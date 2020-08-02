<?php

namespace Tests\Feature;

use Tests\TestCase;

class GenericControllerGetChangeLanguageTest extends TestCase
{
    /**
     * @test
     *
     * @dataProvider supportedLanguagesProvider
     * @param string $supportedLang
     */
    public function it_changes_language_to_a_supported_one(string $supportedLang): void
    {
        $this->get("/change-language/{$supportedLang}")
            ->assertRedirect()
            ->assertCookie('userLocale', $supportedLang, false);
    }

    public function supportedLanguagesProvider(): array
    {
        return [
            'EN' => ['en'],
            'PT' => ['pt'],
        ];
    }
}
