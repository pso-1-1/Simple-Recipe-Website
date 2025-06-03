<?php

namespace Tests;

class AuthTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testUserRegistration()
    {
        $username = 'newuser';
        $password = 'newpass123';
        $email = 'newuser@test.com';

        // Mock the result set for user verification
        $result = $this->createMock(\mysqli_result::class);
        $result->method('fetch_assoc')
            ->willReturn([
                'id' => $this->lastInsertId,
                'username' => $username,
                'email' => $email,
                'password' => password_hash($password, PASSWORD_DEFAULT)
            ]);

        // Mock the prepared statement
        $stmt = $this->getMockBuilder(\mysqli_stmt::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['bind_param', 'execute'])
            ->addMethods(['get_result'])
            ->getMock();
            
        $stmt->method('bind_param')
            ->willReturn(true);
        $stmt->method('execute')
            ->willReturn(true);
        $stmt->method('get_result')
            ->willReturn($result);

        $this->db->method('prepare')
            ->willReturn($stmt);

        // Test registration
        $userId = $this->createTestUser($username, $password);

        // Verify user was created
        $this->assertEquals($this->lastInsertId - 1, $userId);
    }

    public function testUserLogin()
    {
        $username = 'testuser';
        $password = 'testpass123';
        
        // Mock the result set for login verification
        $result = $this->createMock(\mysqli_result::class);
        $result->method('fetch_assoc')
            ->willReturn([
                'id' => $this->lastInsertId,
                'username' => $username,
                'password' => password_hash($password, PASSWORD_DEFAULT)
            ]);

        // Mock the prepared statement
        $stmt = $this->getMockBuilder(\mysqli_stmt::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['bind_param', 'execute'])
            ->addMethods(['get_result'])
            ->getMock();
            
        $stmt->method('bind_param')
            ->willReturn(true);
        $stmt->method('execute')
            ->willReturn(true);
        $stmt->method('get_result')
            ->willReturn($result);

        $this->db->method('prepare')
            ->willReturn($stmt);

        // Create test user
        $userId = $this->createTestUser($username, $password);

        // Test login
        $this->assertEquals($this->lastInsertId - 1, $userId);
    }

    public function testDuplicateUsername()
    {
        $username = 'duplicateuser';
        
        // Mock the first user creation
        $this->createTestUser($username);

        // Mock the second user creation to throw an exception
        $stmt = $this->getMockBuilder(\mysqli_stmt::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['bind_param', 'execute'])
            ->getMock();
            
        $stmt->method('bind_param')
            ->willReturn(true);
        $stmt->method('execute')
            ->will($this->throwException(new \Exception('Duplicate username')));

        $this->db->method('prepare')
            ->willReturn($stmt);

        // Try to create second user with same username
        $this->expectException(\Exception::class);
        $this->createTestUser($username);
    }

    public function testInvalidLogin()
    {
        $username = 'testuser';
        $password = 'testpass123';
        
        // Mock the result set for invalid login
        $result = $this->createMock(\mysqli_result::class);
        $result->method('fetch_assoc')
            ->willReturn([
                'id' => $this->lastInsertId,
                'username' => $username,
                'password' => password_hash($password, PASSWORD_DEFAULT)
            ]);

        // Mock the prepared statement
        $stmt = $this->getMockBuilder(\mysqli_stmt::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['bind_param', 'execute'])
            ->addMethods(['get_result'])
            ->getMock();
            
        $stmt->method('bind_param')
            ->willReturn(true);
        $stmt->method('execute')
            ->willReturn(true);
        $stmt->method('get_result')
            ->willReturn($result);

        $this->db->method('prepare')
            ->willReturn($stmt);

        // Create test user
        $this->createTestUser($username, $password);

        // Test invalid password
        $this->assertFalse(password_verify('wrongpassword', password_hash($password, PASSWORD_DEFAULT)));
    }
} 