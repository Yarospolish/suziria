<?php

namespace App\Utils;

class Validator
{
    public static function validate(array $data, array $rules): void
    {
        foreach ($rules as $field => $ruleString) {
            if (str_contains($ruleString, 'required') && !array_key_exists($field, $data)) {
                throw new \InvalidArgumentException("Field {$field} is required");
            }

            if (!array_key_exists($field, $data)) {
                continue;
            }

            $rules = explode('|', $ruleString);
            foreach ($rules as $rule) {
                self::applyRule($field, $data[$field], $rule);
            }
        }
    }

    private static function applyRule(string $field, mixed $value, string $rule): void
    {
        if ($rule === 'required' && empty($value)) {
            throw new \InvalidArgumentException("Field {$field} is required");
        }

        if ($rule === 'string' && !is_string($value)) {
            throw new \InvalidArgumentException("Field {$field} must be a string");
        }

        if (str_starts_with($rule, 'max:') && is_string($value)) {
            $max = (int) substr($rule, 4);
            if (strlen($value) > $max) {
                throw new \InvalidArgumentException("Field {$field} must be no more than {$max} characters");
            }
        }

        if ($rule === 'numeric' && !is_numeric($value)) {
            throw new \InvalidArgumentException("Field {$field} must be numeric");
        }

        if (str_starts_with($rule, 'min:') && is_numeric($value)) {
            $min = (float) substr($rule, 4);
            if ($value < $min) {
                throw new \InvalidArgumentException("Field {$field} must be at least {$min}");
            }
        }

        if ($rule === 'array' && !is_array($value)) {
            throw new \InvalidArgumentException("Field {$field} must be an array");
        }

        if (str_starts_with($rule, 'in:') && is_string($value)) {
            $allowed = explode(',', substr($rule, 3));
            if (!in_array($value, $allowed)) {
                throw new \InvalidArgumentException("Field {$field} must be one of: " . implode(', ', $allowed));
            }
        }
    }
}