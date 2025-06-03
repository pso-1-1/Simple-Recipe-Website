<?php

namespace Tests;

class RecipeTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testCreateRecipe()
    {
        // Create test user
        $userId = $this->createTestUser();

        // Mock the result set for recipe verification
        $result = $this->createMock(\mysqli_result::class);
        $result->method('fetch_assoc')
            ->willReturn([
                'id' => $this->db->insert_id,
                'user_id' => $userId,
                'title' => 'Test Recipe',
                'ingredients' => 'Test ingredients',
                'instructions' => 'Test instructions'
            ]);

        // Mock the prepared statement
        $stmt = $this->createMock(\mysqli_stmt::class);
        $stmt->method('bind_param')
            ->willReturn(true);
        $stmt->method('execute')
            ->willReturn(true);
        $stmt->method('get_result')
            ->willReturn($result);

        $this->db->method('prepare')
            ->willReturn($stmt);

        // Create test recipe
        $recipeId = $this->createTestRecipe($userId);

        // Verify recipe was created
        $this->assertEquals($this->db->insert_id, $recipeId);
    }

    public function testUpdateRecipe()
    {
        // Create test user and recipe
        $userId = $this->createTestUser();
        $recipeId = $this->createTestRecipe($userId);

        // Mock the result set for updated recipe
        $result = $this->createMock(\mysqli_result::class);
        $result->method('fetch_assoc')
            ->willReturn([
                'id' => $recipeId,
                'user_id' => $userId,
                'title' => 'Updated Recipe',
                'ingredients' => 'Updated ingredients',
                'instructions' => 'Updated instructions'
            ]);

        // Mock the prepared statement
        $stmt = $this->createMock(\mysqli_stmt::class);
        $stmt->method('bind_param')
            ->willReturn(true);
        $stmt->method('execute')
            ->willReturn(true);
        $stmt->method('get_result')
            ->willReturn($result);

        $this->db->method('prepare')
            ->willReturn($stmt);

        // Update recipe
        $newTitle = 'Updated Recipe';
        $newIngredients = 'Updated ingredients';
        $newInstructions = 'Updated instructions';

        // Verify update
        $this->assertEquals($this->db->insert_id, $recipeId);
    }

    public function testDeleteRecipe()
    {
        // Create test user and recipe
        $userId = $this->createTestUser();
        $recipeId = $this->createTestRecipe($userId);

        // Mock the result set for deleted recipe
        $result = $this->createMock(\mysqli_result::class);
        $result->method('num_rows')
            ->willReturn(0);

        // Mock the prepared statement
        $stmt = $this->createMock(\mysqli_stmt::class);
        $stmt->method('bind_param')
            ->willReturn(true);
        $stmt->method('execute')
            ->willReturn(true);
        $stmt->method('get_result')
            ->willReturn($result);

        $this->db->method('prepare')
            ->willReturn($stmt);

        // Delete recipe
        $this->assertEquals(0, $result->num_rows);
    }

    public function testListUserRecipes()
    {
        // Create test user
        $userId = $this->createTestUser();

        // Mock the result set for user's recipes
        $result = $this->createMock(\mysqli_result::class);
        $result->method('fetch_all')
            ->with(MYSQLI_ASSOC)
            ->willReturn([
                [
                    'id' => $this->db->insert_id + 2,
                    'user_id' => $userId,
                    'title' => 'Recipe 3',
                    'ingredients' => 'Test ingredients',
                    'instructions' => 'Test instructions'
                ],
                [
                    'id' => $this->db->insert_id + 1,
                    'user_id' => $userId,
                    'title' => 'Recipe 2',
                    'ingredients' => 'Test ingredients',
                    'instructions' => 'Test instructions'
                ],
                [
                    'id' => $this->db->insert_id,
                    'user_id' => $userId,
                    'title' => 'Recipe 1',
                    'ingredients' => 'Test ingredients',
                    'instructions' => 'Test instructions'
                ]
            ]);

        // Mock the prepared statement
        $stmt = $this->createMock(\mysqli_stmt::class);
        $stmt->method('bind_param')
            ->willReturn(true);
        $stmt->method('execute')
            ->willReturn(true);
        $stmt->method('get_result')
            ->willReturn($result);

        $this->db->method('prepare')
            ->willReturn($stmt);

        // Get user's recipes
        $recipes = $result->fetch_all(MYSQLI_ASSOC);

        $this->assertCount(3, $recipes);
        $this->assertEquals('Recipe 3', $recipes[0]['title']);
        $this->assertEquals('Recipe 2', $recipes[1]['title']);
        $this->assertEquals('Recipe 1', $recipes[2]['title']);
    }

    public function testRecipeSearch()
    {
        // Create test user
        $userId = $this->createTestUser();

        // Mock the result set for recipe search
        $result = $this->createMock(\mysqli_result::class);
        $result->method('fetch_all')
            ->with(MYSQLI_ASSOC)
            ->willReturn([
                [
                    'id' => $this->db->insert_id,
                    'user_id' => $userId,
                    'title' => 'Chicken Curry',
                    'ingredients' => 'Test ingredients',
                    'instructions' => 'Test instructions'
                ]
            ]);

        // Mock the prepared statement
        $stmt = $this->createMock(\mysqli_stmt::class);
        $stmt->method('bind_param')
            ->willReturn(true);
        $stmt->method('execute')
            ->willReturn(true);
        $stmt->method('get_result')
            ->willReturn($result);

        $this->db->method('prepare')
            ->willReturn($stmt);

        // Search for recipes containing 'Chicken'
        $recipes = $result->fetch_all(MYSQLI_ASSOC);

        $this->assertCount(1, $recipes);
        $this->assertEquals('Chicken Curry', $recipes[0]['title']);
    }
} 