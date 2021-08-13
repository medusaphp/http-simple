<?php declare(strict_types = 1);
namespace Medusa\Http\Simple;

use CurlHandle;
use function curl_close;
use function curl_exec;
use function curl_getinfo;
use function curl_init;
use function curl_setopt;
use const CURLINFO_RESPONSE_CODE;
use const CURLOPT_COOKIESESSION;
use const CURLOPT_CUSTOMREQUEST;
use const CURLOPT_HEADER;
use const CURLOPT_HTTPHEADER;
use const CURLOPT_POSTFIELDS;
use const CURLOPT_RETURNTRANSFER;
use const CURLOPT_UNIX_SOCKET_PATH;
use const CURLOPT_URL;

/**
 * Class Curl
 * @package medusa/http-simple
 * @author  Pascal Schnell <pascal.schnell@getmedusa.org>
 */
class Curl {

    /** @var CurlHandle|resource */
    protected CurlHandle $curl;

    public function setSocketPath(string $path): Curl {
        curl_setopt($this->curl, CURLOPT_UNIX_SOCKET_PATH, $path);
        return $this;
    }

    /**
     * @param array|string|MessageInterface|null $postDataOrRequest
     * @return Response
     */
    public function send(array|string|null|MessageInterface $postDataOrRequest = null): Response {

        if ($postDataOrRequest instanceof MessageInterface) {
            return static::createForRequest($postDataOrRequest)->send();
        }

        if ($postDataOrRequest !== null) {
            curl_setopt($this->curl, CURLOPT_POSTFIELDS, $postDataOrRequest);
        }

        $response = curl_exec($this->curl);

        if (false === $response) {
            return new Response([
                                ], '', 500, 'Internal Server Error');
        }

        $headerLen = curl_getinfo($this->curl, CURLINFO_HEADER_SIZE);
        $responseCode = curl_getinfo($this->curl, CURLINFO_RESPONSE_CODE);
        return Response::createFromRawResponse($response, $responseCode, $headerLen);
    }

    public static function createForRequest(MessageInterface $request): static {

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $request->getUri());
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_COOKIESESSION, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $request->getMethod());

        if ($request->hasBody()) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $request->getBody());
        }

        $headers = $request->getHeaders(true);

        if (!$request->hasHeader('X-Forwarded-For') && $request->getRemoteAddress()) {
            $headers[] = 'X-Forwarded-For: ' . $request->getRemoteAddress();
        }

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $self = new static();
        $self->curl = $ch;
        return $self;
    }

    public function close(): void {
        curl_close($this->curl);
    }
}
