<?php

namespace Tests\Unit;

use App\Support\MobileMoneyNumber;
use PHPUnit\Framework\TestCase;

class MobileMoneyNumberTest extends TestCase
{
    public function test_normalizes_common_formats(): void
    {
        $this->assertSame('699009900', MobileMoneyNumber::normalize('+237 6 99 00 99 00'));
        $this->assertSame('699009900', MobileMoneyNumber::normalize('00237699009900'));
        $this->assertSame('677123456', MobileMoneyNumber::normalize('677-12-34-56'));
        $this->assertNull(MobileMoneyNumber::normalize('22212345'));
        $this->assertNull(MobileMoneyNumber::normalize(null));
    }

    public function test_detects_operator_mismatch(): void
    {
        $this->assertNull(MobileMoneyNumber::validationError('699009900', 'CM_OM'));
        $this->assertNull(MobileMoneyNumber::validationError('655000000', 'CM_OM'));
        $this->assertNull(MobileMoneyNumber::validationError('677000000', 'CM_MOMO'));
        $this->assertNull(MobileMoneyNumber::validationError('652000000', 'CM_MOMO'));
        $this->assertNotNull(MobileMoneyNumber::validationError('677000000', 'CM_OM'));
        $this->assertNotNull(MobileMoneyNumber::validationError('699000000', 'CM_MOMO'));
        // Préfixe inconnu : non bloqué
        $this->assertNull(MobileMoneyNumber::validationError('685000000', 'CM_MOMO'));
    }
}
