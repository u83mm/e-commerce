<?php

declare(strict_types=1);

namespace Application\Core;

final class ErrorHandler
{
    public static function handle(\Throwable $th, ?object $controllerInstance = null): void
    {
        $error_msg = [
            'Error' => $th->getMessage()
        ];

        if(isset($_SESSION['role']) && $_SESSION['role'] === "ROLE_ADMIN") {
            $error_msg = [
                'Message'   => $th->getMessage(),
                'Line'      => $th->getLine(),
                'Path'      => $th->getFile(),
                'Trace'     => $th->getTraceAsString()
            ];
        }

        if(isset($controllerInstance)) {
            $controllerInstance->render('error_view.twig', [
                'exception_message' => $error_msg,
                'menus'             => $controllerInstance->showNavLinks()
            ]);

            exit;
        }

        // If fails before build the controller
        echo "<h2>Criticla Error</h2><pre>" . print_r($error_msg, true) . "</pre>";
        exit;
    }
}
