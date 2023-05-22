<?php

declare(strict_types=1);

namespace App\Http\Requests;

class ContactFormRequest extends BaseTestFormRequest
{
    public function rules(string $prefix = 'data.'): array
    {
        return parent::rules($prefix);
    }

    public function attributes(string $prefix = 'data.'): array
    {
        return parent::attributes($prefix);
    }

    public function messages(string $prefix = 'data.'): array
    {
        return parent::messages($prefix);
    }
}
