<?php
/**
 * Validator.php
 * Reusable server-side form validation class for the Online Store.
 */
class Validator {
    private array $errors = [];
    private array $data   = [];

    /**
     * Load and sanitize raw POST data for the given fields.
     */
    public function load(array $fields): self {
        foreach ($fields as $field) {
            $this->data[$field] = isset($_POST[$field])
                ? trim(htmlspecialchars($_POST[$field], ENT_QUOTES, 'UTF-8'))
                : '';
        }
        return $this;
    }

    /** Return sanitized value */
    public function get(string $field): string {
        return $this->data[$field] ?? '';
    }

    /** Return all sanitized data */
    public function all(): array {
        return $this->data;
    }

    // ── Rules ─────────────────────────────────────────────────────────────────

    public function required(string $field, string $label): self {
        if ($this->data[$field] === '') {
            $this->errors[$field] = "{$label} is required.";
        }
        return $this;
    }

    public function minLength(string $field, int $min, string $label): self {
        if (!isset($this->errors[$field]) && mb_strlen($this->data[$field]) < $min) {
            $this->errors[$field] = "{$label} must be at least {$min} characters.";
        }
        return $this;
    }

    public function maxLength(string $field, int $max, string $label): self {
        if (!isset($this->errors[$field]) && mb_strlen($this->data[$field]) > $max) {
            $this->errors[$field] = "{$label} must not exceed {$max} characters.";
        }
        return $this;
    }

    public function email(string $field, string $label = 'Email'): self {
        if (!isset($this->errors[$field]) && $this->data[$field] !== '') {
            if (!filter_var($this->data[$field], FILTER_VALIDATE_EMAIL)) {
                $this->errors[$field] = "Please enter a valid {$label} address.";
            }
        }
        return $this;
    }

    public function matches(string $field, string $otherField, string $label): self {
        if (!isset($this->errors[$field]) && $this->data[$field] !== ($this->data[$otherField] ?? '')) {
            $this->errors[$field] = "{$label} do not match.";
        }
        return $this;
    }

    public function phone(string $field, string $label = 'Phone'): self {
        if (!isset($this->errors[$field]) && $this->data[$field] !== '') {
            // Indian mobile: starts with 6-9, exactly 10 digits
            if (!preg_match('/^[6-9]\d{9}$/', $this->data[$field])) {
                $this->errors[$field] = "{$label} must be a valid 10-digit Indian mobile number.";
            }
        }
        return $this;
    }

    public function pincode(string $field, string $label = 'PIN Code'): self {
        if (!isset($this->errors[$field]) && $this->data[$field] !== '') {
            if (!preg_match('/^\d{6}$/', $this->data[$field])) {
                $this->errors[$field] = "{$label} must be a valid 6-digit PIN code.";
            }
        }
        return $this;
    }

    public function onlyAlpha(string $field, string $label): self {
        if (!isset($this->errors[$field]) && $this->data[$field] !== '') {
            if (!preg_match('/^[a-zA-Z\s\'\-]+$/', $this->data[$field])) {
                $this->errors[$field] = "{$label} must contain letters only.";
            }
        }
        return $this;
    }

    public function numeric(string $field, string $label): self {
        if (!isset($this->errors[$field]) && $this->data[$field] !== '') {
            if (!is_numeric($this->data[$field])) {
                $this->errors[$field] = "{$label} must be a number.";
            }
        }
        return $this;
    }

    public function min(string $field, float $minVal, string $label): self {
        if (!isset($this->errors[$field]) && $this->data[$field] !== '') {
            if ((float)$this->data[$field] < $minVal) {
                $this->errors[$field] = "{$label} must be at least {$minVal}.";
            }
        }
        return $this;
    }

    public function checkbox(string $field, string $label): self {
        // Checkboxes are not in POST if unchecked
        if (!isset($_POST[$field]) || $_POST[$field] !== '1') {
            $this->errors[$field] = "You must agree to {$label}.";
        }
        return $this;
    }

    public function cardNumber(string $field): self {
        if (!isset($this->errors[$field])) {
            $number = preg_replace('/\s+/', '', $this->data[$field]);
            if (!preg_match('/^\d{16}$/', $number)) {
                $this->errors[$field] = 'Card number must be exactly 16 digits.';
                return $this;
            }
            // Luhn algorithm
            $sum = 0;
            $alternate = false;
            for ($i = strlen($number) - 1; $i >= 0; $i--) {
                $n = (int)$number[$i];
                if ($alternate) {
                    $n *= 2;
                    if ($n > 9) $n -= 9;
                }
                $sum += $n;
                $alternate = !$alternate;
            }
            if ($sum % 10 !== 0) {
                $this->errors[$field] = 'Invalid card number (Luhn check failed).';
            }
        }
        return $this;
    }

    public function cardExpiry(string $field): self {
        if (!isset($this->errors[$field]) && $this->data[$field] !== '') {
            if (!preg_match('/^(0[1-9]|1[0-2])\/\d{2}$/', $this->data[$field])) {
                $this->errors[$field] = 'Expiry must be in MM/YY format.';
                return $this;
            }
            [$month, $year] = explode('/', $this->data[$field]);
            $expiry = \DateTime::createFromFormat('m/y', "{$month}/{$year}");
            $expiry->modify('last day of this month');
            if ($expiry < new \DateTime()) {
                $this->errors[$field] = 'Your card has expired.';
            }
        }
        return $this;
    }

    public function cvv(string $field): self {
        if (!isset($this->errors[$field]) && $this->data[$field] !== '') {
            if (!preg_match('/^\d{3,4}$/', $this->data[$field])) {
                $this->errors[$field] = 'CVV must be 3 or 4 digits.';
            }
        }
        return $this;
    }

    // ── Results ───────────────────────────────────────────────────────────────

    public function passes(): bool {
        return empty($this->errors);
    }

    public function fails(): bool {
        return !empty($this->errors);
    }

    public function errors(): array {
        return $this->errors;
    }

    public function firstError(string $field): string {
        return $this->errors[$field] ?? '';
    }

    public function hasError(string $field): bool {
        return isset($this->errors[$field]);
    }
}
