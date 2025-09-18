@extends('layouts.app_back')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">

                {{-- Card for Search --}}
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Create Test Template</h5>
                    </div>
                    <div class="card-body">

                        {{-- Search Test --}}
                        <div class="mb-3">
                            <label for="test-search" class="form-label fw-semibold">Search Test</label>
                            <select id="test-search" class="form-control">
                                <option value="">Select a Test</option>
                                @foreach ($tests as $test)
                                    <option value="{{ $test->m12_test_id }}">{{ $test->m12_name }}</option>
                                @endforeach
                            </select>
                            <small class="text-muted">Only tests without templates will be shown.</small>
                        </div>

                        {{-- Template Form --}}
                        <form id="template-form" action="{{ route('create_test_template') }}" method="POST" class="d-none">
                            @csrf
                            <input type="hidden" name="m12_test_id" id="m12_test_id">

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-semibold">Test Type</label>
                                    <select class="form-control" name="tr08_test_type" required>
                                        <option value="">-- Select Test Type --</option>
                                        <option value="Parametric">Parametric</option>
                                        <option value="Single">Single</option>
                                    </select>
                                </div>


                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-semibold">Times Test Perform</label>
                                    <input type="number" class="form-control" name="txt_test_performed" placeholder="Eg: 1"
                                        required>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-semibold">Number of Parameter</label>
                                    <input type="number" class="form-control" id="txt_param_num" name="txt_param_num"
                                        placeholder="Eg: 1" min="1" required>
                                </div>

                                {{-- Container for dynamically added parameter cards --}}
                                <div id="parameters-container" class="row"></div>
                            </div>
                            {{-- Formula --}}
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Formula</label>
                                <textarea class="form-control" name="txt_main_test_formula" rows="2"
                                    placeholder='((final_length - initial_length) / initial_length) * 100'></textarea>
                            </div>

                            {{-- Submit Button --}}
                            <div class="text-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="ti ti-plus"></i> Create Template
                                </button>
                            </div>

                        </form>

                    </div>
                </div>

            </div>
        </div>
    </div>



    <script>
        document.getElementById('test-search').addEventListener('change', function() {
            let selected = this.value;
            if (selected) {
                document.getElementById('template-form').classList.remove('d-none');
                document.getElementById('m12_test_id').value = selected;
            } else {
                document.getElementById('template-form').classList.add('d-none');
            }
        });

        document.getElementById('txt_param_num').addEventListener('input', function() {
            let container = document.getElementById('parameters-container');
            container.innerHTML = ''; // clear previous cards
            let count = parseInt(this.value);

            if (count > 0) {
                for (let i = 1; i <= count; i++) {
                    let card = document.createElement('div');
                    card.classList.add('col-md-12', 'mb-3');

                    card.innerHTML = `
                        <div class="card shadow-sm border rounded-3">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">Parameter ${i}</h6>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">Parameter Name</label>
                                        <input type="text" class="form-control" name="parameters[${i}][name]" placeholder="Eg: Length" required>
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">Number of Inputs</label>
                                        <input type="text" class="form-control" name="parameters[${i}][inputs]" placeholder="Eg: 2 or n" required>
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">Criteria (Min - Max)</label>
                                        <div class="input-group">
                                            <input type="number" step="any" class="form-control" name="parameters[${i}][min]" placeholder="Min" required>
                                            <span class="input-group-text">-</span>
                                            <input type="number" step="any" class="form-control" name="parameters[${i}][max]" placeholder="Max" required>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label fw-semibold">Formula</label>
                                        <textarea class="form-control" name="parameters[${i}][formula]" rows="2"
                                            placeholder="Eg: ((final_length - initial_length) / initial_length) * 100"></textarea>
                                        <small class="text-muted">You can use variables like input1, input2, etc. or 'n' for average calculations.</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                    container.appendChild(card);
                }
            }
        });
    </script>
@endsection
