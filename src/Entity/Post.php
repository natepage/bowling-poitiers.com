<?php
declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class Post extends AbstractEntity
{
    public const ALL_STATUSES = [
        self::STATUS_DRAFT,
        self::STATUS_PUBLISHED,
    ];

    public const STATUS_DRAFT = 'draft';

    public const STATUS_PUBLISHED = 'published';

    #[ORM\Column(type: Types::TEXT)]
    private ?string $content = null;

    #[ORM\Column(type: Types::STRING)]
    private ?string $description = null;

    #[Assert\Valid]
    #[ORM\OneToMany(mappedBy: 'post', targetEntity: PostImage::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private ?Collection $images = null;

    #[ORM\Column(type: Types::STRING)]
    private ?string $title = null;

    #[ORM\Column(type: Types::STRING)]
    private ?string $slug = null;

    #[ORM\Column(type: Types::STRING, length: 50)]
    private string $status = self::STATUS_DRAFT;

    public function __construct()
    {
        $this->images = new ArrayCollection();
    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getImages(): Collection
    {
        return $this->images ??= new ArrayCollection();
    }

    public function addImage(PostImage $image): self
    {
        $images = $this->getImages();

        if ($images->contains($image) === false) {
            $images->add($image);

            $image->setPost($this);
        }

        return $this;
    }

    public function removeImage(PostImage $image): self
    {
        $images = $this->getImages();

        if ($images->contains($image) === true) {
            $images->removeElement($image);

            $image->setPost(null);
        }

        return $this;
    }

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

    /**
     * @return string|null
     */
    public function getContent(): ?string
    {
        return $this->content;
    }

    /**
     * @param string|null $content
     * @return Post
     */
    public function setContent(?string $content): Post
    {
        $this->content = $content;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string|null $description
     * @return Post
     */
    public function setDescription(?string $description): Post
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @param string $status
     * @return Post
     */
    public function setStatus(string $status): Post
    {
        $this->status = $status;
        return $this;
    }
}
