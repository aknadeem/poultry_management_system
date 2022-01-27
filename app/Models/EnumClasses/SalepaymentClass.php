<?php
namespace App\Models\EnumClasses;

enum SalepaymentStatus :int {
    case UNPAID = 1;
    case PENDING = 2;
    case PAID = 3;

    public function label(): string {
        return static::getLabel($this);
    }
    
    public static function getLabel(self $value): string {
        return match ($value) {
            SalepaymentStatus::UNPAID => 'Un paid',
            SalepaymentStatus::PAID => 'Paid',
            SalepaymentStatus::PENDING => 'Pending',
        };
    }
    
}
?>