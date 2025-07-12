<?php
// src/Enum/RessourceType.php
namespace App\Enum;

enum RessourceType: string
{
    case HUMAIN = 'humain';
    case MATERIEL = 'materiel';
    case MATIERE = 'matiere';
    
    public static function values(): array
    {
        return array_map(fn($case) => $case->value, self::cases());
    }
    
    public function label(): string
    {
        return match ($this) {
            self::HUMAIN => 'Ressource humaine',
            self::MATERIEL => 'Ressource matérielle',
            self::MATIERE => 'Matière première',
        };
    }
}