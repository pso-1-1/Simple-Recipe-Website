<?php

namespace Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    protected $db;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Set up test database connection
        $this->db = new \mysqli(
            getenv('DB_HOST') ?: '127.0.0.1',
            getenv('DB_USER') ?: 'root',
            getenv('DB_PASSWORD') ?: 'rootpassword',
            getenv('DB_NAME') ?: 'recipedb',
            (int)(getenv('DB_PORT') ?: 3306)
        );

        if ($this->db->connect_error) {
            throw new \Exception('Database connection failed: ' . $this->db->connect_error);
        }
    }

    protected function tearDown(): void
    {
        if ($this->db) {
            $this->db->close();
        }
        parent::tearDown();
    }

    protected function createTestUser($username = 'testuser', $password = 'testpass123')
    {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO users (username, password, email) VALUES (?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $email = $username . '@test.com';
        $stmt->bind_param('sss', $username, $hashedPassword, $email);
        $stmt->execute();
        return $this->db->insert_id;
    }

    protected function createTestRecipe($userId, $title = 'Test Recipe')
    {
        $sql = "INSERT INTO recipes (user_id, title, ingredients, instructions) VALUES (?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $ingredients = 'Test ingredients';
        $instructions = 'Test instructions';
        $stmt->bind_param('isss', $userId, $title, $ingredients, $instructions);
        $stmt->execute();
        return $this->db->insert_id;
    }

    protected function cleanTestData()
    {
        $this->db->query("DELETE FROM recipes");
        $this->db->query("DELETE FROM users");
    }
} 