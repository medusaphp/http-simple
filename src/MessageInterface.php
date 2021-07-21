<?php declare(strict_types = 1);
namespace Medusa\Http\Simple;

/**
 * Class MessageInterface
 * @package medusa/http-simple
 * @author  Pascal Schnell <pascal.schnell@getmedusa.org>
 */
interface MessageInterface {

    public function getProtocolVersion(): string;

    public function addHeaders(array $headers): static;

    /**
     * @param bool $flattened
     * @return array
     */
    public function getHeaders(bool $flattened = false): array;

    /**
     * @return array|string|null
     */
    public function getBody(): array|string|null;

    /**
     * @return bool
     */
    public function hasBody(): bool;

    public function getParsedBody(): mixed;

    public function getHeader(string $name): array;

    public function removeHeader(string $headerName): static;

    public function removeHeaderValue(string $headerName, string $valueName): static;

    public function hasHeader(string $name): bool;
    
    public function withBody(array|string|null $body): static;

    public function withHeader(string $headerNameOrHeader, ?string $headerValue = null): static;

}
