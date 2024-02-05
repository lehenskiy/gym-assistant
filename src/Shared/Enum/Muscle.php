<?php

declare(strict_types=1);

namespace App\Shared\Enum;

enum Muscle: int
{
    case RearDelt = 0;
    case MiddleDelt = 1;
    case FrontDelt = 2;
    case Trapezius = 3;
    case Latissimus = 4;
    case ExtensorSpine = 5;
    case PectoralUpper = 6;
    case PectoralLower = 7;
    case Biceps = 8;
    case Triceps = 9;
    case ObliqueAbs = 10;
    case RectusAbs = 11;
    case Gluteal = 12;
    case Quadriceps = 13;
    case Hamstring = 14;
    case Calf = 15;

    public function fullName(): string
    {
        return match($this) {
            self::RearDelt => 'Rear deltoid muscles',
            self::MiddleDelt => 'Middle deltoid muscles',
            self::FrontDelt => 'Front deltoid muscles',
            self::Trapezius => 'Trapezius muscles',
            self::Latissimus => 'Latissimus muscles',
            self::ExtensorSpine => 'Back extensor muscles',
            self::PectoralUpper => 'Upper pectoral muscles',
            self::PectoralLower => 'Lower pectoral muscles',
            self::Biceps => 'Biceps',
            self::Triceps => 'Triceps',
            self::ObliqueAbs => 'Oblique abdominal muscles',
            self::RectusAbs => 'Rectus abdominal muscles',
            self::Gluteal => 'Gluteal muscles',
            self::Quadriceps => 'Quadriceps',
            self::Hamstring => 'Hamstring',
            self::Calf => 'Calf muscles',
        };
    }
}
