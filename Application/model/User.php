<?php
    declare(strict_types=1);

    namespace Application\model;

    class User
    {
        private ?string $user_name  = null;
        private ?string $email      = null;
        private ?string $password   = null;
        private ?int    $role       = null;

        public function __construct(            
            private array $fields                       
        )
        {
            if(!empty($fields)) {
                foreach($fields as $key => $value) {
                    $method = "set" . ucfirst($key);
                    if(method_exists($this, $method)) {
                        $this->$method($value);
                    }
                }
            }
        }

        
        public function setUserName(string $user_name): self
        {
            $this->user_name = $user_name;
            return $this;   
        }

        public function setEmail(string $email): self
        {
            $this->email = $email;
            return $this;   
        }

        public function setPassword(string $password): self
        {
            $this->password = $password;
            return $this;   
        }

        public function setRole(int $role): self
        {
            $this->role = $role;
            return $this;   
        }

        public function getUserName(): string
        {
            return $this->user_name;
        }

        public function getEmail(): string
        {
            return $this->email;
        }

        public function getPassword(): string
        {
            return $this->password;
        }

        public function getRole(): int
        {
            return $this->role;
        }
    }
?>