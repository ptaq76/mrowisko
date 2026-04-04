@extends('layouts.app')

@section('title', 'Użytkownicy')
@section('module_name', 'ADMINISTRATOR')

@section('nav_menu')
    <div class="dropdown">
        <button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown">
            <i class="fa-solid fa-users"></i> Użytkownicy
        </button>
        <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="{{ route('admin.users.index') }}">
                <i class="fa-solid fa-list"></i> Lista użytkowników
            </a></li>
            <li><a class="dropdown-item" href="{{ route('admin.users.create') }}">
                <i class="fa-solid fa-user-plus"></i> Nowy użytkownik
            </a></li>
        </ul>
    </div>
@endsection

@section('content')

<div class="page-header">
    <h1>Użytkownicy systemu</h1>
    <a href="{{ route('admin.users.create') }}" class="btn btn-add">
        <i class="fa-solid fa-plus"></i> Nowy użytkownik
    </a>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Imię i nazwisko</th>
                    <th>Login</th>
                    <th>Moduł</th>
                    <th style="width:160px">Akcje</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                <tr>
                    <td>
                        {{ $user->name }}
                        @if($user->id === auth()->id())
                            <span class="badge bg-success ms-1">Ty</span>
                        @endif
                    </td>
                    <td><code>{{ $user->login }}</code></td>
                    <td>
                        @php
                            $badgeClass = match($user->module) {
                                'admin'      => 'bg-danger',
                                'biuro'      => 'bg-primary',
                                'kierowca'   => 'bg-warning text-dark',
                                'hakowiec'   => 'bg-warning text-dark',
                                'plac'       => 'bg-success',
                                'handlowiec' => 'bg-secondary',
                                default      => 'bg-secondary',
                            };
                        @endphp
                        <span class="badge {{ $badgeClass }}">{{ $user->moduleName() }}</span>
                    </td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="{{ route('admin.users.edit', $user) }}"
                               class="btn btn-edit btn-sm" title="Edytuj">
                                <i class="fa-solid fa-pen"></i>
                            </a>

                            <button type="button"
                                    class="btn btn-password btn-sm"
                                    title="Zmień hasło"
                                    data-bs-toggle="modal"
                                    data-bs-target="#passwordModal"
                                    data-user-id="{{ $user->id }}"
                                    data-user-name="{{ $user->name }}">
                                <i class="fa-solid fa-key"></i>
                            </button>

                            @if($user->id !== auth()->id())
                            <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                                  onsubmit="return confirmDelete(this)">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" title="Usuń">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center text-muted py-4">Brak użytkowników.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Modal: zmiana hasła --}}
<div class="modal fade" id="passwordModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fa-solid fa-key me-2"></i>
                    Zmiana hasła – <span id="modalUserName"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="passwordForm" method="POST">
                @csrf @method('PATCH')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nowe hasło</label>
                        <input type="text" name="password" id="modalPassword"
                               class="form-control" placeholder="Min. 6 znaków"
                               autocomplete="off">
                        <div id="modalPasswordError" class="text-danger small mt-1" style="display:none"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fa-solid fa-xmark"></i> Anuluj
                    </button>
                    <button type="submit" class="btn btn-save">
                        <i class="fa-solid fa-key"></i> Zmień hasło
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
const passwordModal = document.getElementById('passwordModal');
passwordModal.addEventListener('show.bs.modal', function(e) {
    const btn = e.relatedTarget;
    document.getElementById('modalUserName').textContent = btn.dataset.userName;
    document.getElementById('passwordForm').action = '/admin/users/' + btn.dataset.userId + '/password';
    document.getElementById('modalPassword').value = '';
    document.getElementById('modalPasswordError').style.display = 'none';
});

document.getElementById('passwordForm').addEventListener('submit', function(e) {
    const pass = document.getElementById('modalPassword').value;
    const err  = document.getElementById('modalPasswordError');
    if (pass.length < 6) {
        e.preventDefault();
        err.textContent = 'Hasło musi mieć co najmniej 6 znaków.';
        err.style.display = 'block';
    }
});
</script>
@endsection
