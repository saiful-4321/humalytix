<?php

namespace App\Modules\HRM\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Modules\HRM\Models\TrainingParticipant;
// Using dompdf wrapper if available, or just native
use PDF; // Assuming barryvdh/laravel-dompdf is aliased typically

class CertificateController extends Controller
{
    /**
     * Download the certificate for a completed training participation.
     */
    private function getCertificateData(TrainingParticipant $participant)
    {
        return [
            'employee' => $participant->employee,
            'training_title' => $participant->session->training->title,
            'extra_info' => 'Duration: ' . $participant->session->training->duration_hours . ' Hours',
            'trainer_name' => $participant->session->training->trainer,
            'certificate_id' => 'CERT-' . str_pad($participant->id, 6, '0', STR_PAD_LEFT),
            'date' => $participant->completion_date ? $participant->completion_date->format('F d, Y') : now()->format('F d, Y'),
        ];
    }

    public function download(TrainingParticipant $participant)
    {
        if (auth()->id() !== $participant->employee->user_id && !auth()->user()->can('hrm.trainings.view')) abort(403);
        if (!in_array($participant->status, ['completed', 'attended'])) return back()->with('error', 'Certificate only for completed trainings.');

        $data = $this->getCertificateData($participant);
        
        $pdf = app('dompdf.wrapper');
        $pdf->loadView('HRM::pages.training.certificate', $data);
        $pdf->setPaper('a4', 'landscape');

        return $pdf->download('Certificate-' . $participant->employee->employee_code . '.pdf');
    }

    public function preview(TrainingParticipant $participant)
    {
        if (auth()->id() !== $participant->employee->user_id && !auth()->user()->can('hrm.trainings.view')) abort(403);
        return view('HRM::pages.training.certificate', $this->getCertificateData($participant));
    }

    /**
     * Download certificate for a manually recorded Certification (e.g. PMP-19412).
     */
    public function downloadExternal(\App\Modules\HRM\Models\Certification $certification)
    {
        if (auth()->id() !== $certification->employee->user_id && !auth()->user()->can('hrm.certifications.view')) abort(403);

        $data = [
            'employee' => $certification->employee,
            'training_title' => $certification->name,
            'extra_info' => 'Issued by: ' . $certification->issuing_organization,
            'trainer_name' => $certification->issuing_organization,
            'certificate_id' => $certification->credential_id ?? 'EXT-' . $certification->id,
            'date' => $certification->issue_date->format('F d, Y'),
        ];

        $pdf = app('dompdf.wrapper');
        $pdf->loadView('HRM::pages.training.certificate', $data);
        $pdf->setPaper('a4', 'landscape');

        return $pdf->download('Certificate-' . $certification->employee->employee_code . '.pdf');
    }
    public function previewExternal(\App\Modules\HRM\Models\Certification $certification)
    {
        if (auth()->id() !== $certification->employee->user_id && !auth()->user()->can('hrm.certifications.view')) abort(403);

        $data = [
            'employee' => $certification->employee,
            'training_title' => $certification->name,
            'extra_info' => 'Issued by: ' . $certification->issuing_organization,
            'trainer_name' => $certification->issuing_organization,
            'certificate_id' => $certification->credential_id ?? 'EXT-' . $certification->id,
            'date' => $certification->issue_date->format('F d, Y'),
        ];

        return view('HRM::pages.training.certificate', $data);
    }
}
