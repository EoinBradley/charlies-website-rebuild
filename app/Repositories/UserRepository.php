<?php

namespace App\Repositories;

use App\Models\Users\Exceptions\InvalidLoginCredentials;
use App\Models\Users\User;
use PDO;

class UserRepository
{
    public function __construct(private readonly PDO $db)
    {
    }

    public function getUserById(int $id): ?User
    {
        $stmt = $this->db->prepare("
            SELECT * FROM users WHERE id = :id
        ");
        $stmt->execute(['id' => $id]);

        if ($stmt->rowCount() === 0) {
            return null;
        }

        $data = $stmt->fetch(PDO::FETCH_OBJ);

        return new User(
            (int) $data->id,
            $data->username,
            $data->email,
            $data->password,
            $data->first_name,
            $data->last_name
        );
    }

    public function getUserByEmail(string $email): ?User
    {
        $stmt = $this->db->prepare("
            SELECT * FROM users WHERE email = :email
        ");
        $stmt->execute(['email' => $email]);

        if ($stmt->rowCount() === 0) {
            return null;
        }

        $data = $stmt->fetch(PDO::FETCH_OBJ);

        return new User(
            (int) $data->id,
            $data->username,
            $data->email,
            $data->password,
            $data->first_name,
            $data->last_name
        );
    }

    /** @throws InvalidLoginCredentials */
    public function authenticateUser(string $email, string $password): User
    {
        $user = $this->getUserByEmail($email);
        if (is_null($user)) {
            throw new InvalidLoginCredentials();
        }

        if (password_verify($password, $user->hashed_password) === false) {
            throw new InvalidLoginCredentials();
        }

        $_SESSION['userId'] = $user->id;

        return $user;
    }
}
