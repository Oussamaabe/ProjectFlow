<?php

namespace App\Enum;

enum AffaireStatus: string
{
    case EN_COURS = 'en_cours';
    case ADJUDICATAIRE = 'adjudicataire';
    case PERDU = 'perdu';
    case ABANDONNE = 'abandonne';
    
    public static function values(): array
    {
        return array_map(fn($case) => $case->value, self::cases());
    }
    
    public static function getLabel(string $status): string
    {
        return match ($status) {
            self::EN_COURS->value => 'En cours',
            self::ADJUDICATAIRE->value => 'Adjudicataire',
            self::PERDU->value => 'Perdu',
            self::ABANDONNE->value => 'Abandonné',
            default => $status,
        };
    }
}