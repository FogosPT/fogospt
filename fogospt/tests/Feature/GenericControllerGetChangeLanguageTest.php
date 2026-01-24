<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class GenericControllerGetChangeLanguageTest extends TestCase
{
    #[Test]
    #[DataProvider('supportedLanguagesProvider')]
    public function it_changes_language_to_a_supported_one(string $supportedLang): void
    {
        $this->get("/change-language/{$supportedLang}")
            ->assertRedirect()
            ->assertCookie('userLocale', $supportedLang, false);
    }

    public static function supportedLanguagesProvider(): array
    {
        return [
            'EN' => ['en'],
            'PT' => ['pt'],
        ];
    }
}
