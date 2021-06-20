<?php declare(strict_types = 1);
namespace Medusa\Http\Simple;

use function http_build_query;
use function parse_str;
use function parse_url;
use function str_starts_with;
use const PHP_QUERY_RFC3986;

/**
 * Class Uri
 * @package medusa/http-simple
 * @author  Pascal Schnell <pascal.schnell@getmedusa.org>
 */
class Uri implements UriInterface {

    /** @var string */
    private string $scheme = '';
    /** @var string */
    private string $host = '';
    /** @var string */
    private string $path = '';
    /** @var array */
    private array $query = [];
    /** @var string */
    private string $fragment = '';

    public function __construct(string|UriInterface|null $uri = null) {

        if ($uri instanceof UriInterface) {
            $uri = (string)$uri;
        }

        if ($uri !== null) {
            $magicScheme = str_starts_with($uri, ':');

            if ($magicScheme) {
                $uri = 'http' . $uri;
            }

            $parts = parse_url($uri);
            $this->scheme = !$magicScheme ? ($parts['scheme'] ?? '') : '';
            $this->host = $parts['host'] ?? '';
            $this->path = $parts['path'] ?? '';
            parse_str($parts['query'] ?? '', $this->query);
            $this->fragment = $parts['fragment'] ?? '';
        }
    }

    /**
     * @return string
     */
    public function getScheme(): string {
        return $this->scheme;
    }

    /**
     * Set Scheme
     * @param string $scheme
     * @return UriInterface
     */
    public function setScheme(string $scheme): UriInterface {
        $this->scheme = $scheme;
        return $this;
    }

    /**
     * With Scheme
     * @param string $scheme
     * @return UriInterface
     */
    public function withScheme(string $scheme): UriInterface {
        $self = clone $this;
        $self->scheme = $scheme;
        return $self;
    }

    /**
     * @return string
     */
    public function getHost(): string {
        return $this->host;
    }

    /**
     * Set Host
     * @param string $host
     * @return UriInterface
     */
    public function setHost(string $host): UriInterface {
        $this->host = $host;
        return $this;
    }

    /**
     * With Host
     * @param string $host
     * @return UriInterface
     */
    public function withHost(string $host): UriInterface {
        $self = clone $this;
        $self->host = $host;
        return $self;
    }

    /**
     * @return string
     */
    public function getPath(): string {
        return $this->path;
    }

    /**
     * Set Path
     * @param string $path
     * @return UriInterface
     */
    public function setPath(string $path): UriInterface {
        $this->path = $path;
        return $this;
    }

    /**
     * With Path
     * @param string $path
     * @return UriInterface
     */
    public function withPath(string $path): UriInterface {
        $self = clone $this;
        $self->path = $path;
        return $self;
    }

    /**
     * @return array
     */
    public function getQuery(): array {
        return $this->query;
    }

    /**
     * Set Query
     * @param array $query
     * @return UriInterface
     */
    public function setQuery(array $query): UriInterface {
        $this->query = $query;
        return $this;
    }

    /**
     * With Query
     * @param array $query
     * @return UriInterface
     */
    public function withQuery(array $query): UriInterface {
        $self = clone $this;
        $self->query = $query;
        return $self;
    }

    /**
     * @return string
     */
    public function getFragment(): string {
        return $this->fragment;
    }

    /**
     * Set Fragment
     * @param string $fragment
     * @return UriInterface
     */
    public function setFragment(string $fragment): UriInterface {
        $this->fragment = $fragment;
        return $this;
    }

    /**
     * With Fragment
     * @param string $fragment
     * @return UriInterface
     */
    public function withFragment(string $fragment): UriInterface {
        $self = clone $this;
        $self->fragment = $fragment;
        return $self;
    }

    /**
     * @return string
     */
    public function __toString(): string {
        return $this->scheme . '://' . $this->host . $this->path .
            ($this->query ? '?' . http_build_query(
                    $this->query,
                    encoding_type: PHP_QUERY_RFC3986)
                : '')
            . ($this->fragment ? '#' . $this->fragment : '');
    }
}
