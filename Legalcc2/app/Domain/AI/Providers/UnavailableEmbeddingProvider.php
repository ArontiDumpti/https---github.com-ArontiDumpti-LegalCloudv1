<?php

namespace App\Domain\AI\Providers;

use App\Domain\AI\Contracts\EmbeddingProvider;
use App\Domain\AI\ProviderNotConfigured;

class UnavailableEmbeddingProvider implements EmbeddingProvider
{
    public function embed(string $text): array
    {
        throw new ProviderNotConfigured('No embedding provider has been configured.');
    }
}
