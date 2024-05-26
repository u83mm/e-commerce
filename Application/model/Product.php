<?php
    declare(strict_types=1);

    namespace Application\model;

    class Product
    {
        private ?int    $id          = null;
        private ?string $name        = null;
        private ?string $description = null;
        private ?int    $id_category = null;        
        private ?string $image       = null;
        private ?float  $price       = null;
        private ?int    $qty         = null;
        public function __construct(
            private array $fields = []
        )
        {
            if(!empty($this->fields)) {
                $this->setProduct($this->fields);
            }
        }

        public function setProduct(array $fields): self
        {
            if(!empty($fields)) {
                foreach($fields as $key => $value) {
                    $method = "set" . ucfirst($key);
                    if(method_exists($this, $method)) {
                        $this->$method($value);
                    }
                }
            }
            return $this;
        }

        public function setId(int $id): self
        {
            $this->id = $id;
            return $this;
        }

        public function setName(string $name): self
        {
            $this->name = $name;
            return $this;
        }

        public function setDescription(string $description): self
        {
            $this->description = $description;
            return $this;
        }

        public function setId_category(int $id_category): self
        {
            $this->id_category = $id_category;
            return $this;
        }

        public function setPrice(float|string $price): self
        {
            $this->price = floatval($price);
            return $this;
        }

        public function setImage(string $image): self
        {
            $this->image = $image;
            return $this;
        }

        public function setQty(int|string $qty): self
        {
            $this->qty = intval($qty);
            return $this;
        }

        public function getId(): int
        {
            return $this->id;
        }

        public function getName(): string
        {
            return $this->name;
        }

        public function getDescription(): string
        {
            return $this->description;
        }

        public function getIdCategory(): int
        {
            return $this->id_category;
        }

        public function getPrice(): float
        {
            return $this->price;
        }

        public function getImage(): string
        {
            return $this->image;
        }

        public function getQty(): int
        {
            return $this->qty;
        }
    }
?>