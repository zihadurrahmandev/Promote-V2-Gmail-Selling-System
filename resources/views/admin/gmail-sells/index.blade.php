@extends('admin.admin_dashboard')

@section('title', 'Gmail Sell Requests')

@section('body')
<div class="container-fluid">
<div class="row mb-4">
  <div class="col-lg-12">
    <div class="card border-0 shadow rounded-3">
      <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center rounded-top">
        <h5 class="mb-0">
          <i class="bi bi-gear me-2 text-white"></i>Gmail Settings
        </h5>
      </div>

      <div class="card-body">
        <form action="{{ route('admin.gmail-sells.update') }}" method="POST">
          @csrf
          <div class="row g-4">
            <div class="col-md-4">
              <label for="gmail_price" class="form-label fw-semibold">Gmail Price</label>
              <div class="input-group">
                <input type="number" step="0.01" value="{{ env('GMAIL_PRICE') }}" name="gmail_price" id="gmail_price" class="form-control" required>
              </div>
            </div>

            <div class="col-md-4">
              <label for="service_status" class="form-label fw-semibold">Service Status</label>
              <select name="service_status" id="service_status" class="form-select" required>
                <option value="1" {{ env('GMAIL_STATUS') == 1 ? 'selected' : '' }}>Yes</option>
                <option value="0" {{ env('GMAIL_STATUS') == 0 ? 'selected' : '' }}>No</option>
              </select>
            </div>

            <div class="col-md-4">
              <label for="gmail_password" class="form-label fw-semibold">Gmail Password</label>
              <input type="text" value="{{ env('GMAIL_PASSWORD') }}" name="gmail_password" id="gmail_password" class="form-control" required>
            </div>
          </div>

          <div class="text-end mt-4">
            <button type="submit" class="btn btn-success px-4">
              <i class="bi bi-save me-1"></i> Save Settings
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

  {{-- Sell Requests Table --}}
  <div class="row">
    <div class="col-sm-12">
      <div class="card shadow-sm">
        <div class="card-header pb-0 card-no-border">
          <h4>Gmail Sell Requests</h4>

          @if(session('success'))
              <div class="alert alert-success mt-2">{{ session('success') }}</div>
          @endif
          @if(session('error'))
              <div class="alert alert-danger mt-2">{{ session('error') }}</div>
          @endif
        </div>

        <div class="card-body">
          <div class="table-responsive theme-scrollbar">
            <table class="table table-striped table-bordered align-middle" id="gmail_table" style="width:100%">
              <thead class="table-light">
                <tr>
                  <th>ID</th>
                  <th>User</th>
                  <th>Gmail</th>
                  <th>Password</th>
                  <th>Recovery Email</th>
                  <th>Backup Code</th>
                  <th>Status</th>
                  <th>Submitted At</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                @foreach($gmail_sells as $sell)
                  <tr>
                    <td>{{ $sell->id }}</td>
                    <td>{{ $sell->user->name ?? 'Unknown' }}</td>
                    <td>
                        {{ $sell->gmail }}
                        <button class="btn btn-sm btn-outline-secondary" onclick="copyText('{{ $sell->gmail }}')">📋</button>
                    </td>
                    <td>
                        {{ $sell->password }}
                        <button class="btn btn-sm btn-outline-secondary" onclick="copyText('{{ $sell->password }}')">📋</button>
                    </td>
                    <td>
                        {{ $sell->recovery_email }}
                        <button class="btn btn-sm btn-outline-secondary" onclick="copyText('{{ $sell->recovery_email }}')">📋</button>
                    </td>
                    <td>
                        {{ $sell->backup_code ?? '-' }}
                        @if($sell->backup_code)
                          <button class="btn btn-sm btn-outline-secondary" onclick="copyText('{{ $sell->backup_code }}')">📋</button>
                        @endif
                    </td>
                    <td><span class="badge bg-{{ $sell->status === 'pending' ? 'warning' : ($sell->status === 'approved' ? 'success' : 'secondary') }}">{{ ucfirst($sell->status) }}</span></td>
                    <td>{{ $sell->created_at->format('d M, Y h:i A') }}</td>
                    <td>
                      @if($sell->status === 'Pending')
                        <form action="{{ route('admin.gmail-sells.approve', $sell->id) }}" method="POST" class="d-inline-block mb-1">
                            @csrf
                            <button type="submit" class="btn btn-success btn-sm">Accept</button>
                        </form>

                        <form action="{{ route('admin.gmail-sells.reject', $sell->id) }}" method="POST" class="d-inline-block mb-1" onsubmit="return confirm('Reject this Gmail sell request?')">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-warning btn-sm">Reject</button>
                        </form>
                      @endif

                      <form action="{{ route('admin.gmail-sells.delete', $sell->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Are you sure you want to delete this record?')">
                          @csrf
                          @method('DELETE')
                          <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                      </form>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>

<script>
    function copyText(text) {
        navigator.clipboard.writeText(text).then(() => {
            alert("Copied to clipboard!");
        });
    }
</script>
@endsection
