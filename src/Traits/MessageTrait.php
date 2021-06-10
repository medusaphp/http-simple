<?php declare(strict_types = 1);
namespace Medusa\Http\Simple\Traits;

use JsonException;
use function array_filter;
use function array_map;
use function explode;
use function implode;
use function in_array;
use function is_int;
use function is_string;
use function json_decode;
use function str_contains;
use function stripos;
use function strtolower;
use const JSON_THROW_ON_ERROR;

trait MessageTrait {

    private string            $uri           = '';
    private null|string|array $body          = null;
    private string            $method;
    private string            $remoteAddress;
    private array             $parsedHeaders = [];
    private mixed             $parsedBody;
    private bool              $bodyIsParsed  = false;

    /**
     * @return array|string|null
     */
    public function getBody(): array|string|null {
        return $this->body;
    }

    /**
     * @return string
     */
    public function getMethod(): string {
        return $this->method;
    }

    /**
     * @return bool
     */
    public function hasBody(): bool {
        return $this->body !== null;
    }

    public function removeHeader(string $headerName): static {
        unset($this->parsedHeaders[$headerName]);
        return $this;
    }

    public function removeHeaderValue(string $headerName, string $valueName): static {
        $this->parsedHeaders[$headerName] = array_filter(
            $this->parsedHeaders[$headerName] ?? [],
            fn(string $header) => stripos($header, 'boundary=') === false
        );

        return $this;
    }

    public function hasHeader(string $name): bool {
        return ($this->parsedHeaders[strtolower($name)] ?? null) !== null;
    }

    /**
     * @return string
     */
    public function getRemoteAddress(): string {
        return $this->remoteAddress;
    }

    /**
     * @return string
     */
    public function getUri(): string {
        return $this->uri;
    }

    /**
     * Set Uri
     * @param string $uri
     * @return self
     */
    public function setUri(string $uri): static {
        $this->uri = $uri;
        return $this;
    }

    /**
     * @param bool $flattened
     * @return array
     */
    public function getHeaders(bool $flattened = false): array {
        if ($flattened) {
            $headers = [];
            foreach ($this->parsedHeaders as $name => $value) {
                $headers[] = $name . ':' . implode(';', $value);
            }

            return $headers;
        }
        return $this->parsedHeaders;
    }

    /**
     * @param array $headers
     * @return self
     */
    public function addHeaders(array $headers): static {

        foreach ($headers as $name => $value) {

            if (is_int($name)) {
                if (!str_contains($value, ':')) {
                    continue;
                }
                [$name, $value] = explode(':', $value);
            }

            if (is_string($value)) {
                $value = array_map('trim', explode(';', $value));
            }
            $this->parsedHeaders[$name] = $value;
        }

        return $this;
    }

    /**
     * @return mixed
     * @throws JsonException
     */
    public function getParsedBody(): mixed {

        if ($this->bodyIsParsed) {
            return $this->parsedBody;
        }

        $this->bodyIsParsed = true;

        if (in_array('application/json', $this->getHeader('Content-Type'))) {
            $this->parsedBody = json_decode($this->body, true, 512, JSON_THROW_ON_ERROR);
        } else {
            $this->parsedBody = $this->body;
        }

        return $this->parsedBody;
    }

    public function getHeader(string $name): array {
        return $this->parsedHeaders[$name] ?? [];
    }
}
