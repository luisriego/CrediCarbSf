<?php

declare(strict_types=1);

namespace App\Domain\Validation\Traits;

use App\Domain\Exception\InvalidArgumentException;

use function mb_strlen;
use function preg_match;
use function preg_replace;

trait AssertTaxpayerValidatorTrait
{
    public function cleanTaxpayer(string $taxpayer): string
    {
        return preg_replace('/\D/', '', $taxpayer);
    }

    public function assertValidTaxpayer(string $taxpayer): void
    {
        $cleanTaxpayer = $this->cleanTaxpayer($taxpayer);
        $this->validateTaxpayer($cleanTaxpayer);
    }

    public function validTaxpayer(string $taxpayer): string
    {
        $cleanTaxpayer = $this->cleanTaxpayer($taxpayer);
        $this->validateTaxpayer($cleanTaxpayer);

        return $cleanTaxpayer;
    }

    private function validateTaxpayer(string $taxpayer): void
    {
        if (mb_strlen($taxpayer) === 11) {
            $this->assertValidCpf($taxpayer);
        } elseif (mb_strlen($taxpayer) === 14) {
            $this->assertValidCnpj($taxpayer);
        } else {
            throw new InvalidArgumentException('Invalid Taxpayer length');
        }
    }

    private function assertValidCpf(string $cpf): void
    {
        if (preg_match('/(\d)\1{10}/', $cpf)) {
            throw new InvalidArgumentException('Invalid CPF format');
        }

        for ($t = 9; $t < 11; $t++) {
            for ($d = 0, $c = 0; $c < $t; $c++) {
                $d += $cpf[$c] * (($t + 1) - $c);
            }
            $d = ((10 * $d) % 11) % 10;

            if ($cpf[$c] !== $d) {
                throw new InvalidArgumentException('Invalid CPF digits');
            }
        }
    }

    private function assertValidCnpj(string $cnpj): void
    {
        if (preg_match('/(\d)\1{13}/', $cnpj)) {
            throw new InvalidArgumentException('Invalid CNPJ format');
        }

        $sum1 = 0;
        $sum2 = 0;
        $weights1 = [5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
        $weights2 = [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];

        for ($i = 0; $i < 12; $i++) {
            $sum1 += (int) $cnpj[$i] * $weights1[$i];
            $sum2 += (int) $cnpj[$i] * $weights2[$i];
        }

        $remainder1 = $sum1 % 11;
        $digit1 = $remainder1 < 2 ? 0 : 11 - $remainder1;

        $sum2 += $digit1 * $weights2[12];
        $remainder2 = $sum2 % 11;
        $digit2 = $remainder2 < 2 ? 0 : 11 - $remainder2;

        if ((int) $cnpj[12] !== $digit1 || (int) $cnpj[13] !== $digit2) {
            throw new InvalidArgumentException('Invalid CNPJ digits');
        }
    }
}
