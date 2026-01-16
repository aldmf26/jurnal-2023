@props([
    'route' => '',
    'halaman' => '',
])

{{-- modal setting --}}
@if (auth()->user()->posisi_id == 1)
    <x-theme.button modal="Y" idModal="akses" icon="fas fa-cog" addClass="float-end" teks="" />
@endif

<form action="{{ route('akses.save') }}" method="post">
    @csrf
    <input type="hidden" name="route" value="{{ $route }}">
    <x-theme.modal title="Akses Setting" idModal="akses" size="modal-lg">
        <table class="table table-bordered text-center">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nama</th>
                    <th>Halaman</th>
                    <th>Create</th>
                    <th>Read</th>
                    <th>Update</th>
                    <th>Delete</th>
                </tr>
            </thead>

            <tbody>
                @php
                    // 👇 Query SEKALI untuk semua user
                    $users = DB::table('users')->where('nonaktif', 'T')->get();

                    // 👇 Get semua permissions sekali (sudah di-cache di controller)
                    $allPermissions = SettingHal::getAllPermissions($halaman);
                @endphp

                @foreach ($users as $no => $u)
                    @php
                        // 👇 Ambil dari cache, TIDAK query database lagi
                        $userPermissions = $allPermissions->get($u->id, collect());
                        $hasAccess = $userPermissions->isNotEmpty();

                        // Group by jenis
                        $createPerms = $userPermissions->where('jenis', 'create');
                        $readPerms = $userPermissions->where('jenis', 'read');
                        $updatePerms = $userPermissions->where('jenis', 'update');
                        $deletePerms = $userPermissions->where('jenis', 'delete');
                    @endphp
                    <tr>
                        <td>{{ $no + 1 }}</td>
                        <td>{{ ucwords($u->name) }}</td>

                        <td>
                            <label>
                                <input type="checkbox"
                                    class="form-check-glow form-check-input form-check-primary akses_h akses_h{{ $u->id }}"
                                    id_user="{{ $u->id }}" {{ $hasAccess ? 'checked' : '' }} />
                                Akses
                            </label>
                            <input type="hidden" class="open_check{{ $u->id }}" name="id_user[]"
                                {{ $hasAccess ? '' : 'disabled' }} value="{{ $u->id }}">
                        </td>

                        <td>
                            <input type="hidden" name="id_permission_gudang" value="{{ $halaman }}">
                            @foreach ($createPerms as $perm)
                                <label>
                                    <input type="checkbox" name="id_permission{{ $u->id }}[]"
                                        value="{{ $perm->id_permission_button }}"
                                        {{ $perm->id_permission_page ? 'checked' : '' }}
                                        class="form-check-glow form-check-input form-check-primary open_check{{ $u->id }}"
                                        {{ $hasAccess ? '' : 'disabled' }} />
                                    {!! $perm->nm_permission_button !!}
                                </label><br>
                            @endforeach
                        </td>

                        <td>
                            @foreach ($readPerms as $perm)
                                <label>
                                    <input type="checkbox" name="id_permission{{ $u->id }}[]"
                                        value="{{ $perm->id_permission_button }}"
                                        {{ $perm->id_permission_page ? 'checked' : '' }}
                                        class="form-check-glow form-check-input form-check-primary open_check{{ $u->id }}"
                                        {{ $hasAccess ? '' : 'disabled' }} />
                                    {!! $perm->nm_permission_button !!}
                                </label><br>
                            @endforeach
                        </td>

                        <td>
                            @foreach ($updatePerms as $perm)
                                <label>
                                    <input type="checkbox" name="id_permission{{ $u->id }}[]"
                                        value="{{ $perm->id_permission_button }}"
                                        {{ $perm->id_permission_page ? 'checked' : '' }}
                                        class="form-check-glow form-check-input form-check-primary open_check{{ $u->id }}"
                                        {{ $hasAccess ? '' : 'disabled' }} />
                                    {!! $perm->nm_permission_button !!}
                                </label><br>
                            @endforeach
                        </td>

                        <td>
                            @foreach ($deletePerms as $perm)
                                <label>
                                    <input type="checkbox" name="id_permission{{ $u->id }}[]"
                                        value="{{ $perm->id_permission_button }}"
                                        {{ $perm->id_permission_page ? 'checked' : '' }}
                                        class="form-check-glow form-check-input form-check-primary open_check{{ $u->id }}"
                                        {{ $hasAccess ? '' : 'disabled' }} />
                                    {!! $perm->nm_permission_button !!}
                                </label><br>
                            @endforeach
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </x-theme.modal>
</form>
