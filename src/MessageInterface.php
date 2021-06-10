<?php declare(strict_types = 1);
namespace Medusa\Http\Simple;

/**
 * Class MessageInterface
 * @package medusa/http-simple
 * @author  Pascal Schnell <pascal.schnell@getmedusa.org>
 */
interface MessageInterface {

    public function addHeaders(array $headers): static;

    /**
     * Set Uri
     * @param string $uri
     * @return self
     */
    public function setUri(string $uri): static;

    /**
     * @return string
     */
    public function getUri(): string;

    /**
     * @return array
     */
    public function getHeaders(bool $flattened = false): array;

    /**
     * @return array|string|null
     */
    public function getBody(): array|string|null;

    /**
     * @return string
     */
    public function getMethod(): string;

    /**
     * @return bool
     */
    public function hasBody(): bool;

    public function getParsedBody(): mixed;

    public function getHeader(string $name): array;

    public function removeHeader(string $headerName): static;

    public function removeHeaderValue(string $headerName, string $valueName): static;

    public function hasHeader(string $name): bool;

    /**
     * @return string
     */
    public function getRemoteAddress(): string;
}