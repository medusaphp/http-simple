<?php declare(strict_types = 1);
namespace Medusa\Http\Simple;

/**
 * Class UriInterface
 * @package medusa/http-simple
 * @author  Pascal Schnell <pascal.schnell@getmedusa.org>
 */
interface UriInterface {

    /**
     * @return string
     */
    public function getScheme(): string;

    /**
     * Set Scheme
     * @param string $scheme
     * @return UriInterface
     */
    public function setScheme(string $scheme): UriInterface;

    /**
     * With Scheme
     * @param string $scheme
     * @return UriInterface
     */
    public function withScheme(string $scheme): UriInterface;

    /**
     * @return string
     */
    public function getHost(): string;

    /**
     * Set Host
     * @param string $host
     * @return UriInterface
     */
    public function setHost(string $host): UriInterface;

    /**
     * With Host
     * @param string $host
     * @return UriInterface
     */
    public function withHost(string $host): UriInterface;

    /**
     * @return string
     */
    public function getPath(): string;

    /**
     * Set Path
     * @param string $path
     * @return UriInterface
     */
    public function setPath(string $path): UriInterface;

    /**
     * With Path
     * @param string $path
     * @return UriInterface
     */
    public function withPath(string $path): UriInterface;

    /**
     * @return array
     */
    public function getQuery(): array;

    /**
     * Set Query
     * @param array $query
     * @return UriInterface
     */
    public function setQuery(array $query): UriInterface;

    /**
     * With Query
     * @param array $query
     * @return UriInterface
     */
    public function withQuery(array $query): UriInterface;

    /**
     * @return string
     */
    public function getFragment(): string;

    /**
     * Set Fragment
     * @param string $fragment
     * @return UriInterface
     */
    public function setFragment(string $fragment): UriInterface;

    /**
     * With Fragment
     * @param string $fragment
     * @return UriInterface
     */
    public function withFragment(string $fragment): UriInterface;

    public function getPort(): ?int;

    /**
     * @return int|null
     */
    public function setPort(int $port): UriInterface;

    /**
     * @param int $port
     * @return UriInterface
     */
    public function withPort(int $port): UriInterface;
}
