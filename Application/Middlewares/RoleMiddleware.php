<?php
declare(strict_types=1);

namespace Application\Middlewares;

use Application\Interfaces\MiddlewareInterface;

final class RoleMiddleware implements MiddlewareInterface
{
    public function __construct(private array $roles = [])
    {
        
    }
    public function handle(): void
    {
        if(!isset($_SESSION['role'])) {
            $_SESSION['error_message'] = "You must be logged to do that.";

            header("Location: /");
        }
        elseif(!in_array($_SESSION['role'], [
            'ROLE_ADMIN'
        ])) {
            $_SESSION['error_message'] = "You don't have privileges to do that.";

            header("Location: /");
        }
    }
}