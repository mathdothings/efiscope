<?php

namespace App\DTOs\FormSubmission;

/**
 * Data Transfer Object (DTO) for submitting tax declaration forms.
 * 
 * Encapsulates all required data for tax declaration submissions, ensuring type safety and immutability.
 * Use `fromArray()` to safely instantiate from raw input arrays.
 * 
 */
final class SubmitDTO
{
    /**
     * @param string $taxType The type of tax being declared (e.g., "VAT", "Income").
     * @param string $session Session identifier for the submission.
     * @param string $jsSession Client-side JavaScript session ID (if applicable).
     * @param array $user User data associated with the submission (e.g., ID, name).
     * @param bool $ieEmit Whether the submission is for an IE (Institutional Entity).
     * @param string $contribuitionType Type of contribution (e.g., "Annual", "Quarterly").
     * @param string $dateStart Start date of the tax period (format: YYYY-MM-DD).
     * @param string $dateEnd End date of the tax period (format: YYYY-MM-DD).
     * @param array $keysList List of document keys/references included in the submission.
     * @param int $start Pagination or batch starting index (if applicable).
     */
    public function __construct(
        public readonly string $taxType,
        public readonly string $session,
        public readonly string $jsSession,
        public readonly string $user,
        public readonly string $ieEmit,
        public readonly string $contribuitionType,
        public readonly string $dateStart,
        public readonly string $dateEnd,
        public readonly array $keysList,
        public readonly int $start
    ) {}

    /**
     * Creates a DTO instance from a raw input array.
     * 
     * Handles input validation, default values, and key normalization (e.g., hyphenated keys).
     * 
     * @param array $data Input data (typically from $_POST or API payload).
     * @return self
     * @throws \InvalidArgumentException If required fields (e.g., `taxType`) are missing.
     */
    public static function create(array $data): self
    {
        return new self(
            taxType: $data['taxType'] ?? throw new \InvalidArgumentException('Missing taxType'),
            session: $data['session'] ?? '',
            jsSession: $data['jsSession'] ?? '',
            user: $data['user'] ?? [],
            ieEmit: $data['ieEmit'] ?? '',
            contribuitionType: $data['contribuition-type'] ?? '',
            dateStart: $data['dateStart'] ?? '',
            dateEnd: $data['dateEnd'] ?? '',
            keysList: $data['keysList'] ?? [],
            start: (int) ($data['start'] ?? 0)
        );
    }
}
