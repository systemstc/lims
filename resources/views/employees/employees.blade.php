@extends('layouts.app_back')
@section('content')
    <div class="container-fluid">
        <div class="nk-content-inner">
            <div class="nk-content-body">
                <div class="components-preview wide-xxl mx-auto">
                    <div class="nk-block nk-block-lg">
                        <div class="nk-block-head nk-block-head-sm mb-3">
                            <div class="nk-block-between align-items-center flex-wrap" style="gap: 15px;">
                                <div class="nk-block-head-content">
                                    <nav>
                                        <ul class="breadcrumb breadcrumb-arrow mb-1">
                                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                                            <li class="breadcrumb-item active">Employees</li>
                                        </ul>
                                    </nav>
                                    <h3 class="nk-block-title page-title fw-bold mb-0">Employee List</h3>
                                    <div class="nk-block-des text-soft">
                                        @if(Session::has('ro_id') && Session::get('role') !== 'ADMIN')
                                            <p class="mb-0 small">Manage and track registered lab employees for <span class="badge bg-outline-info text-dark fw-bold">{{ Session::get('ro_name', 'Your Regional Office') }}</span></p>
                                        @else
                                            <p class="mb-0 small">Manage and track all registered lab employees</p>
                                        @endif
                                    </div>
                                </div>
                                <div class="nk-block-head-content">
                                    <div class="d-flex align-items-center flex-wrap gap-2">
                                        <button type="button" class="btn btn-outline-primary text-nowrap" data-bs-toggle="modal" data-bs-target="#importEmployeeModal" style="border-radius: 6px; padding: 0.5rem 1.1rem; font-weight: 500;">
                                            <em class="icon ni ni-upload-cloud me-1"></em><span>Import Employees</span>
                                        </button>
                                        <a href="{{ route('create_employee') }}" class="btn btn-primary text-nowrap" style="border-radius: 6px; padding: 0.5rem 1.1rem; font-weight: 500;">
                                            <em class="icon ni ni-plus me-1"></em><span>Create Employee</span>
                                        </a>
                                        <a href="{{ route('dashboard') }}" class="btn btn-outline-light bg-white text-secondary text-nowrap" style="border-radius: 6px; padding: 0.5rem 1.1rem; font-weight: 500;">
                                            <em class="icon ni ni-arrow-left me-1"></em><span>Back</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if (session('import_errors'))
                            <div class="alert alert-warning alert-dismissible fade show mt-2" role="alert">
                                <div class="d-flex align-items-center mb-1">
                                    <em class="icon ni ni-alert-circle fs-5 me-2"></em>
                                    <strong>Some records had issues during import:</strong>
                                </div>
                                <ul class="mb-0 ps-4 text-start" style="max-height: 180px; overflow-y: auto; font-size: 0.85rem;">
                                    @foreach (session('import_errors') as $err)
                                        <li>{{ $err }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        <div class="card card-bordered card-preview">
                            <div class="card-inner">
                                <table class="datatable-init-export nowrap table" data-export-title="Export">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Emp Govt ID</th>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Role(s)</th>
                                            <th>Validity</th>
                                            <th>RO</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($employees as $key => $employee)
                                            <tr>
                                                <td>{{ $key + 1 }}</td>
                                                <td>{{ $employee->m06_emp_id ?? 'N/A' }}</td>
                                                <td>
                                                    <div class="d-inline-flex align-items-center">
                                                        <span class="fw-medium text-dark">{{ $employee->m06_name }}</span>
                                                        @if (!empty($employee->short_name))
                                                            <span class="badge bg-light text-primary border border-primary-subtle ms-2 px-1 py-0" title="Short Name / Initials" style="font-size: 0.70rem; font-weight: 700; letter-spacing: 0.5px;">{{ $employee->short_name }}</span>
                                                        @endif
                                                    </div>
                                                    @if (!empty($employee->m06_designation))
                                                        <span class="d-block text-muted small" style="font-size: 0.75rem;">{{ $employee->m06_designation }}</span>
                                                    @endif
                                                </td>
                                                <td>{{ $employee->m06_email }}</td>
                                                <td>
                                                    @if ($employee->user && $employee->user->roles && $employee->user->roles->count() > 0)
                                                        @foreach($employee->user->roles as $r)
                                                            <span class="badge {{ $r->pivot->is_primary ? 'bg-primary' : 'bg-outline-primary' }} mb-1" style="font-size: 0.72rem;">{{ $r->m03_name }}</span>
                                                        @endforeach
                                                    @else
                                                        <span class="badge bg-primary" style="font-size: 0.72rem;">{{ $employee->role->m03_name ?? 'N/A' }}</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if (!empty($employee->m06_valid_upto))
                                                        @php
                                                            $isExp = \Carbon\Carbon::parse($employee->m06_valid_upto)->endOfDay()->isPast();
                                                            $formattedDate = \Carbon\Carbon::parse($employee->m06_valid_upto)->format('d M, Y');
                                                        @endphp
                                                        @if ($isExp)
                                                            <span class="badge bg-danger" title="Contract expired" style="font-size: 0.72rem;"><em class="icon ni ni-alert-circle me-1"></em>Expired ({{ $formattedDate }})</span>
                                                        @else
                                                            <span class="badge bg-success" title="Active contract" style="font-size: 0.72rem;"><em class="icon ni ni-calendar-check me-1"></em>Till {{ $formattedDate }}</span>
                                                        @endif
                                                    @else
                                                        <span class="badge bg-light text-muted border" style="font-size: 0.72rem;">Permanent</span>
                                                    @endif
                                                </td>
                                                <td>{{ $employee->ro->m04_name ?? 'N/A' }}</td>
                                                <td
                                                    class="text-{{ $employee->m06_status == 'ACTIVE' ? 'success' : 'danger' }}">
                                                    <strong>{{ $employee->m06_status }}</strong>
                                                </td>
                                                <td class="nk-tb-col nk-tb-col-tools">
                                                    <ul class="nk-tb-actions gx-1 my-n1">
                                                        <li class="me-n1">
                                                            <div class="dropdown">
                                                                <a href="#"
                                                                    class="dropdown-toggle btn btn-icon btn-trigger"
                                                                    data-bs-toggle="dropdown"><em
                                                                        class="icon ni ni-more-h"></em></a>
                                                                <div class="dropdown-menu dropdown-menu-end">
                                                                    <ul class="link-list-opt no-bdr">
                                                                        <li><a href="{{ route('update_employee', $employee->m06_employee_id) }}"><em
                                                                                    class="icon ni ni-edit"></em><span>Edit
                                                                                </span></a></li>
                                                                        <li><a href="#"><em
                                                                                    class="icon ni ni-eye"></em><span>View
                                                                                </span></a></li>
                                                                        @if ($employee->user)
                                                                            <li>
                                                                                <a href="#"
                                                                                    onclick="event.preventDefault(); document.getElementById('toggle-2fa-{{ $employee->user->tr01_user_id }}').submit();">
                                                                                    <em
                                                                                        class="icon ni ni-{{ $employee->user->tr01_is_2fa_blocked ? 'unlock' : 'lock-alt' }}"></em>
                                                                                    <span>{{ $employee->user->tr01_is_2fa_blocked ? 'Unblock 2FA' : 'Block 2FA' }}</span>
                                                                                </a>
                                                                                <form
                                                                                    id="toggle-2fa-{{ $employee->user->tr01_user_id }}"
                                                                                    action="{{ route('toggle_2fa_access', $employee->user->tr01_user_id) }}"
                                                                                    method="POST" class="d-none">
                                                                                    @csrf
                                                                                </form>
                                                                            </li>
                                                                        @endif
                                                                        <li><a href="#"><em
                                                                                    class="icon ni ni-trash"></em><span>Delete
                                                                                </span></a></li>
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                        </li>
                                                    </ul>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div><!-- .card-preview -->
                    </div> <!-- nk-block -->
                </div><!-- .components-preview -->
            </div>
        </div>
    </div>

    <!-- Import Employees Modal -->
    <div class="modal fade" id="importEmployeeModal" tabindex="-1" aria-labelledby="importEmployeeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-light">
                    <h5 class="modal-title" id="importEmployeeModalLabel">
                        <em class="icon ni ni-upload-cloud text-primary me-1"></em> Import Employees (CSV / Excel)
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('import_employees') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body p-4">
                        <!-- Template Download Card -->
                        <div class="alert alert-pro alert-primary mb-3">
                            <div class="alert-text d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-1 text-primary"><em class="icon ni ni-file-code me-1"></em> Need the format template?</h6>
                                    <p class="mb-0 small text-muted">Download the pre-formatted sample CSV matching your sheet columns (Region, Name, Designation, Gov Email, mobile, Empl Id, Role1...RoleN).</p>
                                </div>
                                <a href="{{ route('employee_sample_csv') }}" class="btn btn-sm btn-primary text-nowrap ms-2">
                                    <em class="icon ni ni-download me-1"></em> Sample CSV
                                </a>
                            </div>
                        </div>

                        <!-- File Upload Input -->
                        <div class="form-group mb-3">
                            <label class="form-label fw-bold" for="employee_file">Select File <span class="text-danger">*</span></label>
                            <div class="form-control-wrap">
                                <input type="file" name="employee_file" id="employee_file" class="form-control" accept=".csv, .txt, .xlsx, .xls" required>
                            </div>
                            <small class="text-muted">Supported formats: <strong>.csv</strong>, <strong>.xlsx</strong>, <strong>.txt</strong> (Max: 10MB)</small>
                        </div>

                        <!-- Configuration Options -->
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label" for="default_password">Default Password for User Accounts</label>
                                    <div class="form-control-wrap">
                                        <input type="text" name="default_password" id="default_password" class="form-control" value="Default@123" placeholder="Default@123">
                                    </div>
                                    <small class="text-muted">Assigned to newly created user accounts (unless specified in CSV).</small>
                                </div>
                            </div>
                            <div class="col-md-6 d-flex flex-column justify-content-center">
                                <div class="custom-control custom-checkbox mb-2">
                                    <input type="checkbox" class="custom-control-input" id="update_existing" name="update_existing" value="1" checked>
                                    <label class="custom-control-label fw-medium" for="update_existing">Update details if Employee already exists</label>
                                </div>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="reset_existing_password" name="reset_existing_password" value="1">
                                    <label class="custom-control-label" for="reset_existing_password">Also reset password for existing users to default</label>
                                </div>
                            </div>
                        </div>

                        <!-- Column Mapping Info Box -->
                        <div class="border rounded p-3 bg-lighter small">
                            <div class="fw-bold mb-2 text-dark"><em class="icon ni ni-info me-1"></em> Supported Columns in Your Sheet:</div>
                            <div class="row g-2">
                                <div class="col-md-4"><code>Region</code>: Regional Office name</div>
                                <div class="col-md-4"><code>Name</code>: Full Employee Name</div>
                                <div class="col-md-4"><code>Designation</code>: Job title / post</div>
                                <div class="col-md-4"><code>Gov Email</code>: Login email address</div>
                                <div class="col-md-4"><code>mobile</code>: 10-digit mobile number</div>
                                <div class="col-md-4"><code>Empl Id</code>: Govt Employee ID</div>
                                <div class="col-md-6"><code>Role1, Role2 ... RoleN</code>: Any number of roles (e.g. Role1 to Role6+)</div>
                                <div class="col-md-6"><code>Password</code> (optional): Custom password per user</div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="btnSubmitImport">
                            <em class="icon ni ni-upload me-1"></em> Start Import
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
