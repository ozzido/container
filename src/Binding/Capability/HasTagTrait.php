<?php

declare(strict_types=1);

namespace Ozzido\Container\Binding\Capability;

trait HasTagTrait
{
    /** @var array<non-empty-string, bool> */
    private array $tags = [];

    /** @inheritdoc */
    public function withTag(string $tag): static
    {
        $this->tags[$tag] = true;

        return $this;
    }

    /** @inheritdoc */
    public function withTags(array $tags): static
    {
        foreach ($tags as $tag) {
            $this->withTag($tag);
        }

        return $this;
    }

    /** @inheritdoc */
    public function hasTag(string $tag): bool
    {
        return isset($this->tags[$tag]);
    }
}
