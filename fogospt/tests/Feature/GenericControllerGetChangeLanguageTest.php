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
            ->assertSessionHas('userLocale', $supportedLang);
    }

    public function supportedLanguagesProvider(): array
    {
        return [
            'EN' => ['en'],
            'PT' => ['pt'],
        ];
    }
}
