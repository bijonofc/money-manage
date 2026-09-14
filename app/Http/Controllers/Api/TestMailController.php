<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\TestMail;
use appsbd\Libs\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Throwable;

class TestMailController extends Controller
{
    /**
     * Send test email synchronously or queued.
     */
    public function send(Request $request): JsonResponse
    {
        $recipient = $request->input('email', 'bijon.clan@gmail.com');
        $sync = $request->boolean('sync', false);
        $response = new ApiResponse;

        try {
            if ($sync) {
                Mail::to($recipient)->sendNow(new TestMail(
                    title: 'Money Manage SMTP Test (Sync)',
                    bodyText: 'This test email was sent synchronously to verify your SMTP credentials.'
                ));

                ApiResponse::addInfoArray("Test email successfully sent directly via SMTP to {$recipient}.");

                return $response->displayWithResponse(true, [
                    'mode' => 'synchronous',
                    'recipient' => $recipient,
                    'mailer' => config('mail.default'),
                    'host' => config('mail.mailers.smtp.host'),
                ]);
            }

            Mail::to($recipient)->queue(new TestMail);
            $pendingJobs = DB::table('jobs')->where('queue', 'default')->count();

            ApiResponse::addInfoArray("Test email has been queued on the 'default' queue for {$recipient}.");

            return $response->displayWithResponse(true, [
                'mode' => 'queued',
                'queue' => 'default',
                'recipient' => $recipient,
                'pending_jobs_in_default_queue' => $pendingJobs,
                'mailer' => config('mail.default'),
            ]);
        } catch (Throwable $e) {
            ApiResponse::addErrorArray($e->getMessage());

            return $response->displayWithResponse(false, [
                'trace_summary' => $e->getFile().':'.$e->getLine(),
            ], 500);
        }
    }
}
