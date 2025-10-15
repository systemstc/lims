@extends('layouts.app_back')

@section('content')
    <div class="nk-content ">
        <div class="container-fluid">
            <div class="nk-content-inner">
                <div class="nk-content-body">

                    {{-- Page Header --}}
                    <div class="nk-block-head nk-block-head-sm">
                        <div class="nk-block-head">
                            <div class="nk-block-head-content d-flex justify-content-between align-items-center">
                                <h3 class="nk-block-title page-title">Create Manuscript</h3>
                                <div class="text-end">
                                    <a href="{{ route('view_manuscripts') }}" class="btn btn-primary">
                                        <em class="icon ni ni-caret-left-fill"></em>&nbsp; Back
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Page Content --}}
                    <div class="nk-block">
                        <div class="card card-bordered">
                            <div class="card-inner">
                                <form action="{{ route('create_manuscript') }}" method="POST" id="createManuscriptForm">
                                    @csrf

                                    <div class="row g-3">

                                        {{-- Sample --}}
                                        <div class="col-md-4">
                                            <label class="form-label">Sample</label>
                                            <div class="form-control-wrap">
                                                <select id="sample_id" name="txt_sample_id" class="form-select" required>
                                                    <option value="">Select Sample</option>
                                                    @foreach ($samples as $sample)
                                                        <option value="{{ $sample->m10_sample_id }}">
                                                            {{ $sample->m10_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('txt_sample_id')
                                                    <span class="text-danger small">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        {{-- Group --}}
                                        <div class="col-md-4">
                                            <label class="form-label">Group</label>
                                            <div class="form-control-wrap">
                                                <select id="group_id" name="txt_group_id" class="form-select" required>
                                                    <option value="">Select Group</option>
                                                </select>
                                                @error('txt_group_id')
                                                    <span class="text-danger small">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        {{-- Test --}}
                                        <div class="col-md-4">
                                            <label class="form-label">Test</label>
                                            <div class="form-control-wrap">
                                                <select id="test_id" name="txt_test_id" class="form-select" required>
                                                    <option value="">Select Test</option>
                                                </select>
                                                @error('txt_test_id')
                                                    <span class="text-danger small">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        {{-- Manuscript Name --}}
                                        <div class="col-md-8">
                                            <label class="form-label">Manuscript Name</label>
                                            <div class="form-control-wrap">
                                                <input type="text" name="txt_name" class="form-control"
                                                    placeholder="Enter manuscript name" required>
                                                @error('txt_name')
                                                    <span class="text-danger small">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        {{-- Submit --}}
                                        <div class="col-md-4 text-end mt-5">
                                            <button type="submit" class="btn btn-primary">
                                                <em class="icon ni ni-save"></em>
                                                <span>Create</span>
                                            </button>
                                        </div>
                                        {{-- Existing Manuscripts Section --}}
                                        <div class="col-md-12">
                                            <div id="existing_manuscripts" class="mt-4"></div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div> {{-- .nk-block --}}
                </div>
            </div>
        </div>
    </div>

    {{-- JS Section --}}
    <script>
        const sampleSelect = document.getElementById('sample_id');
        const groupSelect = document.getElementById('group_id');
        const testSelect = document.getElementById('test_id');
        const manuscriptSection = document.getElementById('existing_manuscripts');

        // Load groups when sample changes
        sampleSelect.addEventListener('change', function() {
            const sampleId = this.value;
            groupSelect.innerHTML = '<option value="">Select Group</option>';
            testSelect.innerHTML = '<option value="">Select Test</option>';
            manuscriptSection.innerHTML = '';

            if (!sampleId) return;

            fetch(`{{ route('get_groups') }}?sample_id=${sampleId}`)
                .then(res => res.json())
                .then(groups => {
                    groups.forEach(g => {
                        groupSelect.innerHTML +=
                            `<option value="${g.m11_group_code}">${g.m11_name}</option>`;
                    });
                })
                .catch(err => console.error('Error loading groups:', err));
        });

        // Load tests when group changes
        groupSelect.addEventListener('change', function() {
            const groupId = this.value;
            testSelect.innerHTML = '<option value="">Select Test</option>';
            manuscriptSection.innerHTML = '';

            if (!groupId) return;

            fetch(`{{ route('get_tests') }}?group_id=${groupId}`)
                .then(res => res.json())
                .then(tests => {
                    tests.forEach(t => {
                        testSelect.innerHTML +=
                            `<option value="${t.m12_test_number}">${t.m12_name}</option>`;
                    });
                })
                .catch(err => console.error('Error loading tests:', err));
        });

        // Load existing manuscripts when test changes
        testSelect.addEventListener('change', function() {
            const testId = this.value;
            manuscriptSection.innerHTML = '';

            if (!testId) return;

            fetch(`{{ route('get_manuscripts') }}?test_id=${testId}`)
                .then(res => res.json())
                .then(manuscripts => {
                    if (manuscripts.length > 0) {
                        let html = `
                            <div class="alert alert-info">
                                <strong>${manuscripts.length}</strong> existing manuscript(s) found for this test:
                            </div>
                            <div class="card card-bordered">
                                <div class="card-inner p-2">
                                    <ul class="list-group list-group-flush">
                        `;
                        manuscripts.forEach(m => {
                            html += `
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>${m.m22_name}</span>
                                </li>`;
                        });
                        html += `
                                    </ul>
                                </div>
                            </div>
                        `;
                        manuscriptSection.innerHTML = html;
                    } else {
                        manuscriptSection.innerHTML = `
                            <div class="alert alert-success">
                                No existing manuscripts found for this test. You can create a new one.
                            </div>`;
                    }
                })
                .catch(err => console.error('Error loading manuscripts:', err));
        });
    </script>
@endsection
