@extends('Main::layouts.app')

@section('title', 'Company Settings')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">Company Settings</h4>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xl-12">
        <div class="card bg-white">
            <div class="card-body">
                <div class="p-4">
                    <!-- Wizard Progress -->
                    <div class="wizard-progress mb-4">
                        <div class="progress" style="height: 5px;">
                            <div class="progress-bar bg-primary" role="progressbar" style="width: 25%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <ul class="nav nav-pills nav-justified mt-3">
                            <li class="nav-item step-indicator active" data-step="1">
                                <a href="javascript:void(0);" class="nav-link active">
                                    <span class="number">1.</span> General
                                </a>
                            </li>
                            <li class="nav-item step-indicator" data-step="2">
                                <a href="javascript:void(0);" class="nav-link">
                                    <span class="number">2.</span> SEO
                                </a>
                            </li>
                            <li class="nav-item step-indicator" data-step="3">
                                <a href="javascript:void(0);" class="nav-link">
                                    <span class="number">3.</span> Branding
                                </a>
                            </li>
                            <li class="nav-item step-indicator" data-step="4">
                                <a href="javascript:void(0);" class="nav-link">
                                    <span class="number">4.</span> Features
                                </a>
                            </li>
                        </ul>
                    </div>

                    <form action="{{ route('dashboard.settings.company.update') }}" method="POST" enctype="multipart/form-data" id="settingsForm">
                    @csrf
                    
                    <!-- Step 1: General Info -->
                    <div class="wizard-step" id="step-1">
                        <h5 class="mb-3 text-primary">General Information</h5>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="company_name" class="form-label">Company Name</label>
                                <input type="text" class="form-control" id="company_name" name="company_name" value="{{ old('company_name', $setting->company_name) }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="short_name" class="form-label">Short Name</label>
                                <input type="text" class="form-control" id="short_name" name="short_name" value="{{ old('short_name', $setting->short_name) }}">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="address" class="form-label">Address</label>
                            <textarea class="form-control" id="address" name="address" rows="3">{{ old('address', $setting->address) }}</textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $setting->email) }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">Phone</label>
                                <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone', $setting->phone) }}">
                            </div>
                        </div>
                    </div>

                    <!-- Step 2: SEO -->
                    <div class="wizard-step d-none" id="step-2">
                        <h5 class="mb-3 text-primary">SEO Settings</h5>
                        <div class="mb-3">
                            <label for="meta_title" class="form-label">Meta Title</label>
                            <input type="text" class="form-control" id="meta_title" name="meta_title" value="{{ old('meta_title', $setting->meta_title) }}">
                        </div>

                        <div class="mb-3">
                            <label for="meta_desc" class="form-label">Meta Description</label>
                            <textarea class="form-control" id="meta_desc" name="meta_desc" rows="3">{{ old('meta_desc', $setting->meta_desc) }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label for="meta_tags" class="form-label">Meta Tags</label>
                            <input type="text" class="form-control" id="meta_tags" name="meta_tags" value="{{ old('meta_tags', $setting->meta_tags) }}">
                        </div>
                    </div>

                    <!-- Step 3: Branding -->
                    <div class="wizard-step d-none" id="step-3">
                        <h5 class="mb-3 text-primary">Branding & Logos</h5>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="logo_white" class="form-label">Logo White</label>
                                <input type="file" class="form-control" id="logo_white" name="logo_white">
                                @if($setting->logo_white)
                                    <div class="mt-2">
                                        <img src="{{ asset($setting->logo_white) }}" alt="Logo White" height="50">
                                    </div>
                                @endif
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="logo_dark" class="form-label">Logo Dark</label>
                                <input type="file" class="form-control" id="logo_dark" name="logo_dark">
                                @if($setting->logo_dark)
                                    <div class="mt-2">
                                        <img src="{{ asset($setting->logo_dark) }}" alt="Logo Dark" height="50">
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="logo_white_small" class="form-label">Logo White Small</label>
                                <input type="file" class="form-control" id="logo_white_small" name="logo_white_small">
                                @if($setting->logo_white_small)
                                    <div class="mt-2">
                                        <img src="{{ asset($setting->logo_white_small) }}" alt="Logo White Small" height="50">
                                    </div>
                                @endif
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="logo_dark_small" class="form-label">Logo Dark Small</label>
                                <input type="file" class="form-control" id="logo_dark_small" name="logo_dark_small">
                                @if($setting->logo_dark_small)
                                    <div class="mt-2">
                                        <img src="{{ asset($setting->logo_dark_small) }}" alt="Logo Dark Small" height="50">
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="favicon" class="form-label">Favicon</label>
                            <input type="file" class="form-control" id="favicon" name="favicon">
                            @if($setting->favicon)
                                <div class="mt-2">
                                    <img src="{{ asset($setting->favicon) }}" alt="Favicon" height="32">
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Step 4: Features -->
                    <div class="wizard-step d-none" id="step-4">
                        <h5 class="mb-3 text-primary">Feature Control</h5>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-check form-switch form-switch-lg mb-3" dir="ltr">
                                    <input class="form-check-input" type="checkbox" id="registration_active" name="registration_active" {{ old('registration_active', $setting->registration_active ?? true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="registration_active">Enable User Registration</label>
                                </div>
                                <div class="form-check form-switch form-switch-lg mb-3" dir="ltr">
                                    <input class="form-check-input" type="checkbox" id="password_reset_active" name="password_reset_active" {{ old('password_reset_active', $setting->password_reset_active ?? true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="password_reset_active">Enable Password Reset</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 d-flex justify-content-between">
                        <button type="button" class="btn btn-secondary" id="prevBtn" style="display:none;">Previous</button>
                        <div class="ms-auto">
                            <button type="button" class="btn btn-info me-2" id="nextBtn">Next</button>
                            <button type="submit" class="btn btn-primary">Save & Exit</button>
                        </div>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let currentStep = 1;
        const totalSteps = 4;

        const prevBtn = document.getElementById('prevBtn');
        const nextBtn = document.getElementById('nextBtn');
        const progressBar = document.querySelector('.progress-bar');
        const stepIndicators = document.querySelectorAll('.step-indicator');

        function updateStep(step) {
            // Hide all steps
            document.querySelectorAll('.wizard-step').forEach(el => el.classList.add('d-none'));
            // Show current step
            document.getElementById('step-' + step).classList.remove('d-none');

            // Update buttons
            if (step === 1) {
                prevBtn.style.display = 'none';
            } else {
                prevBtn.style.display = 'inline-block';
            }

            if (step === totalSteps) {
                nextBtn.style.display = 'none';
            } else {
                nextBtn.style.display = 'inline-block';
            }

            // Update Progress Bar
            const progress = (step / totalSteps) * 100;
            progressBar.style.width = progress + '%';
            progressBar.setAttribute('aria-valuenow', progress);

            // Update Indicators
            stepIndicators.forEach(indicator => {
                const indicatorStep = parseInt(indicator.getAttribute('data-step'));
                const link = indicator.querySelector('.nav-link');
                if (indicatorStep === step) {
                    indicator.classList.add('active');
                    link.classList.add('active');
                } else if (indicatorStep < step) {
                    indicator.classList.remove('active');
                    link.classList.remove('active');
                    // Optional: Add a class for completed steps if you want different styling
                } else {
                    indicator.classList.remove('active');
                    link.classList.remove('active');
                }
            });
        }

        nextBtn.addEventListener('click', function() {
            if (currentStep < totalSteps) {
                currentStep++;
                updateStep(currentStep);
            }
        });

        prevBtn.addEventListener('click', function() {
            if (currentStep > 1) {
                currentStep--;
                updateStep(currentStep);
            }
        });
        
        // Optional: Allow clicking on steps
        stepIndicators.forEach(indicator => {
            indicator.addEventListener('click', function() {
                const step = parseInt(this.getAttribute('data-step'));
                // Only allow going to next step if we want to enforce sequential, but for settings usually random access is fine.
                // However, for a wizard, usually sequential. Let's allow random access for better UX in settings.
                currentStep = step;
                updateStep(currentStep);
            });
        });

        // Initialize
        updateStep(currentStep);
    });
</script>
@endpush
@endsection
