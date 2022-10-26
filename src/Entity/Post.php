<?php
declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\String\Slugger\AsciiSlugger;

#[ORM\Entity]
class Post extends AbstractEntity
{
    #[ORM\Column(type: 'string')]
    private ?string $title = null;

    #[ORM\Column(type: 'string')]
    private ?string $slug = null;

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
        $this->slug = (new AsciiSlugger())->slug($this->title)->lower()->toString();
    }
}
