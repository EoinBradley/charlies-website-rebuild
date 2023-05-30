<?php

namespace App\Http\Handlers;

use App\Repositories\SiteConfigRepository;
use Laminas\Diactoros\Response\HtmlResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class AppHandler implements RequestHandlerInterface
{
    public function __construct(private readonly SiteConfigRepository $siteConfigRepository)
    {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $homepageDescription = $this->siteConfigRepository->getHomepageDescriptionConfig();

        return new HtmlResponse(<<< html
            <!DOCTYPE html>
            <html lang="en">
                <head>
                    <title>Charlie's Bar Cork</title>
                    <meta charset="utf-8">
                    <meta http-equiv="X-UA-Compatible" content="IE=edge">
                    <meta name="viewport" content="width=device-width, initial-scale=1">
                    <meta name="description" content="$homepageDescription->value">
                    <link href="/assets/app.css" rel="stylesheet" type="text/css">
                </head>
                <body>
                    <div id="app"></div>
                    <script>
                        window.data = {
                            homepage_description: `$homepageDescription->value`,
                        };
                    </script>
                    <script src="/assets/app.js" type="application/javascript"></script>
                </body>
            </html>
        html);
    }
}
