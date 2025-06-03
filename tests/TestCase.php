<?php

namespace Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;
use PHPUnit\Framework\MockObject\MockObject;

class TestCase extends BaseTestCase
{
    protected $db;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a mock for mysqli
        $this->db = $this->createMock(\mysqli::class);
        
        // Set up common mock expectations
        $this->db->method('prepare')
            ->willReturn($this->createMock(\mysqli_stmt::class));
            
        $this->db->method('query')
            ->willReturn($this->createMock(\mysqli_result::class));
            
        $this->db->method('insert_id')
            ->willReturn(1);
    }

    protected function tearDown(): void
    {
        $this->db = null;
        parent::tearDown();
    }

    protected function createTestUser($username = 'testuser', $password = 'testpass123')
    {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        // Mock the prepared statement
        $stmt = $this->createMock(\mysqli_stmt::class);
        $stmt->method('bind_param')
            ->willReturn(true);
        $stmt->method('execute')
            ->willReturn(true);
            
        $this->db->method('prepare')
            ->willReturn($stmt);
            
        return 1; // Return a mock user ID
    }

    protected function createTestRecipe($userId, $title = 'Test Recipe')
    {
        // Mock the prepared statement
        $stmt = $this->createMock(\mysqli_stmt::class);
        $stmt->method('bind_param')
            ->willReturn(true);
        $stmt->method('execute')
            ->willReturn(true);
            
        $this->db->method('prepare')
            ->willReturn($stmt);
            
        return 1; // Return a mock recipe ID
    }

    protected function cleanTestData()
    {
        $this->db->query("DELETE FROM recipes");
        $this->db->query("DELETE FROM users");
    }
} 