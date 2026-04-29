<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $incomeCategories = [
            ['name' => 'Salary/Wages', 'icon' => 'wallet', 'color' => '#22c55e'],
            ['name' => 'Business Income', 'icon' => 'store', 'color' => '#16a34a'],
            ['name' => 'Freelance', 'icon' => 'laptop', 'color' => '#15803d'],
            ['name' => 'Investment Returns', 'icon' => 'trending-up', 'color' => '#14532d'],
            ['name' => 'Gifts/Refunds', 'icon' => 'gift', 'color' => '#4ade80'],
            ['name' => 'Other Income', 'icon' => 'plus-circle', 'color' => '#86efac'],
        ];

        $expenseCategories = [
            ['name' => 'Food & Groceries', 'icon' => 'shopping-cart', 'color' => '#ef4444'],
            ['name' => 'Housing/Rent', 'icon' => 'home', 'color' => '#f97316'],
            ['name' => 'Utilities', 'icon' => 'zap', 'color' => '#f59e0b'],
            ['name' => 'Transportation', 'icon' => 'car', 'color' => '#eab308'],
            ['name' => 'Healthcare', 'icon' => 'heart', 'color' => '#14b8a6'],
            ['name' => 'Education', 'icon' => 'book', 'color' => '#06b6d4'],
            ['name' => 'Entertainment', 'icon' => 'film', 'color' => '#3b82f6'],
            ['name' => 'Shopping', 'icon' => 'bag', 'color' => '#8b5cf6'],
            ['name' => 'Personal Care', 'icon' => 'user', 'color' => '#d946ef'],
            ['name' => 'Insurance', 'icon' => 'shield', 'color' => '#ec4899'],
            ['name' => 'Debt Payments', 'icon' => 'credit-card', 'color' => '#f43f5e'],
            ['name' => 'Other Expenses', 'icon' => 'more-horizontal', 'color' => '#64748b'],
        ];

        // Create income categories
        foreach ($incomeCategories as $category) {
            Category::create([
                'user_id' => 1,
                'name' => $category['name'],
                'type' => 'income',
                'icon' => $category['icon'],
                'color' => $category['color'],
                'is_system' => true,
                'is_active' => true,
            ]);
        }

        // Create expense categories
        foreach ($expenseCategories as $category) {
            Category::create([
                'user_id' => 1,
                'name' => $category['name'],
                'type' => 'expense',
                'icon' => $category['icon'],
                'color' => $category['color'],
                'is_system' => true,
                'is_active' => true,
            ]);
        }
    }
}
