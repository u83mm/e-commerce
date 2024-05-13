<?php
    declare(strict_types=1);

    namespace Application\Repository;

    use Application\model\User;
    use model\classes\Query;

    class UserRepository extends Query
    {
        public function save(User $user): void
        {
            $this->insertInto('users', [
                'user_name' => $user->getUserName(),
                'email'     => $user->getEmail(),
                'password'  => $user->getPassword(),
            ]);
        }
    }    
?>