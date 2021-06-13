<?php declare(strict_types = 1);
namespace Medusa\Http\Simple;

use Medusa\Http\Simple\Traits\MessageTrait;
use function file_get_contents;
use function getallheaders;
use function implode;
use function in_array;
use function is_string;
use function json_encode;
use function Medusa\Http\getRemoteAddress;
use function parse_url;
use function strlen;
use function strpos;
use function strtolower;
use function substr;

/**
 * Class Request
 * @package medusa/http-simple
 * @author  Pascal Schnell <pascal.schnell@getmedusa.org>
 */
class Request implements MessageInterface {

    use MessageTrait;

    public function __construct(
        array $headers,
        null|string|array $body,
        string $method,
        string $uri,
        string $remoteAddress,
        string $protocolVersion = 'HTTP/1.1'
    ) {
        $this->protocolVersion = $protocolVersion;
        $this->body = $body;
        $this->uri = $uri;
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
            $_SERVER['REQUEST_URI'],
            $remoteAddress,
            $_SERVER['SERVER_PROTOCOL']
        );
    }

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

        $parts = parse_url($this->getUri());
        $host = $parts['host'] ?? '';
        $path = $parts['path'] ?? '/';
        $query = $parts['query'] ?? '';

        if ($query) {
            $path .= '?' . $query;
        }

        $method = $this->getMethod();
        $protocol = $this->getProtocolVersion();
        $headersString = implode("\r\n", $headers);
        $request = "{$method} {$path} {$protocol}\r\n";
        $request .= "Host: {$host}\r\n";
        $request .= "{$headersString}\r\n\r\n";
        $request .= $body;

        return $request;
    }
}
