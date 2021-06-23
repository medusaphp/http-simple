<?php declare(strict_types = 1);
namespace Medusa\Http\Simple;

/**
 * Class Response
 * @package medusa/http-simple
 * @author  Pascal Schnell <pascal.schnell@getmedusa.org>
 */
interface ResponseInterface extends MessageInterface {

    /**
     * @return string
     */
    public function getReasonPhrase(): string;

    /**
     * @return int
     */
    public function getStatusCode(): int;

    /**
     * @param int         $statusCode
     * @param string|null $phrase
     * @return $this
     */
    public function setStatus(int $statusCode, ?string $phrase = null): static;

    /**
     * @param int         $statusCode
     * @param string|null $phrase
     * @return \Medusa\Http\Simple\Response
     */
    public function withStatus(int $statusCode, ?string $phrase = null): static;
}