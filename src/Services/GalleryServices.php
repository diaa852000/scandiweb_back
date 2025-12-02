<?php

namespace App\Services;

use App\Entities\Gallery;
use App\Entities\Product;
use App\Repository\GalleryRepository;
use App\Services\BaseServices;
use GraphQL\Error\UserError;

class GalleryServices extends BaseServices
{
    private GalleryRepository $galleryRepository;

    public function __construct(GalleryRepository $galleryRepository)
    {
        $this->galleryRepository = $galleryRepository;
    }

    public function create(array $data): Gallery
    {
        $gallery = new Gallery();
        $gallery->image_url = $data['image_url'];
        $gallery->product = $data['product'];

        $this->galleryRepository->create($gallery);
        return $gallery;
    }

    public function update(int|string $id, array $data): Gallery
    {
        $gallery = $this->galleryRepository->findById($id);
        if (!$gallery) {
            throw new UserError("Gallery with id '{$id}' not found.");
        }

        if (isset($data['image_url'])) {
            $gallery->image_url = $data['image_url'];
        }
        if (isset($data['product'])) {
            $gallery->product = $data['product'];
        }

        $this->galleryRepository->update($gallery);
        return $gallery;
    }

    public function delete(int|string $id): bool
    {
        $gallery = $this->galleryRepository->findById($id);
        if (!$gallery) {
            return false;
        }

        return $this->galleryRepository->delete($gallery);
    }

    public function findById(int|string $id): ?object
    {
        return $this->galleryRepository->findById($id);
    }

    public function findAll(): array
    {
        return $this->galleryRepository->findAll();
    }

    public function addGalleryItem(Product $product, string $imageUrl): Gallery
    {
        $gallery = new Gallery();
        $gallery->image_url = $imageUrl;
        $gallery->product = $product;

        $product->addGalleryItem($gallery);

        $this->galleryRepository->create($gallery);
        return $gallery;
    }
}