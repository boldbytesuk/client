<?php

namespace Flipp\Client;

use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class Client
{
    protected string $apiEndpoint = 'https://useflipp.com/api/v1/';
    protected string $signedUrlBase = 'https://s.useflipp.com/';
    protected string $directUrlBase = 'https://i.useflipp.com/';
    protected ?string $token;
    protected string $templateId;
    protected Collection $fields;

    public function __construct(?string $token = null)
    {
        $this->token = $token;
        $this->fields = new Collection;
    }

    public static function make(?string $token = null): self
    {
        return new self($token);
    }

    public function setTemplate(string $templateId): self
    {
        $this->templateId = $templateId;

        return $this;
    }

    public function setField(string $name, ?string $value, ?array $styles = null): self
    {
        $this->fields->push(
            new Field($name, $value, $styles)
        );

        return $this;
    }

    /**
     * Get Signed URL of the image
     * The image will be generated on the first HTTP request
     * 
     * @return string 
     */
    public function getSignedUrl(): string
    {
        $query = base64_encode(json_encode($this->buildQuery()));
        $signature = hash_hmac('sha256', $this->templateId . $query, $this->token);

        return $this->signedUrlBase . $this->templateId . '.png?' . http_build_query(['s' => $signature, 'v' => $query]);
    }

    /**
     * Get QueryString URL for the image
     * The image will be generated on the first HTTP request
     * 
     * @return string 
     */
    public function getDirectUrl(): string
    {
        return $this->directUrlBase . $this->templateId . '.png?' . http_build_query($this->buildQuery());
    }

    /**
     * Generate an image synchronously
     * 
     * @return string 
     * @throws \Exception 
     */
    public function getImage(): string
    {
        $response = Http::withHeaders([
            'Authorization' => "Bearer {$this->token}",
        ])
            ->asJson()
            ->acceptJson()
            ->post($this->apiEndpoint . 'images', [
                'template_id' => $this->templateId,
                'values' => $this->getValues(),
            ]);

        if ($response->failed()) {
            if ($response->clientError()) {
                throw new Exception($response->json('message'));
            }

            throw new Exception('Something went wrong');
        }

        return $response->json('image_url');
    }

    /**
     * Get a fallback link with a preview for the specified URL
     * 
     * @param string $url 
     * @return string 
     * @throws \Exception 
     */
    public function getLink(string $url): string
    {
        $response = Http::withHeaders([
            'Authorization' => "Bearer {$this->token}",
        ])
            ->asJson()
            ->acceptJson()
            ->post($this->apiEndpoint . 'links', [
                'template_id' => $this->templateId,
                'url' => $url,
                'values' => $this->getValues(),
            ]);

        if ($response->failed()) {
            if ($response->clientError()) {
                throw new Exception($response->json('message'));
            }

            throw new Exception('Something went wrong');
        }

        return $response->json('fallback_url');
    }

    protected function getValues(): array
    {
        return $this->fields
            ->map(fn (Field $field) => $field->getDefinition())
            ->toArray();
    }

    protected function buildQuery(): array
    {
        return $this->fields
            ->mapWithKeys(fn (Field $field) => [$field->name => $field->value])
            ->toArray();
    }
}
