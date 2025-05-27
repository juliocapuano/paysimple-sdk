<?php

namespace PaySimple\V4\Core;

class PaySimpleException extends \Exception
{
    protected array $errors = [];

    public function __construct($message = "", array $errors = [], int $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->errors = $errors;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Creates a PaySimpleException from the API client's processed response data and meta information.
     *
     * @param array $response_data The 'data' part of the API client's response, typically ['errors' => ['message1', ...]].
     * @param object $response_meta The 'meta' part of the API client's response.
     * @return self
     */
    public static function fromApiResponse(array $response_data, object $response_meta): self
    {
        $errorMessages = $response_data['errors'] ?? ['Unknown API error.'];
        $message = implode('; ', $errorMessages);
        
        $httpStatusCode = isset($response_meta->HttpStatus) ? (int)$response_meta->HttpStatus : 0;

        // Constructor: __construct($message = "", array $errors = [], int $code = 0, \Throwable $previous = null)
        return new self($message, $errorMessages, $httpStatusCode);
    }
}
