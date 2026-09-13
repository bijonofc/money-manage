<?php

namespace App\Services;

use App\Models\Category;

class CategoryService
{
    /**
     * Standard starter categories for new users.
     */
    public static array $defaultIncomeCategories = [
        ['name' => 'Salary/Wages', 'icon' => 'wallet', 'color' => '#22c55e'],
        ['name' => 'Business Income', 'icon' => 'store', 'color' => '#16a34a'],
        ['name' => 'Freelance', 'icon' => 'laptop', 'color' => '#15803d'],
        ['name' => 'Investment Returns', 'icon' => 'trending-up', 'color' => '#14532d'],
        ['name' => 'Gifts/Refunds', 'icon' => 'gift', 'color' => '#4ade80'],
        ['name' => 'Other Income', 'icon' => 'plus-circle', 'color' => '#86efac'],
    ];

    public static array $defaultExpenseCategories = [
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

    /**
     * Populate standard default categories for a user if they don't have any.
     */
    public static function seedDefaultCategoriesForUser(int $userId): int
    {
        $existingCount = Category::where('tenant_id', $userId)->count();
        if ($existingCount > 0) {
            return 0;
        }

        $created = 0;

        foreach (self::$defaultIncomeCategories as $cat) {
            Category::create([
                'tenant_id' => $userId,
                'name' => $cat['name'],
                'type' => 'income',
                'icon' => $cat['icon'],
                'color' => $cat['color'],
                'is_system' => true,
                'is_active' => true,
            ]);
            $created++;
        }

        foreach (self::$defaultExpenseCategories as $cat) {
            Category::create([
                'tenant_id' => $userId,
                'name' => $cat['name'],
                'type' => 'expense',
                'icon' => $cat['icon'],
                'color' => $cat['color'],
                'is_system' => true,
                'is_active' => true,
            ]);
            $created++;
        }

        return $created;
    }
}
