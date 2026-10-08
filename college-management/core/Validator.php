<?php
declare(strict_types=1);

namespace Core;

/**
 * Minimal rule-based validator: required|email|min:3|max:255|numeric|in:a,b
 */
final class Validator
{
    private array $errors = [];

    public function __construct(private array $data, private array $rules)
    {
        $this->run();
    }

    private function run(): void
    {
        foreach ($this->rules as $field => $ruleString) {
            $rules = explode('|', $ruleString);
            $value = $this->data[$field] ?? null;

            foreach ($rules as $rule) {
                [$name, $param] = array_pad(explode(':', $rule, 2), 2, null);
                $this->applyRule($field, $value, $name, $param);
            }
        }
    }

    private function applyRule(string $field, $value, string $rule, ?string $param): void
    {
        switch ($rule) {
            case 'required':
                if ($value === null || $value === '') {
                    $this->addError($field, "The {$field} field is required.");
                }
                break;
            case 'email':
                if ($value && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->addError($field, "The {$field} must be a valid email address.");
                }
                break;
            case 'min':
                if ($value !== null && $value !== '' && strlen((string) $value) < (int) $param) {
                    $this->addError($field, "The {$field} must be at least {$param} characters.");
                }
                break;
            case 'max':
                if ($value !== null && $value !== '' && strlen((string) $value) > (int) $param) {
                    $this->addError($field, "The {$field} must not exceed {$param} characters.");
                }
                break;
            case 'numeric':
                if ($value !== null && $value !== '' && !is_numeric($value)) {
                    $this->addError($field, "The {$field} must be numeric.");
                }
                break;
            case 'in':
                $allowed = explode(',', (string) $param);
                if ($value !== null && $value !== '' && !in_array((string)$value, $allowed, true)) {
                    $this->addError($field, "The {$field} is invalid.");
                }
                break;
            case 'date':
                if ($value && !strtotime((string) $value)) {
                    $this->addError($field, "The {$field} must be a valid date.");
                }
                break;
        }
    }

    private function addError(string $field, string $message): void
    {
        $this->errors[$field][] = $message;
    }

    public function fails(): bool
    {
        return !empty($this->errors);
    }

    public function errors(): array
    {
        return $this->errors;
    }

    public function firstErrors(): array
    {
        return array_map(fn($msgs) => $msgs[0], $this->errors);
    }
}
