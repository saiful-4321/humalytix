@extends('Main::layouts.app')

@section('title', 'Theme Settings')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">Theme Customization</h4>
        </div>
    </div>
</div>

@include("Main::widgets.message.sweet-alert")

<div class="row clearfix">
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
                                    <span class="number">1.</span> Presets & Mode
                                </a>
                            </li>
                            <li class="nav-item step-indicator" data-step="2">
                                <a href="javascript:void(0);" class="nav-link">
                                    <span class="number">2.</span> Layout
                                </a>
                            </li>
                            <li class="nav-item step-indicator" data-step="3">
                                <a href="javascript:void(0);" class="nav-link">
                                    <span class="number">3.</span> Sidebar & Topbar
                                </a>
                            </li>
                            <li class="nav-item step-indicator" data-step="4">
                                <a href="javascript:void(0);" class="nav-link">
                                    <span class="number">4.</span> Footer & Custom
                                </a>
                            </li>
                        </ul>
                    </div>

                    <div class="d-flex justify-content-end mb-4">
                        <form action="{{ route('dashboard.settings.theme.reset') }}" method="POST" onsubmit="return confirm('Are you sure you want to reset to default settings?');">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger btn-sm"><i class="mdi mdi-refresh me-1"></i> Reset to Default</button>
                        </form>
                    </div>

                    <form action="{{ route('dashboard.settings.theme.update') }}" method="POST" id="themeSettingsForm">
                    @csrf

                    <!-- Step 1: Presets & Mode -->
                    <div class="wizard-step" id="step-1">
                        <h5 class="mb-3 text-primary"><i class="mdi mdi-palette-swatch me-2"></i>Theme Presets</h5>
                        <div class="row mb-4" id="preset-container">
                            <div class="col-6 col-md-2 mb-3">
                                <div class="theme-preset" onclick="applyPreset('light')" data-preset="light">
                                    <div class="preset-preview bg-light border" style="height: 50px;"></div>
                                    <div class="text-center mt-2 small">Light</div>
                                </div>
                            </div>
                            <div class="col-6 col-md-2 mb-3">
                                <div class="theme-preset" onclick="applyPreset('dark')" data-preset="dark">
                                    <div class="preset-preview bg-dark" style="height: 50px;"></div>
                                    <div class="text-center mt-2 small">Dark</div>
                                </div>
                            </div>
                            <div class="col-6 col-md-2 mb-3">
                                <div class="theme-preset" onclick="applyPreset('blue')" data-preset="blue">
                                    <div class="preset-preview" style="height: 50px; background: #2c3e50;"></div>
                                    <div class="text-center mt-2 small">Blue</div>
                                </div>
                            </div>
                            <div class="col-6 col-md-2 mb-3">
                                <div class="theme-preset" onclick="applyPreset('green')" data-preset="green">
                                    <div class="preset-preview" style="height: 50px; background: #198754;"></div>
                                    <div class="text-center mt-2 small">Green</div>
                                </div>
                            </div>
                            <div class="col-6 col-md-2 mb-3">
                                <div class="theme-preset" onclick="applyPreset('purple')" data-preset="purple">
                                    <div class="preset-preview" style="height: 50px; background: #6f42c1;"></div>
                                    <div class="text-center mt-2 small">Purple</div>
                                </div>
                            </div>
                            <div class="col-6 col-md-2 mb-3">
                                <div class="theme-preset" onclick="applyPreset('red')" data-preset="red">
                                    <div class="preset-preview" style="height: 50px; background: #dc3545;"></div>
                                    <div class="text-center mt-2 small">Red</div>
                                </div>
                            </div>
                            <!-- Gradient Presets -->
                            <div class="col-6 col-md-2 mb-3">
                                <div class="theme-preset" onclick="applyPreset('midnight')" data-preset="midnight">
                                    <div class="preset-preview" style="height: 50px; background: linear-gradient(to right, #2c3e50, #4ca1af);"></div>
                                    <div class="text-center mt-2 small">Midnight</div>
                                </div>
                            </div>
                            <div class="col-6 col-md-2 mb-3">
                                <div class="theme-preset" onclick="applyPreset('sunset')" data-preset="sunset">
                                    <div class="preset-preview" style="height: 50px; background: linear-gradient(to right, #ff512f, #dd2476);"></div>
                                    <div class="text-center mt-2 small">Sunset</div>
                                </div>
                            </div>
                            <div class="col-6 col-md-2 mb-3">
                                <div class="theme-preset" onclick="applyPreset('ocean')" data-preset="ocean">
                                    <div class="preset-preview" style="height: 50px; background: linear-gradient(to right, #1cb5e0, #000046);"></div>
                                    <div class="text-center mt-2 small">Ocean</div>
                                </div>
                            </div>
                            <!-- Create Custom Theme -->
                            <div class="col-6 col-md-2 mb-3">
                                <div class="theme-preset" data-bs-toggle="modal" data-bs-target="#customThemeModal">
                                    <div class="preset-preview d-flex align-items-center justify-content-center bg-light border" style="height: 50px;">
                                        <i class="mdi mdi-plus font-size-24 text-primary"></i>
                                    </div>
                                    <div class="text-center mt-2 small">Create Custom</div>
                                </div>
                            </div>
                        </div>

                        <!-- Custom Theme Modal -->
                        <div class="modal fade" id="customThemeModal" tabindex="-1" aria-labelledby="customThemeModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="customThemeModalLabel">Create Custom Theme</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="modal_topbar_color" class="form-label">Topbar Color</label>
                                                <input type="color" class="form-control form-control-color w-100" id="modal_topbar_color" value="{{ $setting->topbar_custom_color ?? '#ffffff' }}">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="modal_sidebar_color" class="form-label">Sidebar Color</label>
                                                <input type="color" class="form-control form-control-color w-100" id="modal_sidebar_color" value="{{ $setting->sidebar_custom_color ?? '#2c3e50' }}">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="modal_footer_color" class="form-label">Footer Color</label>
                                                <input type="color" class="form-control form-control-color w-100" id="modal_footer_color" value="{{ $setting->footer_custom_color ?? '#ffffff' }}">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="modal_body_color" class="form-label">Page Content Color</label>
                                                <input type="color" class="form-control form-control-color w-100" id="modal_body_color" value="{{ $setting->body_custom_color ?? '#f8f8fb' }}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        <button type="button" class="btn btn-primary" onclick="applyCustomThemeFromModal()">Apply Theme</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <h5 class="mb-3 text-primary"><i class="mdi mdi-theme-light-dark me-2"></i>Layout Mode</h5>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="btn-group w-100" role="group">
                                    <input type="radio" class="btn-check" name="layout_mode" id="layout_mode_light" value="light" {{ old('layout_mode', $setting->layout_mode) == 'light' ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary" for="layout_mode_light"><i class="bx bx-sun me-1"></i> Light Mode</label>

                                    <input type="radio" class="btn-check" name="layout_mode" id="layout_mode_dark" value="dark" {{ old('layout_mode', $setting->layout_mode) == 'dark' ? 'checked' : '' }}>
                                    <label class="btn btn-outline-dark" for="layout_mode_dark"><i class="bx bx-moon me-1"></i> Dark Mode</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Hidden Input to store custom gradient strings since input type=color only supports hex -->
                    <input type="hidden" id="sidebar_gradient_val" name="sidebar_custom_color" value="{{ old('sidebar_custom_color', $setting->sidebar_custom_color ?? '#2c3e50') }}">
                    <input type="hidden" id="topbar_gradient_val" name="topbar_custom_color" value="{{ old('topbar_custom_color', $setting->topbar_custom_color ?? '#ffffff') }}">
                    <input type="hidden" id="footer_gradient_val" name="footer_custom_color" value="{{ old('footer_custom_color', $setting->footer_custom_color ?? '#ffffff') }}">
                    <input type="hidden" id="body_gradient_val" name="body_custom_color" value="{{ old('body_custom_color', $setting->body_custom_color ?? '#f8f8fb') }}">

                    <!-- Step 2: Layout Options -->
                    <div class="wizard-step d-none" id="step-2">
                        <h5 class="mb-3 text-primary"><i class="mdi mdi-monitor-dashboard me-2"></i>Layout Configuration</h5>
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label class="form-label d-block mb-2">Layout Type</label>
                                <div class="btn-group w-100" role="group">
                                    <input type="radio" class="btn-check" name="layout_type" id="layout_vertical" value="vertical" {{ old('layout_type', $setting->layout_type) == 'vertical' ? 'checked' : '' }}>
                                    <label class="btn btn-outline-secondary" for="layout_vertical">Vertical</label>

                                    <input type="radio" class="btn-check" name="layout_type" id="layout_horizontal" value="horizontal" {{ old('layout_type', $setting->layout_type) == 'horizontal' ? 'checked' : '' }}>
                                    <label class="btn btn-outline-secondary" for="layout_horizontal">Horizontal</label>
                                </div>
                            </div>

                            <div class="col-md-6 mb-4">
                                <label class="form-label d-block mb-2">Layout Width</label>
                                <div class="btn-group w-100" role="group">
                                    <input type="radio" class="btn-check" name="layout_width" id="layout_width_fluid" value="fluid" {{ old('layout_width', $setting->layout_width) == 'fluid' ? 'checked' : '' }}>
                                    <label class="btn btn-outline-secondary" for="layout_width_fluid">Fluid</label>

                                    <input type="radio" class="btn-check" name="layout_width" id="layout_width_boxed" value="boxed" {{ old('layout_width', $setting->layout_width) == 'boxed' ? 'checked' : '' }}>
                                    <label class="btn btn-outline-secondary" for="layout_width_boxed">Boxed</label>
                                </div>
                            </div>

                            <div class="col-md-6 mb-4">
                                <label class="form-label d-block mb-2">Layout Position</label>
                                <div class="btn-group w-100" role="group">
                                    <input type="radio" class="btn-check" name="layout_position" id="layout_fixed" value="fixed" {{ old('layout_position', $setting->layout_position) == 'fixed' ? 'checked' : '' }}>
                                    <label class="btn btn-outline-secondary" for="layout_fixed">Fixed</label>

                                    <input type="radio" class="btn-check" name="layout_position" id="layout_scrollable" value="scrollable" {{ old('layout_position', $setting->layout_position) == 'scrollable' ? 'checked' : '' }}>
                                    <label class="btn btn-outline-secondary" for="layout_scrollable">Scrollable</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 3: Sidebar & Topbar -->
                    <div class="wizard-step d-none" id="step-3">
                        <h5 class="mb-3 text-primary"><i class="mdi mdi-page-layout-sidebar-left me-2"></i>Sidebar & Topbar</h5>
                        
                        <div class="row mb-4">
                            <div class="col-md-12 mb-3">
                                <label class="form-label d-block mb-2">Sidebar Color</label>
                                <div class="btn-group w-100" role="group">
                                    <input type="radio" class="btn-check" name="sidebar_color" id="sidebar_light" value="light" {{ old('sidebar_color', $setting->sidebar_color) == 'light' ? 'checked' : '' }}>
                                    <label class="btn btn-outline-secondary" for="sidebar_light">Light</label>

                                    <input type="radio" class="btn-check" name="sidebar_color" id="sidebar_dark" value="dark" {{ old('sidebar_color', $setting->sidebar_color) == 'dark' ? 'checked' : '' }}>
                                    <label class="btn btn-outline-dark" for="sidebar_dark">Dark</label>

                                    <input type="radio" class="btn-check" name="sidebar_color" id="sidebar_brand" value="brand" {{ old('sidebar_color', $setting->sidebar_color) == 'brand' ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary" for="sidebar_brand">Brand</label>

                                    <input type="radio" class="btn-check" name="sidebar_color" id="sidebar_custom" value="custom" {{ old('sidebar_color', $setting->sidebar_color) == 'custom' ? 'checked' : '' }}>
                                    <label class="btn btn-outline-info" for="sidebar_custom">Custom</label>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6 mb-3">
                                <label class="form-label d-block mb-2">Sidebar Size</label>
                                <select class="form-select" id="sidebar_size" name="sidebar_size">
                                    <option value="lg" {{ old('sidebar_size', $setting->sidebar_size) == 'lg' ? 'selected' : '' }}>Default</option>
                                    <option value="md" {{ old('sidebar_size', $setting->sidebar_size) == 'md' ? 'selected' : '' }}>Compact</option>
                                    <option value="sm" {{ old('sidebar_size', $setting->sidebar_size) == 'sm' ? 'selected' : '' }}>Icon View</option>
                                </select>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="row mb-4">
                            <div class="col-md-12 mb-3">
                                <label class="form-label d-block mb-2">Topbar Color</label>
                                <div class="btn-group w-100" role="group">
                                    <input type="radio" class="btn-check" name="topbar_color" id="topbar_light" value="light" {{ old('topbar_color', $setting->topbar_color) == 'light' ? 'checked' : '' }}>
                                    <label class="btn btn-outline-secondary" for="topbar_light">Light</label>

                                    <input type="radio" class="btn-check" name="topbar_color" id="topbar_dark" value="dark" {{ old('topbar_color', $setting->topbar_color) == 'dark' ? 'checked' : '' }}>
                                    <label class="btn btn-outline-dark" for="topbar_dark">Dark</label>

                                    <input type="radio" class="btn-check" name="topbar_color" id="topbar_custom" value="custom" {{ old('topbar_color', $setting->topbar_color) == 'custom' ? 'checked' : '' }}>
                                    <label class="btn btn-outline-info" for="topbar_custom">Custom</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 4: Footer & Custom Colors -->
                    <div class="wizard-step d-none" id="step-4">
                        <h5 class="mb-3 text-primary"><i class="mdi mdi-format-color-fill me-2"></i>Footer & Custom Colors</h5>
                        
                        <div class="row mb-4">
                            <div class="col-md-12 mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="footer_enabled" name="footer_enabled" value="1" {{ old('footer_enabled', $setting->footer_enabled ?? 1) == 1 ? 'checked' : '' }}>
                                    <label class="form-check-label" for="footer_enabled">
                                        <strong>Enable Footer</strong>
                                        <small class="d-block text-muted">Toggle to show/hide footer across the application</small>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4" id="footer_color_section">
                            <div class="col-md-6 mb-3">
                                <label class="form-label d-block mb-2">Footer Color</label>
                                <div class="btn-group w-100" role="group">
                                    <input type="radio" class="btn-check" name="footer_color" id="footer_light" value="light" {{ old('footer_color', $setting->footer_color ?? 'light') == 'light' ? 'checked' : '' }}>
                                    <label class="btn btn-outline-secondary" for="footer_light">Light</label>

                                    <input type="radio" class="btn-check" name="footer_color" id="footer_dark" value="dark" {{ old('footer_color', $setting->footer_color) == 'dark' ? 'checked' : '' }}>
                                    <label class="btn btn-outline-dark" for="footer_dark">Dark</label>

                                    <input type="radio" class="btn-check" name="footer_color" id="footer_custom" value="custom" {{ old('footer_color', $setting->footer_color) == 'custom' ? 'checked' : '' }}>
                                    <label class="btn btn-outline-info" for="footer_custom">Custom</label>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label d-block mb-2">Page Background</label>
                                <div class="btn-group w-100" role="group">
                                    <input type="radio" class="btn-check" name="body_color" id="body_default" value="default" {{ old('body_color', $setting->body_color ?? 'default') == 'default' ? 'checked' : '' }}>
                                    <label class="btn btn-outline-secondary" for="body_default">Default</label>

                                    <input type="radio" class="btn-check" name="body_color" id="body_custom" value="custom" {{ old('body_color', $setting->body_color) == 'custom' ? 'checked' : '' }}>
                                    <label class="btn btn-outline-info" for="body_custom">Custom</label>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">
                        <h6 class="mb-3">Custom Color Pickers (Active when 'Custom' is selected above)</h6>
                        <p class="text-muted small">Note: For gradients, please select a preset. Manual color pickers only support solid colors.</p>

                        <div class="row mb-4">
                            <div class="col-md-3 mb-3">
                                <label for="sidebar_color_picker" class="form-label">Custom Sidebar Color</label>
                                <input type="color" class="form-control form-control-color w-100" id="sidebar_color_picker" value="{{ $setting->sidebar_custom_color ?? '#2c3e50' }}" title="Choose your color">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="topbar_color_picker" class="form-label">Custom Topbar Color</label>
                                <input type="color" class="form-control form-control-color w-100" id="topbar_color_picker" value="{{ $setting->topbar_custom_color ?? '#ffffff' }}" title="Choose your color">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="footer_color_picker" class="form-label">Custom Footer Color</label>
                                <input type="color" class="form-control form-control-color w-100" id="footer_color_picker" value="{{ $setting->footer_custom_color ?? '#ffffff' }}" title="Choose your color">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="body_color_picker" class="form-label">Custom Page Background</label>
                                <input type="color" class="form-control form-control-color w-100" id="body_color_picker" value="{{ $setting->body_custom_color ?? '#f8f8fb' }}" title="Choose your color">
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
        // Wizard Logic
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
        
        stepIndicators.forEach(indicator => {
            indicator.addEventListener('click', function() {
                const step = parseInt(this.getAttribute('data-step'));
                currentStep = step;
                updateStep(currentStep);
            });
        });

        // Initialize Wizard
        updateStep(currentStep);


        // Auto-select Custom Radio Logic and Sync Hidden Inputs
        const sections = ['topbar', 'sidebar', 'footer', 'body'];
        sections.forEach(section => {
            const colorPicker = document.getElementById(section + '_color_picker');
            const hiddenInput = document.getElementById(section + '_gradient_val');
            const customRadio = document.getElementById(section + '_custom');
            
            if (colorPicker && hiddenInput && customRadio) {
                colorPicker.addEventListener('input', function() {
                    customRadio.checked = true;
                    hiddenInput.value = this.value; // Sync hex to hidden input
                });
                colorPicker.addEventListener('click', function() {
                    customRadio.checked = true;
                });
            }
        });

        // Footer toggle functionality
        const footerToggle = document.getElementById('footer_enabled');
        const footerColorSection = document.getElementById('footer_color_section');
        
        function toggleFooterOptions() {
            if (footerToggle && footerColorSection) {
                if (footerToggle.checked) {
                    footerColorSection.style.opacity = '1';
                    footerColorSection.querySelectorAll('input').forEach(input => input.disabled = false);
                } else {
                    footerColorSection.style.opacity = '0.5';
                    footerColorSection.querySelectorAll('input').forEach(input => input.disabled = true);
                }
            }
        }
        
        if (footerToggle) {
            footerToggle.addEventListener('change', toggleFooterOptions);
            toggleFooterOptions(); // Initialize on load
        }

        // Highlight Active Preset on Load
        const currentSidebarColor = '{{ $setting->sidebar_custom_color ?? "" }}';
        const currentTopbarColor = '{{ $setting->topbar_custom_color ?? "" }}';
        const currentLayoutMode = '{{ $setting->layout_mode ?? "light" }}';
        const sidebarColorType = '{{ $setting->sidebar_color ?? "light" }}';
        const topbarColorType = '{{ $setting->topbar_color ?? "light" }}';
        const footerColorType = '{{ $setting->footer_color ?? "light" }}';
        
        // Match current settings to a preset
        let matchedPreset = null;
        
        if (sidebarColorType === 'light' && topbarColorType === 'light' && footerColorType === 'light' && currentLayoutMode === 'light') {
            matchedPreset = 'light';
        } else if (sidebarColorType === 'dark' && topbarColorType === 'dark' && footerColorType === 'dark' && currentLayoutMode === 'dark') {
            matchedPreset = 'dark';
        } else if (currentSidebarColor === '#2c3e50' && currentTopbarColor === '#2c3e50') {
            matchedPreset = 'blue';
        } else if (currentSidebarColor === '#198754') {
            matchedPreset = 'green';
        } else if (currentSidebarColor === '#6f42c1') {
            matchedPreset = 'purple';
        } else if (currentSidebarColor === '#dc3545') {
            matchedPreset = 'red';
        } else if (currentSidebarColor && currentSidebarColor.includes('linear-gradient') && currentSidebarColor.includes('2c3e50')) {
            matchedPreset = 'midnight';
        } else if (currentSidebarColor && currentSidebarColor.includes('linear-gradient') && currentSidebarColor.includes('ff512f')) {
            matchedPreset = 'sunset';
        } else if (currentSidebarColor && currentSidebarColor.includes('linear-gradient') && currentSidebarColor.includes('1cb5e0')) {
            matchedPreset = 'ocean';
        }
        
        // Highlight the matched preset
        if (matchedPreset) {
            const selectedPreset = document.querySelector(`.theme-preset[data-preset="${matchedPreset}"]`);
            if (selectedPreset) {
                selectedPreset.querySelector('.preset-preview').style.border = '3px solid #556ee6';
            }
        }
    });

    function applyPreset(preset) {
        // Highlight Selected Preset
        document.querySelectorAll('.theme-preset').forEach(el => {
            const preview = el.querySelector('.preset-preview');
            preview.classList.remove('ring-4', 'ring-primary', 'border-primary');
            preview.style.border = '1px solid #dee2e6'; // Reset to default border
        });
        
        const selectedPreset = document.querySelector(`.theme-preset[data-preset="${preset}"]`);
        if(selectedPreset) {
            selectedPreset.querySelector('.preset-preview').style.border = '3px solid #556ee6'; // Highlight
        }

        const presets = {
            'light': {
                'layout_mode': 'light',
                'topbar_color': 'light',
                'sidebar_color': 'light',
                'footer_color': 'light'
            },
            'dark': {
                'layout_mode': 'dark',
                'topbar_color': 'dark',
                'sidebar_color': 'dark',
                'footer_color': 'dark'
            },
            'blue': {
                'layout_mode': 'light',
                'topbar_color': 'custom',
                'topbar_custom_color': '#2c3e50',
                'sidebar_color': 'custom',
                'sidebar_custom_color': '#2c3e50',
                'footer_color': 'custom',
                'footer_custom_color': '#2c3e50'
            },
            'green': {
                'layout_mode': 'light',
                'topbar_color': 'custom',
                'topbar_custom_color': '#198754',
                'sidebar_color': 'custom',
                'sidebar_custom_color': '#198754',
                'footer_color': 'custom',
                'footer_custom_color': '#198754'
            },
            'purple': {
                'layout_mode': 'light',
                'topbar_color': 'custom',
                'topbar_custom_color': '#6f42c1',
                'sidebar_color': 'custom',
                'sidebar_custom_color': '#6f42c1',
                'footer_color': 'custom',
                'footer_custom_color': '#6f42c1'
            },
            'red': {
                'layout_mode': 'light',
                'topbar_color': 'custom',
                'topbar_custom_color': '#dc3545',
                'sidebar_color': 'custom',
                'sidebar_custom_color': '#dc3545',
                'footer_color': 'custom',
                'footer_custom_color': '#dc3545'
            },
            'midnight': {
                'layout_mode': 'light',
                'topbar_color': 'custom',
                'topbar_custom_color': 'linear-gradient(to right, #2c3e50, #4ca1af)',
                'sidebar_color': 'custom',
                'sidebar_custom_color': 'linear-gradient(to right, #2c3e50, #4ca1af)',
                'footer_color': 'custom',
                'footer_custom_color': 'linear-gradient(to right, #2c3e50, #4ca1af)'
            },
            'sunset': {
                'layout_mode': 'light',
                'topbar_color': 'custom',
                'topbar_custom_color': 'linear-gradient(to right, #ff512f, #dd2476)',
                'sidebar_color': 'custom',
                'sidebar_custom_color': 'linear-gradient(to right, #ff512f, #dd2476)',
                'footer_color': 'custom',
                'footer_custom_color': 'linear-gradient(to right, #ff512f, #dd2476)'
            },
            'ocean': {
                'layout_mode': 'light',
                'topbar_color': 'custom',
                'topbar_custom_color': 'linear-gradient(to right, #1cb5e0, #000046)',
                'sidebar_color': 'custom',
                'sidebar_custom_color': 'linear-gradient(to right, #1cb5e0, #000046)',
                'footer_color': 'custom',
                'footer_custom_color': 'linear-gradient(to right, #1cb5e0, #000046)'
            }
        };

        const config = presets[preset];
        if (!config) return;

        // Apply Layout Mode
        if (config.layout_mode) {
            document.getElementById('layout_mode_' + config.layout_mode).checked = true;
        }

        // Reset Body Color to Default for presets
        document.getElementById('body_default').checked = true;

        // Helper to set color and hidden input
        const setColor = (section, type, value) => {
            const radio = document.getElementById(section + '_' + type);
            if(radio) radio.checked = true;
            
            if (type === 'custom' && value) {
                const hiddenInput = document.getElementById(section + '_gradient_val');
                if(hiddenInput) hiddenInput.value = value;
                
                // If it's a hex, update picker, otherwise ignore (picker doesn't support gradients)
                if(value.startsWith('#')) {
                    const picker = document.getElementById(section + '_color_picker');
                    if(picker) picker.value = value;
                }
            }
        };

        // Apply Colors
        if (config.topbar_color) setColor('topbar', config.topbar_color, config.topbar_custom_color);
        if (config.sidebar_color) setColor('sidebar', config.sidebar_color, config.sidebar_custom_color);
        if (config.footer_color) setColor('footer', config.footer_color, config.footer_custom_color);
    }

    function applyCustomThemeFromModal() {
        // Get values from modal
        const topbarColor = document.getElementById('modal_topbar_color').value;
        const sidebarColor = document.getElementById('modal_sidebar_color').value;
        const footerColor = document.getElementById('modal_footer_color').value;
        const bodyColor = document.getElementById('modal_body_color').value;

        // Apply to main form hidden inputs and radios
        // Topbar
        document.getElementById('topbar_custom').checked = true;
        document.getElementById('topbar_gradient_val').value = topbarColor;
        document.getElementById('topbar_color_picker').value = topbarColor;

        // Sidebar
        document.getElementById('sidebar_custom').checked = true;
        document.getElementById('sidebar_gradient_val').value = sidebarColor;
        document.getElementById('sidebar_color_picker').value = sidebarColor;

        // Footer
        document.getElementById('footer_custom').checked = true;
        document.getElementById('footer_gradient_val').value = footerColor;
        document.getElementById('footer_color_picker').value = footerColor;

        // Body
        document.getElementById('body_custom').checked = true;
        document.getElementById('body_gradient_val').value = bodyColor;
        document.getElementById('body_color_picker').value = bodyColor;

        // Close Modal
        const modalEl = document.getElementById('customThemeModal');
        const modal = bootstrap.Modal.getInstance(modalEl);
        modal.hide();

        // Highlight "Create Custom" preset roughly
        document.querySelectorAll('.theme-preset').forEach(el => {
            el.querySelector('.preset-preview').classList.remove('ring-4', 'ring-primary', 'border-primary');
            el.querySelector('.preset-preview').style.border = '1px solid #dee2e6';
        });

        // Submit form automatically to apply changes immediately
        document.getElementById('themeSettingsForm').submit();
    }
</script>
@endpush
@endsection
