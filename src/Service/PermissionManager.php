<?php

// src/Service/PermissionManager.php
namespace App\Service;

class PermissionManager
{
    public const PERMISSIONS = [
        'Gestion des utilisateurs' => [
            'path' => '/admin/users',
            'permission' => 'CAN_ACCESS_USERS'
        ],
        'Gestion des groupes' => [
            'path' => '/admin/groups',
            'permission' => 'CAN_ACCESS_GROUPS'
        ],
        'Tableau de bord' => [
            'path' => '/admin/dashboard',
            'permission' => 'CAN_ACCESS_DASHBOARD'
        ],
        // Ajoutez toutes vos routes ici...
    ];

    public static function getPermissionChoices(): array
    {
        return array_map(function($item) {
            return $item['permission'];
        }, self::PERMISSIONS);
    }

    public static function getRouteForPermission(string $permission): ?string
    {
        foreach (self::PERMISSIONS as $item) {
            if ($item['permission'] === $permission) {
                return $item['path'];
            }
        }
        return null;
    }
}