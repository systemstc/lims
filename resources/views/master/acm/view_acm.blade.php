@extends('layouts.app_back')

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            {{-- Small centered search card --}}
            <div class="col-md-6">
                <div class="card shadow-sm" id="search-card">
                    <div class="card-body">
                        <h5 class="card-title text-center mb-3">Access Control Management</h5>

                        {{-- Role Search --}}
                        <div class="form-group">
                            <label for="role_search" class="font-weight-bold">Search Role</label>
                            <select id="role_search" class="form-control"></select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Full-width menu table --}}
        <div class="row mt-4">
            <div class="col-12">
                <div class="card shadow-sm" id="menu-card" style="display:none;">
                    <div class="card-body p-0">
                        <table class="table table-hover table-striped mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th>Menu</th>
                                    {{-- <th>Route View</th> --}}
                                    <th class="text-center">View</th>
                                    <th class="text-center">Create</th>
                                    <th class="text-center">Edit</th>
                                    <th class="text-center">Delete</th>
                                </tr>
                            </thead>
                            <tbody id="menu-table-body"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            let selectedRoleId = null;

            // Initialize Select2 with AJAX search
            $('#role_search').select2({
                placeholder: 'Type to search role...',
                allowClear: true,
                ajax: {
                    url: "{{ route('search_role') }}",
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            q: params.term
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: $.map(data, function(item) {
                                return {
                                    id: item.m03_role_id,
                                    text: item.m03_name
                                };
                            })
                        };
                    },
                    cache: true
                }
            });

            // When a role is selected, fetch its menus
            $('#role_search').on('select2:select', function(e) {
                selectedRoleId = e.params.data.id;

                $.post("{{ route('get_menus_acm') }}", {
                    _token: "{{ csrf_token() }}",
                    role_id: selectedRoleId
                }, function(response) {
                    buildMenuTable(response.menus, response.role_id);
                    $('#menu-card').show(); // Show table card after loading
                });
            });

            // Hide table when role is cleared
            $('#role_search').on('select2:clear', function() {
                $('#menu-card').hide();
                $('#menu-table-body').empty();
            });

            function buildMenuTable(menus, roleId) {
                let tbody = '';

                if (!menus || menus.length === 0) {
                    tbody = `<tr><td colspan="6" class="text-center text-muted py-3">
                        No menus found for this role.
                     </td></tr>`;
                } else {
                    menus.forEach(menu => {
                        tbody += buildRow(menu, roleId, true);
                        if (menu.children && menu.children.length > 0) {
                            menu.children.forEach(child => {
                                tbody += buildRow(child, roleId, false);
                            });
                        }
                    });
                }
                $('#menu-table-body').html(tbody);
            }

            function buildRow(menu, roleId, isParent) {
                const viewRoles = menu.m05_role_view ? menu.m05_role_view.split(',') : [];
                const createRoles = menu.m05_role_create ? menu.m05_role_create.split(',') : [];
                const editRoles = menu.m05_role_edit ? menu.m05_role_edit.split(',') : [];
                const deleteRoles = menu.m05_role_delete ? menu.m05_role_delete.split(',') : [];

                return `
                    <tr class="${isParent ? 'bg-light font-weight-bold' : ''}">
                        <td class="${!isParent ? 'pl-4' : ''}">
                            ${!isParent ? '↳ ' : ''}${menu.m05_title}
                        </td>
                        <td class="text-center">
                            <input type="checkbox" class="permission-checkbox"
                                data-menu-id="${menu.m05_menu_id}" data-role-id="${roleId}" data-type="view"
                                ${viewRoles.includes(String(roleId)) ? 'checked' : ''}>
                        </td>
                        <td class="text-center">
                            <input type="checkbox" class="permission-checkbox"
                                data-menu-id="${menu.m05_menu_id}" data-role-id="${roleId}" data-type="create"
                                ${createRoles.includes(String(roleId)) ? 'checked' : ''}>
                        </td>
                        <td class="text-center">
                            <input type="checkbox" class="permission-checkbox"
                                data-menu-id="${menu.m05_menu_id}" data-role-id="${roleId}" data-type="edit"
                                ${editRoles.includes(String(roleId)) ? 'checked' : ''}>
                        </td>
                        <td class="text-center">
                            <input type="checkbox" class="permission-checkbox"
                                data-menu-id="${menu.m05_menu_id}" data-role-id="${roleId}" data-type="delete"
                                ${deleteRoles.includes(String(roleId)) ? 'checked' : ''}>
                        </td>
                    </tr>
                `;
            }

            // Handle checkbox changes
            $(document).on('change', '.permission-checkbox', function() {
                $.post("{{ route('update_acm') }}", {
                    _token: "{{ csrf_token() }}",
                    menu_id: $(this).data('menu-id'),
                    role_id: $(this).data('role-id'),
                    permission_type: $(this).data('type'),
                    checked: $(this).is(':checked')
                }, function(response) {
                    if (response.success) {
                        toastr.clear();
                        NioApp.Toast(response.message, response.type, {
                            position: 'top-right'
                        });
                    } else {
                        toastr.clear();
                        NioApp.Toast('Failed to update permission.', 'danger', {
                            position: 'top-right'
                        });
                    }
                }).fail(function() {
                    toastr.clear();
                    NioApp.Toast('Something went wrong.', 'danger', {
                        position: 'top-right'
                    });
                });
            });
        });
    </script>
@endsection