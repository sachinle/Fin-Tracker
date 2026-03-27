<?php
// File: bootstrap/app.php
// Replace your existing bootstrap/app.php with this

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {

        // ── Force show real error details in ALL environments ─────────────
        // Remove this block once you've identified and fixed the production error
        $exceptions->render(function (\Throwable $e, Request $request) {
            $trace = collect($e->getTrace())
                ->take(8)
                ->map(fn($t) => ($t['file'] ?? '?') . ':' . ($t['line'] ?? '?'))
                ->implode('<br>');

            $html = '<!DOCTYPE html><html>
            <head>
                <title>Error Details</title>
                <style>
                    body { font-family: monospace; background: #0b0f1a; color: #f0f4ff; padding: 40px; }
                    .box { background: #161d2e; border: 1px solid #f87171; border-radius: 12px; padding: 28px; max-width: 900px; }
                    .label { color: #f87171; font-size: 11px; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 6px; }
                    .value { color: #f0f4ff; font-size: 15px; font-weight: 600; margin-bottom: 20px; word-break: break-all; }
                    .trace { color: #6b7a99; font-size: 12px; line-height: 1.8; }
                    .badge { display: inline-block; background: #f87171; color: #0b0f1a; border-radius: 6px;
                             padding: 3px 10px; font-size: 12px; font-weight: 700; margin-bottom: 20px; }
                    .warn { color: #fbbf24; font-size: 11px; margin-top: 24px; }
                </style>
            </head>
            <body>
                <div class="box">
                    <div class="badge">500 — Real Error Details</div>

                    <div class="label">Exception Class</div>
                    <div class="value">' . get_class($e) . '</div>

                    <div class="label">Message</div>
                    <div class="value">' . htmlspecialchars($e->getMessage()) . '</div>

                    <div class="label">File</div>
                    <div class="value">' . htmlspecialchars($e->getFile()) . ' &nbsp;(line ' . $e->getLine() . ')</div>

                    <div class="label">Stack Trace (top 8 frames)</div>
                    <div class="trace">' . $trace . '</div>

                    <div class="warn">⚠️ Remove this error handler from bootstrap/app.php once the issue is fixed.</div>
                </div>
            </body></html>';

            return response($html, 500);
        });
        // ── End force error display ───────────────────────────────────────

    })->create();