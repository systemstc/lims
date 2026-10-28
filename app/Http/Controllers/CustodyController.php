<?php

namespace App\Http\Controllers;

use App\Models\SampleRegistration;
use App\Services\CustodyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class CustodyController extends Controller
{
    /**
     * Send sample back to previous custody node with mandatory clarification remark.
     */
    public function sendBack(Request $request)
    {
        $request->validate([
            'sample_id'             => 'required',
            'to_role'               => 'required|in:RC,Manager,Analyst,DEO,Verification Officer',
            'clarification_remark'  => 'required|string|min:3|max:1000',
            'reassigned_analyst_id' => 'nullable',
        ], [
            'clarification_remark.required' => 'A clarification remark is strictly mandatory when sending back a sample.',
            'clarification_remark.min'      => 'Please provide a clear explanation (minimum 3 characters) for sending back.',
        ]);

        try {
            $fromRole = Session::get('role') ?? 'User';
            
            // Map sender role accurately based on execution context
            if ($request->input('context') === 'reporting' || $request->to_role === 'Verification Officer') {
                $fromRole = 'Issuing Officer';
            } elseif ($request->input('context') === 'verification' || in_array($fromRole, ['Verification Officer', 'Technical Manager'])) {
                $fromRole = 'Verification Officer';
            } elseif ($request->input('context') === 'deo' || $fromRole === 'DEO') {
                $fromRole = 'DEO';
            } elseif ($request->input('context') === 'analyst' || $fromRole === 'Analyst') {
                $fromRole = 'Analyst';
            } elseif ($request->input('context') === 'allotment' || $fromRole === 'Manager') {
                $fromRole = 'Manager';
            }

            $log = CustodyService::sendBack(
                $request->sample_id,
                $fromRole,
                $request->to_role,
                $request->clarification_remark,
                $request->reassigned_analyst_id
            );

            $sampleRef = $log->tr04_reference_id ?? $request->sample_id;

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => "Sample #{$sampleRef} successfully sent back to {$request->to_role} for clarification.",
                    'log'     => $log,
                ]);
            }

            // Determine safe redirect destination so sender returns to their queue/dashboard (where the sample is now removed)
            $targetUrl = match ($fromRole) {
                'Issuing Officer'      => route('test_results'),
                'Verification Officer' => route('view_result_verification'),
                'DEO'                  => route('view_completed_camples'),
                'Analyst'              => route('view_analyst_dashboard'),
                'Manager'              => route('view_allottment'),
                default                => null,
            };

            if (!$targetUrl) {
                $userRole = Session::get('role');
                $targetUrl = match ($userRole) {
                    'DEO'               => route('view_completed_camples'),
                    'Analyst'           => route('view_analyst_dashboard'),
                    'Technical Manager' => route('view_result_verification'),
                    'Manager'           => route('view_allottment'),
                    default             => route('dashboard'),
                };
            }

            Session::flash('type', 'warning');
            Session::flash('message', "Sample #{$sampleRef} has been sent back to {$request->to_role} for clarification.");
            return redirect($targetUrl);

        } catch (\Throwable $e) {
            Log::error('Custody Send Back Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 422);
            }

            Session::flash('type', 'error');
            Session::flash('message', 'Failed to send back sample: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    /**
     * Forward sample or mark clarification resolved.
     */
    public function forward(Request $request)
    {
        $request->validate([
            'sample_id' => 'required',
            'to_role'   => 'required|string',
            'remark'    => 'nullable|string|max:1000',
        ]);

        try {
            $fromRole = Session::get('role') ?? 'User';
            $log = CustodyService::forwardOrResolve(
                $request->sample_id,
                $fromRole,
                $request->to_role,
                $request->remark
            );

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => "Sample custody advanced to {$request->to_role}.",
                    'log'     => $log,
                ]);
            }

            Session::flash('type', 'success');
            Session::flash('message', "Sample custody advanced to {$request->to_role}.");
            return redirect()->back();

        } catch (\Throwable $e) {
            Log::error('Custody Forward Error: ' . $e->getMessage());

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 422);
            }

            Session::flash('type', 'error');
            Session::flash('message', 'Failed to forward sample: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    /**
     * Get custody timeline history via AJAX.
     */
    public function timeline($sampleId)
    {
        try {
            $timeline = CustodyService::getCustodyTimeline($sampleId);
            $sample = SampleRegistration::where('tr04_sample_registration_id', $sampleId)
                ->orWhere('tr04_reference_id', $sampleId)
                ->first();

            return response()->json([
                'success'  => true,
                'sample'   => [
                    'reference_id'         => $sample?->tr04_reference_id,
                    'current_custody_role' => $sample?->tr04_current_custody_role,
                    'is_under_clarification' => (bool)$sample?->tr04_is_under_clarification,
                    'latest_remark'        => $sample?->tr04_latest_clarification_remark,
                    'sent_back_by_name'    => $sample?->tr04_sent_back_by_name,
                    'sent_back_by_role'    => $sample?->tr04_sent_back_by_role,
                    'sent_back_at'         => $sample?->tr04_sent_back_at ? \Carbon\Carbon::parse($sample->tr04_sent_back_at)->format('d M, Y h:i A') : null,
                ],
                'timeline' => $timeline->map(function ($log) {
                    return [
                        'id'                   => $log->tr10_custody_log_id,
                        'from_role'            => $log->from_role,
                        'from_name'            => $log->from_name,
                        'to_role'              => $log->to_role,
                        'to_name'              => $log->to_name,
                        'action_type'          => $log->action_type,
                        'clarification_remark' => $log->clarification_remark,
                        'is_resolved'          => (bool)$log->is_resolved,
                        'resolved_at'          => $log->resolved_at ? \Carbon\Carbon::parse($log->resolved_at)->format('d M, Y h:i A') : null,
                        'created_at'           => $log->created_at ? \Carbon\Carbon::parse($log->created_at)->format('d M, Y h:i A') : null,
                    ];
                }),
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}
