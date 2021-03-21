<?php

use Flipp\Client\Tests\Mocks\FlippClient;

use function PHPUnit\Framework\assertEquals;

test('query string parameters are correct', function () {
    $directUrl = FlippClient::make()
        ->setTemplate('template_id')
        ->setField('title', 'some text')
        ->setField('description', 'some other text')
        ->getDirectUrl();

    $urlParts = parse_url($directUrl);

    assertEquals('/template_id.png', $urlParts['path']);

    parse_str($urlParts['query'], $query);
    assertEquals('some text', $query['title']);
    assertEquals('some other text', $query['description']);
});

test('signed url parameters is correct', function () {
    $signedUrl = FlippClient::make()
        ->setTemplate('template_id')
        ->setField('title', 'some text')
        ->getSignedUrl();

    $urlParts = parse_url($signedUrl);

    assertEquals('/template_id.png', $urlParts['path']);

    parse_str($urlParts['query'], $query);

    $fields = json_decode(base64_decode($query['v']), true);
    assertEquals('some text', $fields['title']);
});

test('signed url signature is correct', function () {
    $signedUrl = FlippClient::make('api_key')
        ->setTemplate('template_id')
        ->setField('title', 'some text')
        ->getSignedUrl();

    $urlParts = parse_url($signedUrl);
    parse_str($urlParts['query'], $query);
    $signature = $query['s'];

    $encodedQueryString = base64_encode(json_encode([
        'title' => 'some text',
    ]));

    assertEquals(
        hash_hmac('sha256', 'template_id' . $encodedQueryString, 'api_key'),
        $signature
    );
});
