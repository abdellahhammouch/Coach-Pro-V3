<?php

class UserController
{
    private UserRespository $repo;

    public function __construct()
    {
        $pdo = Database::getConnection();
        $this->repo = new UserRespository($pdo);
    }

    public function index()
    {
        $users = $this->repo->findAll();
        require __DIR__ . '/../Views/users.php';
    }
}
?>