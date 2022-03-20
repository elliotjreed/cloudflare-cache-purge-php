<?php

declare(strict_types=1);

namespace ElliotJReed\Entity;

class Result
{
    private ?string $id = null;
    private ?string $name = null;

    /**
     * @return string|null The ID of the returned Cloudflare response
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * @param string|null $id The ID of the returned Cloudflare response
     *
     * @return $this
     */
    public function setId(?string $id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string|null The name of the returned Cloudflare response
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name The name of the returned Cloudflare response
     *
     * @return $this
     */
    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }
}
