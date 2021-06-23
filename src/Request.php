<?php declare(strict_types = 1);
namespace Medusa\Http\Simple;

use Medusa\Http\Simple\Traits\MessageTrait;
use function explode;
use function file_get_contents;
use function getallheaders;
use function implode;
use function in_array;
use function is_string;
use function json_encode;
use function Medusa\Http\getRemoteAddress;
use function Medusa\Http\isSsl;
use function strlen;
use function strpos;
use function strtolower;
use function substr;

/**
 * Class Request
 * @package medusa/http-simple
 * @author  Pascal Schnell <pascal.schnell@getmedusa.org>
 */
class Request implements RequestInterface {

    use MessageTrait;

    private ?Uri $uri = null;

    public function __construct(
        array $headers,
        null|string|array $body,
        string $method,
        string|UriInterface $uri,
        string $remoteAddress,
        string $protocolVersion = 'HTTP/1.1'
    ) {

        $this->protocolVersion = $protocolVersion;
        $this->body = $body;
        $this->setUri($uri);
        $this->method = $method;
        $this->remoteAddress = $remoteAddress;
        $this->addHeaders($headers);
    }

    public static function createFromGlobals(): ?self {

        $body = null;

        switch ($_SERVER['REQUEST_METHOD']) {
            case 'DELETE':
            case 'PATCH':
            case 'POST':
            case 'PUT':
                $contentType = strtolower($_SERVER['HTTP_CONTENT_TYPE']);
                $usePostArray = strpos($contentType, 'multipart/form-data') !== false
                    || strpos($contentType, 'application/x-www-form-urlencoded') !== false;
                if ($usePostArray) {
                    $body = $_POST;
                } else {
                    $body = file_get_contents('php://input');
                }
        }

        $remoteAddress = (string)getRemoteAddress();
        if (substr($remoteAddress, 0, 5) === 'unix:') {
            $remoteAddress = '127.0.0.1';
        }

        return new static(
            getallheaders(),
            $body,
            $_SERVER['REQUEST_METHOD'],
            self::createUriFromGlobals(),
            $remoteAddress,
            $_SERVER['SERVER_PROTOCOL']
        );
    }

    /**
     * Create a Uri with values from $_SERVER.
     * @return UriInterface
     */
    public static function createUriFromGlobals(): UriInterface {
        $scheme = isSsl($_SERVER) ? 'https:' : 'http:';
        $host = $_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? $_SERVER['SERVER_ADDR'] ?? '';
        $requestUriParts = explode('?', $_SERVER['REQUEST_URI'] ?? '', 2);
        $path = $requestUriParts[0];
        $query = $requestUriParts[1] ?? $_SERVER['QUERY_STRING'] ?? '';
        $uri = $scheme . '//' . $host . $path . ($query ? '?' . $query : '');
        return new Uri($uri);
    }

    public function __clone(): void {
        $uri = (string)$this->getUri();
        $this->setUri($uri);
    }

    /**
     * @return UriInterface
     */
    public function getUri(): UriInterface {
        return $this->uri ??= new Uri();
    }

    /**
     * Set Uri
     * @param string|UriInterface $uri
     * @return self
     */
    public function setUri(string|UriInterface $uri): static {
        $this->uri = $uri instanceof Uri ? $uri : new Uri($uri);
        return $this;
    }

    /**
     * Set Uri
     * @param string|UriInterface $uri
     * @return self
     */
    public function withUri(string|UriInterface $uri): static {
        $self = clone $this;
        $self->setUri($uri);
        return $self;
    }

    /**
     * @return string
     */
    public function __toString(): string {
        return $this->toString();
    }

    /**
     * @return string
     */
    public function toString(): string {
        $headers = $this->getHeaders(true);
        $body = $this->body;

        if (!is_string($body)) {
            if (!$this->hasHeader('Content-Type')) {
                $body = json_encode($body);
                $headers[] = 'Content-Type: application/json';
            } elseif (in_array('application/json', $this->getHeader('Content-Type'))) {
                $body = json_encode($body);
            }
        }

        if (!$this->hasHeader('Content-Length')) {
            $headers[] = 'Content-Length: ' . strlen($body);
        }

        $host = $this->getUri()->getHost();
        $path = (string)(new Uri())
            ->setPath($this->getUri()->getPath())
            ->setQuery($this->getUri()->getQuery());

        $method = $this->getMethod();
        $protocol = $this->getProtocolVersion();
        $headersString = implode("\r\n", $headers);
        $request = "{$method} {$path} {$protocol}\r\n";
        $request .= "Host: {$host}\r\n";
        $request .= "{$headersString}\r\n\r\n";
        $request .= $body;

        return $request;
    }

    /**
     * @param string $method
     * @return $this
     */
    public function setMethod(string $method): static {
        $this->method = $method;
        return $this;
    }

    /**
     * @param string $method
     * @return Request
     */
    public function withMethod(string $method): static {
        $self = clone $this;
        $self->method = $method;

        return $self;
    }
}
