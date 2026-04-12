<?php
declare(strict_types=1);

namespace Application\model\classes\Entity;

readonly class Product
{
    public function __construct(
        public ?int    $id          = null,
        public ?string $name        = null,
        public ?string $description = null,
        public ?int    $id_category = null,        
        public ?string $image       = null,
        public ?float  $price       = null,
        public ?int    $qty         = null,
    )
    {}

    public function with(array $data): self
    {
        return new self(
            $data['id']             ?? $this->id,
            $data['name']           ?? $this->name,
            $data['description']    ?? $this->description,
            $data['id_category']    ?? $this->id_category,
            $data['image']          ?? $this->image,
            $data['price']          ?? $this->price,
            $data['qty']            ?? $this->qty  
        );
    }
}
