<?php

namespace App\Services\StoryBlok\Redirects;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\TooManyRedirectsException;
use Illuminate\Support\Facades\Log;

class Redirects
{
    public function __construct(
        public readonly Client $client,
        protected array $rules = [],
    ) {}

    public static function fromNginxConfig(string $config): self
    {
        $rules = [];
        $lines = explode(PHP_EOL, $config);
        $pattern = '/^rewrite\s+(\S+)\s+(\S+)\s*(permanent)?\s*;/';
        foreach ($lines as $line) {
            if (preg_match($pattern, $line, $matches)) {
                $rules[$matches[1]] = $matches[2];
            }
        }
        return new self(
            new Client([
                'base_uri' => 'https://uatmy.uea.ac.uk',
            ]),
            $rules,
        );
    }

    public function validate(): void
    {
        foreach ($this->rules as $from => $to) {
            $url = trim($from, '^$');
            try {
                $response = $this->client->head($url, [
                    'allow_redirects' => ['track_redirects' => true],
                    'http_errors' => false,
                ]);
            } catch (TooManyRedirectsException) {
                Log::warning(sprintf('Error: %s Redirect loop...', $url));
                continue;
            } catch (ConnectException) {
                Log::warning(sprintf('Error: %s Could not connect to host...', $url));
                continue;
            }

            if ($history = $response->getHeader('X-Guzzle-Redirect-History')) {
                $dest = array_pop($history);
//                $history = array_filter($history, function ($item) {
//                    if (parse_url($item, PHP_URL_SCHEME) === 'http') {
//                        return false;
//                    }
//                    return true;
//                });
                if (count($history) > 0) {
//                    Log::warning(sprintf('Info: %s Multiple redirects', $url));
//                    print_r([...$history, $dest]);
                }
            } else {
                Log::warning(sprintf('Error: %s Didn\'t redirect...', $url));
                continue;
            }

            $host = parse_url($to, PHP_URL_HOST) ?? 'uatmy.uea.ac.uk';
            $internal = $host === 'uatmy.uea.ac.uk';
            if ($internal ? parse_url($dest, PHP_URL_PATH) !== parse_url($to, PHP_URL_PATH) : $dest !== $to) {
                if (count($history) > 0) {
                    foreach ($history as $item) {
                        if (parse_url($item, PHP_URL_PATH) === parse_url($to, PHP_URL_PATH)) {
//                            Log::debug(sprintf('Success %s -> %s (intermediate location)', $url, $dest));
                            continue 2;
                        }
                    }
                }
                Log::warning(sprintf('Error %s '.PHP_EOL.'-> %s'.PHP_EOL.'!= %s', $url, $dest, $to));
                print_r([...$history, $dest]);
                continue;
//                throw new Exception('URL '.$url.' does not redirect to expected location '.$to);
            }

            if (! $internal && $response->getStatusCode() !== 200) {
                Log::warning(sprintf('Error %s [%s]', $url, $response->getStatusCode()));
                continue;
//                throw new Exception('Invalid status code for URL: '.$url);
            }
//            Log::debug(sprintf('Success %s -> %s', $url, $dest));
        }
    }
}
