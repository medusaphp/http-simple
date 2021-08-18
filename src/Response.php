<?php declare(strict_types = 1);
namespace Medusa\Http\Simple;

use Medusa\Http\Simple\Traits\MessageTrait;
use function explode;
use function preg_match;

/**
 * Class Response
 * @package medusa/http-simple
 * @author  Pascal Schnell <pascal.schnell@getmedusa.org>
 */
class Response implements ResponseInterface {

    use MessageTrait;

    private const STATUS_PHRASES = [
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-status',
        208 => 'Already Reported',
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        306 => 'Switch Proxy',
        307 => 'Temporary Redirect',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Time-out',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Large',
        415 => 'Unsupported Media Type',
        416 => 'Requested range not satisfiable',
        417 => 'Expectation Failed',
        418 => 'I\'m a teapot',
        422 => 'Unprocessable Entity',
        423 => 'Locked',
        424 => 'Failed Dependency',
        425 => 'Unordered Collection',
        426 => 'Upgrade Required',
        428 => 'Precondition Required',
        429 => 'Too Many Requests',
        431 => 'Request Header Fields Too Large',
        451 => 'Unavailable For Legal Reasons',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Time-out',
        505 => 'HTTP Version not supported',
        506 => 'Variant Also Negotiates',
        507 => 'Insufficient Storage',
        508 => 'Loop Detected',
        511 => 'Network Authentication Required',
    ];

    private string $reasonPhrase;

    /**
     * Response constructor.
     * @param array  $headers
     * @param string $body
     * @param int    $statusCode
     */
    public function __construct(
        array $headers,
        string $body,
        private int $statusCode = 200,
        ?string $reasonPhrase = null,
        string $protocolVersion = 'HTTP/1.1'
    ) {
        $this->reasonPhrase = $reasonPhrase ?? self::STATUS_PHRASES[$this->statusCode] ?? '';
        $this->protocolVersion = $protocolVersion;
        $this->addHeaders($headers);
        $this->body = $body;
    }

    /**
     * @param string   $rawResponse
     * @param int|null $statusCode
     * @return static
     */
    public static function createFromRawResponse(string $rawResponse, ?int $statusCode = null): self {

        [$headers, $body] = explode("\r\n\r\n", $rawResponse, 2);

        $headers = explode("\r\n", $headers);

        if (preg_match('/(\d{3})/', $headers[0], $match)) {

            $headerStatusCode = (int)$match[1];
            if (in_array($headerStatusCode, [100, 101, 102, 103])) {
                return self::createFromRawResponse($body, $statusCode);
            }
        }

        if ($statusCode === null && !empty($headerStatusCode)) {
            $statusCode = $headerStatusCode;
        } elseif ($statusCode === null) {
            $statusCode = 200;
        }

        return new static($headers, $body, $statusCode);
    }

    /**
     * @return string
     */
    public function getReasonPhrase(): string {
        return $this->reasonPhrase;
    }

    /**
     * @return int
     */
    public function getStatusCode(): int {
        return $this->statusCode;
    }

    /**
     * @param int         $statusCode
     * @param string|null $phrase
     * @return $this
     */
    public function setStatus(int $statusCode, ?string $phrase = null): static {
        $this->statusCode = $statusCode;
        $this->reasonPhrase = $phrase ?? self::STATUS_PHRASES[$statusCode] ?? '';
        return $this;
    }

    /**
     * @param int         $statusCode
     * @param string|null $phrase
     * @return self
     */
    public function withStatus(int $statusCode, ?string $phrase = null): static {
        $reasonPhrase = $phrase ?? self::STATUS_PHRASES[$statusCode] ?? '';
        $self = clone $this;

        $self->statusCode = $statusCode;
        $self->reasonPhrase = $reasonPhrase;

        return $self;
    }
}
