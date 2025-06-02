<?php

namespace Tests;

class AuthTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->cleanTestData();
    }

    public function testUserRegistration()
    {
        $username = 'newuser';
        $password = 'newpass123';
        $email = 'newuser@test.com';

        // Test registration
        $userId = $this->createTestUser($username, $password);

        // Verify user was created
        $sql = "SELECT * FROM users WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        $this->assertNotFalse($user);
        $this->assertEquals($username, $user['username']);
        $this->assertEquals($email, $user['email']);
        $this->assertTrue(password_verify($password, $user['password']));
    }

    public function testUserLogin()
    {
        $username = 'testuser';
        $password = 'testpass123';
        
        // Create test user
        $userId = $this->createTestUser($username, $password);

        // Test login
        $sql = "SELECT * FROM users WHERE username = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        $this->assertNotFalse($user);
        $this->assertTrue(password_verify($password, $user['password']));
    }

    public function testDuplicateUsername()
    {
        $username = 'duplicateuser';
        
        // Create first user
        $this->createTestUser($username);

        // Try to create second user with same username
        $this->expectException(\Exception::class);
        $this->createTestUser($username);
    }

    public function testInvalidLogin()
    {
        $username = 'testuser';
        $password = 'testpass123';
        
        // Create test user
        $this->createTestUser($username, $password);

        // Test invalid password
        $sql = "SELECT * FROM users WHERE username = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        $this->assertNotFalse($user);
        $this->assertFalse(password_verify('wrongpassword', $user['password']));
    }
} 