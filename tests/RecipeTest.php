<?php

namespace Tests;

class RecipeTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->cleanTestData();
    }

    public function testCreateRecipe()
    {
        // Create test user
        $userId = $this->createTestUser();

        // Create test recipe
        $recipeId = $this->createTestRecipe($userId);

        // Verify recipe was created
        $sql = "SELECT * FROM recipes WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('i', $recipeId);
        $stmt->execute();
        $result = $stmt->get_result();
        $recipe = $result->fetch_assoc();

        $this->assertNotFalse($recipe);
        $this->assertEquals($userId, $recipe['user_id']);
        $this->assertEquals('Test Recipe', $recipe['title']);
        $this->assertEquals('Test ingredients', $recipe['ingredients']);
        $this->assertEquals('Test instructions', $recipe['instructions']);
    }

    public function testUpdateRecipe()
    {
        // Create test user and recipe
        $userId = $this->createTestUser();
        $recipeId = $this->createTestRecipe($userId);

        // Update recipe
        $newTitle = 'Updated Recipe';
        $newIngredients = 'Updated ingredients';
        $newInstructions = 'Updated instructions';

        $sql = "UPDATE recipes SET title = ?, ingredients = ?, instructions = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('sssi', $newTitle, $newIngredients, $newInstructions, $recipeId);
        $stmt->execute();

        // Verify update
        $sql = "SELECT * FROM recipes WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('i', $recipeId);
        $stmt->execute();
        $result = $stmt->get_result();
        $recipe = $result->fetch_assoc();

        $this->assertEquals($newTitle, $recipe['title']);
        $this->assertEquals($newIngredients, $recipe['ingredients']);
        $this->assertEquals($newInstructions, $recipe['instructions']);
    }

    public function testDeleteRecipe()
    {
        // Create test user and recipe
        $userId = $this->createTestUser();
        $recipeId = $this->createTestRecipe($userId);

        // Delete recipe
        $sql = "DELETE FROM recipes WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('i', $recipeId);
        $stmt->execute();

        // Verify deletion
        $sql = "SELECT * FROM recipes WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('i', $recipeId);
        $stmt->execute();
        $result = $stmt->get_result();

        $this->assertEquals(0, $result->num_rows);
    }

    public function testListUserRecipes()
    {
        // Create test user
        $userId = $this->createTestUser();

        // Create multiple recipes
        $this->createTestRecipe($userId, 'Recipe 1');
        $this->createTestRecipe($userId, 'Recipe 2');
        $this->createTestRecipe($userId, 'Recipe 3');

        // Get user's recipes
        $sql = "SELECT * FROM recipes WHERE user_id = ? ORDER BY id DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $result = $stmt->get_result();
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

        // Create recipes with different titles
        $this->createTestRecipe($userId, 'Chicken Curry');
        $this->createTestRecipe($userId, 'Beef Steak');
        $this->createTestRecipe($userId, 'Vegetable Soup');

        // Search for recipes containing 'Chicken'
        $searchTerm = '%Chicken%';
        $sql = "SELECT * FROM recipes WHERE title LIKE ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('s', $searchTerm);
        $stmt->execute();
        $result = $stmt->get_result();
        $recipes = $result->fetch_all(MYSQLI_ASSOC);

        $this->assertCount(1, $recipes);
        $this->assertEquals('Chicken Curry', $recipes[0]['title']);
    }
} 