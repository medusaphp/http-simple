<?php declare(strict_types = 1);
namespace Medusa\Http\Simple;

/**
 * Class Request
 * @package medusa/http-simple
 * @author  Pascal Schnell <pascal.schnell@getmedusa.org>
 */
interface RequestInterface extends MessageInterface {

    /**
     * @return UriInterface
     */
    public function getUri(): UriInterface;

    /**
     * Set Uri
     * @param string|UriInterface $uri
     * @return static
     */
    public function setUri(string|UriInterface $uri): static;

    /**
     * Set Uri
     * @param string|UriInterface $uri
     * @return static
     */
    public function withUri(string|UriInterface $uri): static;

    /**
     * @return string
     */
    public function __toString(): string;

    /**
     * @param string $method
     * @return $this
     */
    public function setMethod(string $method): static;

    /**
     * @param string $method
     * @return static
     */
    public function withMethod(string $method): static;
}