@extends('layouts.app')

@section('title', 'Settings')
@section('page-title', 'Site Settings')

@section('content')
<form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data">
    @csrf @method('PUT')
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="filter-card">
                <h6 class="fw-bold mb-3">General</h6>
                <x-form.input name="site_name" label="Site Name" :value="$settings['site_name']" required />
                <div class="row">
                    <div class="col-md-6">
                        <label class="form-label-custom">Logo</label>
                        @if($settings['logo'])<img src="{{ asset('storage/'.$settings['logo']) }}" height="40" class="d-block mb-2" alt="Logo">@endif
                        <input type="file" name="logo" class="form-control form-control-custom" accept="image/*">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label-custom">Favicon</label>
                        @if($settings['favicon'])<img src="{{ asset('storage/'.$settings['favicon']) }}" height="32" class="d-block mb-2" alt="Favicon">@endif
                        <input type="file" name="favicon" class="form-control form-control-custom" accept="image/*">
                    </div>
                </div>
            </div>

            <div class="filter-card">
                <h6 class="fw-bold mb-3">SEO</h6>
                <x-form.input name="seo_title" label="Default SEO Title" :value="$settings['seo_title']" />
                <x-form.textarea name="seo_description" label="Default SEO Description" rows="3" :value="$settings['seo_description']" />
            </div>

            <div class="filter-card mb-0">
                <h6 class="fw-bold mb-3">Social Links</h6>
                <x-form.input name="social_facebook" label="Facebook URL" :value="$settings['social_facebook']" />
                <x-form.input name="social_twitter" label="Twitter / X URL" :value="$settings['social_twitter']" />
                <x-form.input name="social_instagram" label="Instagram URL" :value="$settings['social_instagram']" />
            </div>
        </div>

        <div class="col-lg-4">
            <div class="filter-card">
                <h6 class="fw-bold mb-3">Localization</h6>
                <x-form.input name="currency" label="Currency Code" :value="$settings['currency']" required />
                <x-form.input name="timezone" label="Timezone" :value="$settings['timezone']" required />
            </div>

            <div class="filter-card">
                <h6 class="fw-bold mb-3">Mail (SMTP)</h6>
                <x-form.input name="smtp_host" label="SMTP Host" :value="$settings['smtp_host']" hint="Configure credentials in .env" />
            </div>

            <div class="filter-card">
                <div class="form-check form-switch mb-0">
                    <input class="form-check-input" type="checkbox" name="maintenance_mode" value="1" id="maintenance_mode" @checked($settings['maintenance_mode'])>
                    <label class="form-check-label small" for="maintenance_mode">Maintenance Mode</label>
                </div>
            </div>

            <button class="btn btn-brand w-100" type="submit">Save Settings</button>
        </div>
    </div>
</form>
@endsection
