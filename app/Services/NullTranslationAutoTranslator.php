<?php

namespace App\Services;

use App\Contracts\TranslationAutoTranslator;
use RuntimeException;

class NullTranslationAutoTranslator implements TranslationAutoTranslator
{
    public function isConfigured(): bool
    {
        return false;
    }

    public function translate(string $text, string $sourceLocale, string $targetLocale, array $context = []): string
    {
        throw new RuntimeException('AI translation provider is not configured.');
    }
}
