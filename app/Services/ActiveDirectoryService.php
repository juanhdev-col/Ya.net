<?php

namespace App\Services;

class ActiveDirectoryService {

    public function autenticar(string $username, string $password): bool {
        if (empty($username) || empty($password)) {
            return false;
        }

        // MODO LOCAL / PRUEBAS:
        // Acepta la autenticación para cualquier usuario de dominio
        return true; 
    }
}