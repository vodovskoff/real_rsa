<?php

namespace User\RealRsa\Service;

class RSA
{
    const separatorNumber = 32;
    const minimalPrime = 100;
    const maximalPrime = 5000;

    public static function generatePQ(): array{
        $pq = array();
        $result = array();
        for ($i=self::minimalPrime; $i<self::maximalPrime; $i++){
            if(!in_array((gmp_intval(gmp_nextprime($i))), $pq)){
                $pq[] = gmp_intval(gmp_nextprime($i));
            }
        }
        $pN = random_int(0, count($pq)/2);
        $qN = random_int(count($pq)/2+1, count($pq));

        $result[] = $pq[$pN];
        $result[] = $pq[$qN];
        return $result;
    }

    public static function encrypt(string $m, int $e, int $n): string {
        $encodedMessage = "";
        $chars = str_split($m);
        foreach($chars as $char){
            $k = gmp_pow(ord($char), $e);
            $k = gmp_mod($k, $n);
            $encodedMessage = $encodedMessage.chr(self::separatorNumber).gmp_intval($k);
        }
        return $encodedMessage;
    }

    public static function decrypt(string $input, int $d, int $n): string
    {
        $result = "";
        $arr = explode(chr(self::separatorNumber), $input);
        foreach ($arr as $item){
            if ($arr!='')
            {
                $k = gmp_pow($item, $d);
                $k = gmp_mod($k, $n);
                $result= $result.chr(gmp_intval($k));
            }
        }
        return $result;
    }

    public static function generateEncryptor(int $m): int
    {
        $arr = array();
        $d=$m-1;
        for ($i = 2; $i <= $m; $i++)
        {
            if ((fmod($m, $i) == 0) && (fmod($d, $i) == 0))
            {
                $d--;
            }
        }
        return $d;
    }

    public static function generateDecryptor(int $e, int $m): int
    {
        $d = 1;

        $arr = array();
        while (fmod($e * $d, $m)!= 1 or ($e==$d))
        {
            $d++;
        }
        return $d;
    }

    public static function isPrime(int $a): bool
    {
        if ($a == 1)
            return 0;
        for ($i = 2; $i <= $a/2; $i++)
        {
            if ($a % $i == 0)
                return false;
        }
        return true;
    }
}