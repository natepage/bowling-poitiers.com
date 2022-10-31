<?php
declare(strict_types=1);

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity]
#[Vich\Uploadable]
class PostImage extends AbstractEntity
{
    public const SUPPORTED_MIME_TYPES = [
        'image/jpeg',
        'image/png',
    ];

    #[ORM\Column(type: Types::STRING)]
    private ?string $filename = null;

    #[ORM\Column(type: Types::INTEGER)]
    private ?int $filesize = null;

    #[ORM\Column(type: Types::STRING)]
    private ?string $mimeType = null;

    #[ORM\Column(type: Types::STRING)]
    private ?string $originalName = null;

    #[ORM\ManyToOne(targetEntity: Post::class, inversedBy: 'images')]
    private ?Post $post = null;

    #[Vich\UploadableField(
        mapping: 'post_images',
        fileNameProperty: 'filename',
        size: 'filesize',
        mimeType: 'mimeType',
        originalName: 'originalName'
    )]
    private ?File $underlyingFile = null;

    public function __toString(): string
    {
        $toString = $this->filename ?? $this->id ?? '';

        if ($this->filesize !== null) {
            $toString .= ' (' . \number_format($this->filesize / 1024, 2) . ' KB)';
        }

        return $toString;
    }

    /**
     * @return string|null
     */
    public function getMimeType(): ?string
    {
        return $this->mimeType;
    }

    /**
     * @param string|null $mimeType
     * @return PostImage
     */
    public function setMimeType(?string $mimeType): PostImage
    {
        $this->mimeType = $mimeType;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getOriginalName(): ?string
    {
        return $this->originalName;
    }

    /**
     * @param string|null $originalName
     * @return PostImage
     */
    public function setOriginalName(?string $originalName): PostImage
    {
        $this->originalName = $originalName;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getFilename(): ?string
    {
        return $this->filename;
    }

    /**
     * @param string|null $filename
     * @return PostImage
     */
    public function setFilename(?string $filename): PostImage
    {
        $this->filename = $filename;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getFilesize(): ?int
    {
        return $this->filesize;
    }

    /**
     * @param int|null $filesize
     * @return PostImage
     */
    public function setFilesize(?int $filesize): PostImage
    {
        $this->filesize = $filesize;
        return $this;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\File\File|null
     */
    public function getUnderlyingFile(): ?File
    {
        return $this->underlyingFile;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\File\File|null $underlyingFile
     * @return PostImage
     */
    public function setUnderlyingFile(?File $underlyingFile): PostImage
    {
        $this->underlyingFile = $underlyingFile;

        if ($underlyingFile !== null) {
            $this->updateTimestamps();
        }

        return $this;
    }

    /**
     * @return \App\Entity\Post|null
     */
    public function getPost(): ?Post
    {
        return $this->post;
    }

    /**
     * @param \App\Entity\Post|null $post
     * @return PostImage
     */
    public function setPost(?Post $post): PostImage
    {
        $this->post = $post;
        return $this;
    }
}
