<?php

namespace App\Helpers;

use App\Models\User;
use Illuminate\Support\Facades\Cache;

class MenuCacheHelper
{
    /**
     * Generate cache key for user menus
     */
    public static function generateCacheKey(User $user): string
    {
        $userPermissions = $user->getAllPermissions()->pluck('id')->sort()->implode(',');
        return "menus:user:permissions:{$userPermissions}";
    }
    
    /**
     * Clear cache for specific user
     */
    public static function clearForUser(User $user): bool
    {
        $cacheKey = self::generateCacheKey($user);
        return Cache::forget($cacheKey);
    }
    
    /**
     * Clear all menu cache entries
     */
    public static function clearAll(): bool
    {
        return Cache::flush();
    }
    
    /**
     * Clear cache for users with specific permissions
     */
    public static function clearForPermission(int $permissionId): void
    {
        // Get all cache keys that contain this permission
        $pattern = "menus:user:permissions:*{$permissionId}*";
        
        // Note: This is a simplified approach. In production, you might want to
        // store cache keys in a separate table for more efficient clearing
        Cache::flush(); // For now, clear all menu cache
    }
} 