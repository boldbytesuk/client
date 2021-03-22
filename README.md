<p align="center"><img src="/art/cover.png" alt="Flipp social media card "></p>

# Flipp – API for image generation

Flipp is a service that lets you create unique preview images. Generate social media visuals through REST API, or using Signed URLs.

The package is an official wrapper for Flipp API. To start using it, you'll need to [create your free](https://useflipp.com/register) account and get the [API key](https://useflipp.com/settings/profile/api).

## Installation

```bash
composer require useflipp/client
```

## Usage

### Generate image synchronously

Here's a quick example of how to generate an image synchronously using API.

```php
use Flipp\Client\Client as FlippClient;

FlippClient::make($apiKey)
  ->setTemplate($templateId)
  ->setField('title', 'Dynamic Value')
  ->getImage(); // returns generated image URL
```

Please note that the image generation process may take up to 2-3s depending on the template complexity and connection quality.

### Signed URLs

In case if you don't want to perform requests synchronously, you can generate signed URLs with encoded parameters. This doesn't require the API call, and the images will be generated during the first HTTP call (page visit).

```php
use Flipp\Client\Client as FlippClient;

FlippClient::make($apiKey)
  ->setTemplate($templateId)
  ->setField('title', 'Dynamic Value')
  ->getSignedUrl(); // returns signed URL
```

### Dynamic values and styles

You can set dynamic fields values as well as their styles. This option is only available for API calls.

```php
FlippClient::make($apiKey)
  ->setTemplate($templateId)
  ->setField('title', 'Dynamic Value', [
    'color' => '#ff00ff',
  ])
  ->setField('square', null, [
    'backgroundColor' => '#aa3f3f'
  ]);
```

### Fallback URLs

With Flipp, you can also create Fallback URLs with social media previews for the links you can't integrate Flipp directly.

```php
FlippClient::make($apiKey)
  ->setTemplate($templateId)
  ->setField('title', 'Meta Title') // optional
  ->setField('description', 'Meta Description') // optional
  ->getLink($url); // returns short link
```

This method returns a short link that might be shared on social media. You can specify custom title and description which will be used in the template as well as for the metadata for the generated page. If you won't provide the data, those values will be fetched from the provided external URL.