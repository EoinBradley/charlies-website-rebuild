<?php

namespace App\Http\Handlers;

use Laminas\Diactoros\Response\HtmlResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class AppHandler implements RequestHandlerInterface
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return new HtmlResponse(<<< html
            <!DOCTYPE html>
            <html lang="en">
                <head>
                    <title>Charlies Bar Cork</title>
                    <link href="/assets/app.css" rel="stylesheet" type="text/css">
                </head>
                <body>
                    <div id="app"></div>
                    <script src="/assets/app.js" type="application/javascript"></script>
                </body>
            </html>
        html);
    }
}