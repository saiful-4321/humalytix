@extends('Main::layouts.app')

@section('title', 'Skill Matrix')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                     <h4 class="card-title mb-0">Skill Matrix Report</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive" style="max-height: 70vh; overflow-y: auto;">
                        <table class="table table-bordered table-hover text-center align-middle">
                            <thead class="table-light sticky-top" style="z-index: 10;">
                                <tr>
                                    <th class="text-start bg-light sticky-start" style="width: 200px; z-index: 20; position: sticky; left: 0;">Employee</th>
                                    @foreach($skills as $skill)
                                        <th class="vertical-text" style="writing-mode: vertical-rl; transform: rotate(180deg); min-width: 40px;">
                                            {{ $skill->name }}
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($employees as $emp)
                                <tr>
                                    <td class="text-start fw-bold bg-white sticky-start" style="position: sticky; left: 0;">
                                        {{ $emp->full_name }}
                                        <div class="text-muted small fw-normal">{{ $emp->designation->name ?? '' }}</div>
                                    </td>
                                    @foreach($skills as $skill)
                                        @php
                                            $empSkill = $emp->skills->firstWhere('id', $skill->id);
                                            $level = $empSkill->pivot->proficiency_level ?? null;
                                            $class = '';
                                            $text = '-';
                                            
                                            if ($level == 'beginner') {
                                                $class = 'bg-soft-secondary';
                                                $text = '1';
                                            } elseif ($level == 'intermediate') {
                                                $class = 'bg-soft-info';
                                                $text = '2';
                                            } elseif ($level == 'advanced') {
                                                $class = 'bg-soft-primary';
                                                $text = '3';
                                            } elseif ($level == 'expert') {
                                                $class = 'bg-success text-white';
                                                $text = '4';
                                            }
                                        @endphp
                                        <td class="{{ $class }}" title="{{ ucfirst($level) }}">
                                            {{ $text }}
                                        </td>
                                    @endforeach
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3 d-flex gap-3">
                        <span class="badge bg-soft-secondary">1: Beginner</span>
                        <span class="badge bg-soft-info">2: Intermediate</span>
                        <span class="badge bg-soft-primary">3: Advanced</span>
                        <span class="badge bg-success">4: Expert</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
.sticky-start {
    position: sticky;
    left: 0;
    z-index: 5;
    border-right: 2px solid #dee2e6;
}
</style>
@endsection
