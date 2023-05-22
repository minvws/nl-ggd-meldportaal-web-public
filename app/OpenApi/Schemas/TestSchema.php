<?php

declare(strict_types=1);

namespace App\OpenApi\Schemas;

use App\Http\Requests\BaseTestFormRequest;
use GoldSpecDigital\ObjectOrientedOAS\Contracts\SchemaContract;
use GoldSpecDigital\ObjectOrientedOAS\Objects\AllOf;
use GoldSpecDigital\ObjectOrientedOAS\Objects\AnyOf;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Not;
use GoldSpecDigital\ObjectOrientedOAS\Objects\OneOf;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;
use Illuminate\Validation\Rules\In;
use Vyuldashev\LaravelOpenApi\Contracts\Reusable;
use Vyuldashev\LaravelOpenApi\Factories\SchemaFactory;

class TestSchema extends SchemaFactory implements Reusable
{
    /**
     * @return AllOf|OneOf|AnyOf|Not|Schema
     */
    public function build(): SchemaContract
    {
        return Schema::object('Test')
            ->properties(
                ...$this->buildPropertiesFromRequest()
            );
    }

    protected function buildPropertiesFromRequest(): array
    {
        $requestRules = (new BaseTestFormRequest())->rules();

        $schemas = [];

        foreach ($requestRules as $key => $rules) {
            if (!is_array($rules)) {
                continue;
            }

            $schema = $this->getSchemaBasedOnTypeInRules($key, $rules);
            if ($schema === null) {
                continue;
            }

            $schema = $this->setRulesOnSchema($schema, $rules);
            $schemas[] = $schema;
        }

        return $schemas;
    }

    public function getSchemaBasedOnTypeInRules(string $field, array $rules): ?Schema
    {
        /**
         * @var array<string, Schema> $acceptedTypes
         */
        $acceptedTypes = [
            'string' => Schema::string(),
            'integer' => Schema::integer(),
            'boolean' => Schema::boolean(),
            'array' => Schema::array(),
            'date' => Schema::string(),
            'email' => Schema::string(),
        ];

        foreach ($acceptedTypes as $type => $schema) {
            if (!in_array($type, $rules, true)) {
                // TODO: Maybe trow exception?
                continue;
            }

            return $schema->objectId($field);
        }

        return null;
    }

    private function setRulesOnSchema(Schema $schema, array $rules): Schema
    {
        foreach ($rules as $rule) {
            if (!is_string($rule)) {
                if ($rule instanceof In) {
                    $schema = $schema->enum(...$this->mapRuleStringValuesToArray((string) $rule));
                }
                continue;
            }

            // if rule is min:10, max:10, etc
            $value = null;
            if (strpos($rule, ':') !== false) {
                [$rule, $value] = explode(':', $rule, 2);
            }

            $schema = match ($rule) {
                'required' => $schema->required(),
                'nullable' => $schema->nullable(),
                'email' => $schema->format('email'),
                'date' => $schema->format('date'),
                'min' => $this->schemaRuleMinOrMinLength($schema, $value),
                'max' => $this->schemaRuleMaxOrMaxLength($schema, $value),
                'regex' => $schema->pattern($value),
                default => $schema,
            };
        }

        return $schema;
    }

    private function schemaRuleMinOrMinLength(Schema $schema, ?string $value): Schema
    {
        if ($value === null) {
            return $schema;
        }

        if ($schema->type === Schema::TYPE_STRING) {
            return $schema->minLength((int) $value);
        }

        if ($schema->type === Schema::TYPE_INTEGER) {
            return $schema->minimum((int) $value);
        }

        return $schema;
    }

    private function schemaRuleMaxOrMaxLength(Schema $schema, ?string $value): Schema
    {
        if ($value === null) {
            return $schema;
        }

        if ($schema->type === Schema::TYPE_STRING) {
            return $schema->maxLength((int) $value);
        }

        if ($schema->type === Schema::TYPE_INTEGER) {
            return $schema->maximum((int) $value);
        }

        return $schema;
    }

    private function mapRuleStringValuesToArray(string $rule): array
    {
        // Split rule and values
        [$rule, $value] = explode(':', $rule, 2);

        // Split values
        $split = explode(',', $value);
        foreach ($split as $key => $value) {
            $value = str_replace('"', '""', $value);
            $split[$key] = trim($value, '"');
        }

        return $split;
    }
}
