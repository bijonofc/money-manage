<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\TestMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class TestMailController extends Controller
{
    /**
     * Send test email to bijon.ofc2021@gmail.com via the default queue or synchronously.
     */
    public function send(Request $request)
    {
        $recipient = $request->input('email', 'bijon.clan@gmail.com');
        $sync = $request->boolean('sync', false);

        try {
            if ($sync) {
                // Send immediately (synchronously) using sendNow to test SMTP credentials directly
                Mail::to($recipient)->sendNow(new TestMail(
                    title: 'Money Manage SMTP Test (Sync)',
                    bodyText: 'This test email was sent synchronously to verify your Brevo SMTP credentials.'
                ));

                return response()->json([
                    'status' => true,
                    'mode' => 'synchronous',
                    'recipient' => $recipient,
                    'message' => "Test email successfully sent directly via SMTP to {$recipient}.",
                    'mailer' => config('mail.default'),
                    'host' => config('mail.mailers.smtp.host'),
                ]);
            }

            // Normal mode: Dispatch to the default queue
            Mail::to($recipient)->queue(new TestMail());

            $pendingJobs = DB::table('jobs')->where('queue', 'default')->count();

            return response()->json([
                'status' => true,
                'mode' => 'queued',
                'queue' => 'default',
                'recipient' => $recipient,
                'message' => "Test email has been queued on the 'default' queue for {$recipient}.",
                'pending_jobs_in_default_queue' => $pendingJobs,
                'mailer' => config('mail.default'),
                'instruction' => 'To process queued jobs, run: php artisan queue:work --queue=default',
                'sync_tip' => 'To test sending immediately without queue, visit this route with ?sync=1',
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => false,
                'error' => $e->getMessage(),
                'trace_summary' => $e->getFile().':'.$e->getLine(),
            ], 500);
        }
    }
}
