<?php declare(strict_types = 1);
namespace Medusa\Http\Simple;

use Medusa\Http\Simple\Traits\MessageTrait;
use function explode;
use function Medusa\DevTools\dd;
use function preg_match;

/**
 * Class Response
 * @package medusa/http-simple
 * @author  Pascal Schnell <pascal.schnell@getmedusa.org>
 */
class Response implements MessageInterface {

    use MessageTrait;

    /**
     * Response constructor.
     * @param array  $headers
     * @param string $body
     * @param int    $statusCode
     */
    public function __construct(array $headers, string $body, private int $statusCode = 200, private string $reasonPhrase = 'OK', string $protocolVersion = 'HTTP/1.1') {
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
        if ($statusCode === null) {
            if (preg_match('/(\d{3})/', $headers[0], $match)) {
                $statusCode = (int)$match[1];
            } else {
                $statusCode = 200;
            }
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
}
