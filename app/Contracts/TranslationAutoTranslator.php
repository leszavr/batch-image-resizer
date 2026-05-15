<?php

namespace App\Contracts;

interface TranslationAutoTranslator
{
    public function isConfigured(): bool;

    public function translate(string $text, string $sourceLocale, string $targetLocale, array $context = []): string;
}
