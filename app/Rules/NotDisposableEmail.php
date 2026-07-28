<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Str;

class NotDisposableEmail implements Rule
{
    protected array $blocked;

    public function __construct()
    {
        // Tarik daftar dari config
        $this->blocked = config('disposable.domains', []);
    }

    public function passes($attribute, $value): bool
    {
        $domain = Str::of($value)->after('@')->lower()->toString();
        return ! in_array($domain, $this->blocked, true);
    }

    public function message(): string
    {
        return 'Gunakan email permanen (bukan disposable).';
    }
}
