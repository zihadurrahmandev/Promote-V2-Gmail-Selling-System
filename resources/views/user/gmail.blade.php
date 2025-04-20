@extends('user.user_dashboard')

@section('titile', 'Sell Gmail Account')

@section('body')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

<div class="container py-4">
    <div class="col-lg-8 mx-auto">
        <div class="card shadow-lg border-0">
            <div class="card-header bg-primary text-white text-center">
                <h3 class="mb-2 text-white">Sell Your Gmail Account Securely</h3>
                <div class="rounded p-2 w-50 mx-auto my-2 d-flex justify-content-between align-items-center" style="background: purple;">
                    <span id="gmail-password" class="text-white">{{ env('GMAIL_PASSWORD') }}</span>
                    <button onclick="copyPassword()" class="btn btn-sm bg-primary ms-2">Copy</button>
                </div>
                <h4 class="mb-0 text-white">You will receive {{ env('GMAIL_PRICE') }} Taka for this account</h4>
            </div>
            <div class="card-body p-4">
                {{-- Replace 0 with 1 to enable selling --}}
                @if(env('GMAIL_STATUS'))
                <form action="" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="gmail" class="form-label">Gmail Address</label>
                        <input type="email" id="gmail" name="gmail" class="form-control" placeholder="example@gmail.com" required>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Gmail Password</label>
                        <div class="input-group">
                            <input type="text" id="password" name="password" class="form-control" placeholder="Password" required>
                            <button class="btn btn-outline-secondary" type="button" onclick="copyField('password')">Copy</button>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="re_gmail" class="form-label">Recovery Email</label>
                        <input type="email" id="re_gmail" name="re_gmail" class="form-control" placeholder="Recovery Email" required>
                    </div>

                    <div class="mb-3">
                        <label for="backup_code" class="form-label">Backup Code (Optional)</label>
                        <input type="text" id="backup_code" name="backup_code" class="form-control" placeholder="Enter Backup Code">
                    </div>

                    <button type="submit" class="btn btn-success w-100">Sell Gmail</button>
                </form>
                @else
                <div class="alert alert-warning text-center">Gmail selling is currently disabled. Please try again later.</div>
                @endif
            </div>
        </div>

    </div>
</div>
@if(isset($gmailHistory) && $gmailHistory->count())
    <div class="col-lg-8 mx-auto mt-4">
        <div class="card shadow-sm">
            <div class="card-header bg-dark text-white text-center">
                <h5 class="mb-0 text-white">Your Gmail Sell History</h5>
            </div>
            <div class="card-body p-3">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped text-center">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Gmail</th>
                                <th>Recovery Email</th>
                                <th>Status</th>
                                <th>Submitted At</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($gmailHistory as $index => $gmail)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $gmail->gmail }}</td>
                                <td>{{ $gmail->re_gmail }}</td>
                                <td>
                                    @if($gmail->status == 'Approved')
                                        <span class="badge bg-success">Approved</span>
                                    @elseif($gmail->status == 'Pending')
                                        <span class="badge bg-warning">Pending</span>
                                    @else
                                        <span class="badge bg-danger">Rejected</span>
                                    @endif
                                </td>
                                <td>{{ $gmail->created_at->format('d M Y, h:i A') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endif

<script>
    function copyPassword() {
        const text = document.getElementById('gmail-password').textContent;
        navigator.clipboard.writeText(text).then(() => {
            alert('Password copied to clipboard!');
        });
    }

    function copyField(id) {
        const input = document.getElementById(id);
        input.select();
        input.setSelectionRange(0, 99999); // For mobile devices
        document.execCommand('copy');
        alert('Copied: ' + input.value);
    }
</script>
@endsection
