<?php

declare(strict_types=1);

namespace Application\model\classes\interfaces;

interface ValidateInterface
{
    public function test_input(int|string|float|null $data): int|string|float|null;
    public function validate_email(string $email): bool;
    public function validate_form(array $fields): bool;
    public function get_msg(): string;
    public function csrf_token(): string;
    public function validate_csrf_token(): bool;
}