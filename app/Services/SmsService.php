<?php

namespace App\Services;

use App\Models\SmsLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService
{
    protected $senderId;
    protected $apiEndpoint;
    protected $apiKey;
    protected $testMode;

    public function __construct()
    {
        $this->apiKey = env('NEXTSMS_API_KEY', 'e079f42787951816f9aa2c29bb663f9e');
        $this->senderId = env('NEXTSMS_SENDER_ID', 'UniMessage');
        $this->testMode = env('NEXTSMS_TEST_MODE', false);

        // Set endpoint
        if ($this->testMode) {
            $this->apiEndpoint = 'https://messaging-service.co.tz/api/sms/v2/test/text/single';
        } else {
            $this->apiEndpoint = env('NEXTSMS_API_URL', 'https://messaging-service.co.tz/api/sms/v2/text/single');
        }
    }

    /**
     * Send SMS to a phone number
     */
    public function send($phone, $message, $type = 'general', $userId = null, $proposalId = null, $meetingId = null, $flash = 0, $reference = null)
    {
        try {
            // Format phone number using normalizePhoneNumber
            $formattedPhone = $this->normalizePhoneNumber($phone);

            // Create SMS log entry - only include proposal_id if it's valid
            $smsLogData = [
                'phone_number' => $formattedPhone,
                'message' => $message,
                'status' => 'pending',
                'type' => $type,
                'user_id' => $userId,
                'meeting_id' => $meetingId,
            ];

            // Only add proposal_id if it's a valid integer
            if ($proposalId && is_numeric($proposalId)) {
                $smsLogData['proposal_id'] = $proposalId;
            }

            $smsLog = SmsLog::create($smsLogData);

            // Generate reference if not provided
            if (is_null($reference)) {
                $reference = 'ssris_' . time();
            }

            // Prepare request data
            $data = [
                'from' => $this->senderId,
                'to' => $formattedPhone,
                'text' => $message,
                'flash' => $flash,
                'reference' => $reference,
            ];

            // Send request to NextSMS API with Bearer Token
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->timeout(30)->post($this->apiEndpoint, $data);

            // Update SMS log based on response
            if ($response->successful()) {
                $smsLog->update([
                    'status' => 'sent',
                    'api_response' => $response->body(),
                    'sent_at' => now(),
                ]);

                Log::info('SMS sent successfully', [
                    'phone' => $formattedPhone,
                    'message' => $message,
                    'response' => $response->body(),
                    'endpoint' => $this->apiEndpoint,
                ]);
                return [
                    'success' => true,
                    'message' => 'SMS sent successfully',
                    'response' => $response->body(),
                ];
            } else {
                $smsLog->update([
                    'status' => 'failed',
                    'api_response' => $response->body(),
                ]);

                Log::error('SMS sending failed', [
                    'phone' => $formattedPhone,
                    'message' => $message,
                    'response' => $response->body(),
                    'endpoint' => $this->apiEndpoint,
                ]);
                return [
                    'success' => false,
                    'message' => 'SMS sending failed',
                    'response' => $response->body(),
                ];
            }
        } catch (\Exception $e) {
            // Update SMS log to failed if it exists
            if (isset($smsLog)) {
                $smsLog->update([
                    'status' => 'failed',
                    'api_response' => $e->getMessage(),
                ]);
            }

            Log::error('SMS service error', [
                'phone' => $phone,
                'message' => $message,
                'error' => $e->getMessage(),
            ]);
            return [
                'success' => false,
                'message' => 'SMS service error: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Normalize phone number to NextSMS required format (255XXXXXXXXX)
     */
    protected function normalizePhoneNumber($phone)
    {
        // Step 1: Remove all non-numeric characters (spaces, dashes, etc.)
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // Step 2: If starts with local prefix '07' or '06', replace leading '0' with '255'
        if (strpos($phone, '07') === 0 || strpos($phone, '06') === 0) {
            $phone = '255' . substr($phone, 1);
        }

        // Step 3: If it already starts with 255 and is 12 digits long, leave untouched
        if (strlen($phone) === 12 && strpos($phone, '255') === 0) {
            return $phone;
        }

        // Step 4: If not starting with 255 after above steps, add 255
        if (strpos($phone, '255') !== 0) {
            $phone = '255' . $phone;
        }

        return $phone;
    }

    /**
     * Send document submission notification to supervisor
     */
    public function sendDocumentSubmissionNotification($student, $documentTitle, $documentType, $proposalId = null)
    {
        if (!$student->supervisor || !$student->supervisor->phone) {
            return ['success' => false, 'message' => 'Supervisor not found or no phone number'];
        }

        $message = "Hello Dr. {$student->supervisor->name}, {$student->name} has submitted a new  ({$documentTitle}). Please log in to the SSRIS system to review.";

        return $this->send($student->supervisor->phone, $message, 'document_submission', $student->supervisor_id, $proposalId);
    }

    public function sendDocumentApprovalNotification($student, $documentType, $proposalId = null)
    {
        if (!$student->phone) {
            return ['success' => false, 'message' => 'Student has no phone number'];
        }

        $message = "Hello {$student->name}, your {$documentType} has been Approved by your supervisor.";

        return $this->send($student->phone, $message, 'document_approval', $student->id, $proposalId);
    }

    public function sendDocumentRejectionNotification($student, $documentType, $proposalId = null)
    {
        if (!$student->phone) {
            return ['success' => false, 'message' => 'Student has no phone number'];
        }

        $message = "Hello {$student->name}, your {$documentType} has been Rejected by your supervisor.";

        return $this->send($student->phone, $message, 'document_rejection', $student->id, $proposalId);
    }

    /**
     * Send feedback notification to student
     */
    public function sendFeedbackNotification($student, $documentTitle, $proposalId = null)
    {
        if (!$student->phone) {
            return ['success' => false, 'message' => 'Student has no phone number'];
        }

        $message = "Dear {$student->name}, your supervisor has provided feedback on your submitted  ({$documentTitle}). Please log in to the system to review.";

        return $this->send($student->phone, $message, 'feedback', $student->id, $proposalId);
    }

    /**
     * Send meeting notification to student
     */
    public function sendMeetingNotification($student, $meeting, $date, $time)
    {
        if (!$student->phone) {
            return ['success' => false, 'message' => 'Student has no phone number'];
        }

        $message = "Dear {$student->name}, a research meeting has been scheduled with your supervisor on {$date} at {$time}. Topic: {$meeting->title}. Please log in to the system to join.";

        return $this->send($student->phone, $message, 'meeting', $student->id, null, $meeting->id);
    }
}
