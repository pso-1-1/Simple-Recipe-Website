<?php

namespace Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;
use PHPUnit\Framework\MockObject\MockObject;

class TestCase extends BaseTestCase
{
    protected $db;
    protected $lastInsertId = 1;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a mock for mysqli with a custom class
        $this->db = $this->getMockBuilder(\mysqli::class)
            ->disableOriginalConstructor()
            ->addMethods(['getInsertId'])
            ->getMock();
        
        // Set up common mock expectations
        $this->db->method('prepare')
            ->willReturn($this->createMock(\mysqli_stmt::class));
            
        $this->db->method('query')
            ->willReturn($this->createMock(\mysqli_result::class));
            
        // Set up insert_id handling
        $this->db->method('getInsertId')
            ->willReturnCallback(function() {
                return $this->lastInsertId;
            });
    }

    protected function tearDown(): void
    {
        $this->db = null;
        $this->lastInsertId = 1;
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
            
        return $this->lastInsertId++;
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
            
        return $this->lastInsertId++;
    }

    protected function cleanTestData()
    {
        // No need to clean data when using mocks
    }
} 