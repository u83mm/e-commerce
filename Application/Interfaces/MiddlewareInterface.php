<?php
declare(strict_types=1);

namespace Application\Interfaces;

interface MiddlewareInterface {
    public function handle(): void;
}