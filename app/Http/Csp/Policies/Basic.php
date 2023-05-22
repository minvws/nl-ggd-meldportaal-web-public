<?php

declare(strict_types=1);

namespace App\Http\Csp\Policies;

use Spatie\Csp\Directive;
use Spatie\Csp\Keyword;
use Spatie\Csp\Policies\Policy;
use Spatie\Csp\Scheme;

/*
 * This class defines the default CSP (policy) for loading/executing content
 */

class Basic extends Policy
{
    /**
     * @return void
     * @throws \Spatie\Csp\Exceptions\InvalidDirective
     * @throws \Spatie\Csp\Exceptions\InvalidValueSet
     */
    public function configure()
    {
        $this->addDirective(Directive::DEFAULT, Keyword::SELF)
            ->addDirective(Directive::SCRIPT, [Keyword::SELF, Keyword::UNSAFE_EVAL])
            ->addDirective(Directive::STYLE, Keyword::SELF)
            ->addDirective(Directive::CONNECT, ['*', 'data:'])
            ->addDirective(Directive::BLOCK_ALL_MIXED_CONTENT, false)
            ->addDirective(Directive::FORM_ACTION, Keyword::SELF)
            ->addDirective(Directive::IMG, Scheme::DATA)
            ->addDirective(Directive::IMG, Keyword::SELF)
            ->addDirective(Directive::OBJECT, Keyword::NONE)
            ->addDirective(Directive::FONT, Keyword::SELF)
        ;

        if (config('app.debug')) {
            $this->addDirective(Directive::FONT, 'data:')
                ->addDirective(Directive::SCRIPT, Keyword::UNSAFE_INLINE)
                ->addDirective(Directive::STYLE, Keyword::UNSAFE_INLINE)
            ;
        } else {
            $this->addNonceForDirective(Directive::SCRIPT)
                ->addNonceForDirective(Directive::STYLE);
        }
    }
}
