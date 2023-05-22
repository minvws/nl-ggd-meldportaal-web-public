<?php

declare(strict_types=1);

namespace Tests\Unit\Validators;

use App\Validators\NotInFuture;
use Illuminate\Support\Carbon;
use Tests\Unit\TestCase;

class NotInFutureTest extends TestCase
{
    public function testNotInFuture()
    {
        $validator = new NotInFuture();

        $fixedDate = Carbon::create(2020, 5, 5, 12, 34, 56);
        Carbon::setTestNow($fixedDate); // Or any dates

        $this->assertTrue($validator->passes('date', '2019-01-01'));
        $this->assertTrue($validator->passes('date', '2020-05-04'));
        $this->assertTrue($validator->passes('date', '2020-05-05'));
        $this->assertFalse($validator->passes('date', '2020-05-06'));
        $this->assertFalse($validator->passes('date', '2021-01-01'));
    }

    public function testNotInFutureWithXXes()
    {
        $validator = new NotInFuture();

        $fixedDate = Carbon::create(2020, 5, 5, 12, 34, 56);
        Carbon::setTestNow($fixedDate); // Or any dates

        $this->assertTrue($validator->passes('date', '2019-01-XX'));
        $this->assertTrue($validator->passes('date', '2019-XX-XX'));
        $this->assertTrue($validator->passes('date', '2020-05-XX'));
        $this->assertTrue($validator->passes('date', '2020-XX-XX'));
        $this->assertFalse($validator->passes('date', '2021-01-XX'));
        $this->assertFalse($validator->passes('date', '2021-XX-XX'));
    }

    public function tearDown(): void
    {
        // Appears to be necessary to reset the test date
        Carbon::setTestNow();
    }
}
