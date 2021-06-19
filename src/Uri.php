<?php declare(strict_types = 1);
namespace Medusa\Http\Simple;

use function http_build_query;
use function parse_str;
use function parse_url;
use const PHP_QUERY_RFC3986;

/**
 * Class Uri
 * @package medusa/http-simple
 * @author  Pascal Schnell <pascal.schnell@getmedusa.org>
 */
class Uri {

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

    public function __construct(string|Uri|null $uri = null) {

        if ($uri instanceof Uri) {
            $uri = (string)$uri;
        }

        if ($uri !== null) {
            $parts = parse_url($uri);
            $this->scheme = $parts['scheme'] ?? '';
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
     * @return Uri
     */
    public function setScheme(string $scheme): Uri {
        $this->scheme = $scheme;
        return $this;
    }

    /**
     * With Scheme
     * @param string $scheme
     * @return Uri
     */
    public function withScheme(string $scheme): Uri {
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
     * @return Uri
     */
    public function setHost(string $host): Uri {
        $this->host = $host;
        return $this;
    }

    /**
     * With Host
     * @param string $host
     * @return Uri
     */
    public function withHost(string $host): Uri {
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
     * @return Uri
     */
    public function setPath(string $path): Uri {
        $this->path = $path;
        return $this;
    }

    /**
     * With Path
     * @param string $path
     * @return Uri
     */
    public function withPath(string $path): Uri {
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
     * @return Uri
     */
    public function setQuery(array $query): Uri {
        $this->query = $query;
        return $this;
    }

    /**
     * With Query
     * @param array $query
     * @return Uri
     */
    public function withQuery(array $query): Uri {
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
     * @return Uri
     */
    public function setFragment(string $fragment): Uri {
        $this->fragment = $fragment;
        return $this;
    }

    /**
     * With Fragment
     * @param string $fragment
     * @return Uri
     */
    public function withFragment(string $fragment): Uri {
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
