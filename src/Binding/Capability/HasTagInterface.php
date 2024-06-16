<?php

declare(strict_types=1);

namespace Ozzido\Container\Binding\Capability;

interface HasTagInterface
{
    /**
     * Defines binding tag.
     *
     * @param non-empty-string $tag
     */
    public function withTag(string $tag): static;

    /**
     * Defines multiple binding tags.
     *
     * @param list<non-empty-string> $tags
     */
    public function withTags(array $tags): static;

    /**
     * Checks whether a binding has a given tag.
     *
     * @param non-empty-string $tag
     */
    public function hasTag(string $tag): bool;
}
