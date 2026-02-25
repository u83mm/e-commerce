<?php
declare(strict_types=1);

namespace Application\Middlewares;

use Application\Interfaces\MiddlewareInterface;

final class AuthMiddleware implements MiddlewareInterface
{
    public function handle(): void
    {
        if(!isset($_SESSION['role'])) {
            $_SESSION['error_message'] = "You must be logged to do that.";

            header("Location: /login");
            die;
        }
    }
}
