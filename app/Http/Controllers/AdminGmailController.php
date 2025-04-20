<?php

namespace App\Http\Controllers;

use App\Models\BillPayment;
use App\Models\Gmail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class AdminGmailController extends Controller
{
    public function index()
    {
        $gmail_sells = Gmail::with('user')->get(); // Fetch all payment requests
       
        return view('admin.gmail-sells.index', compact('gmail_sells'));
    }

public function updateSettings(Request $request)
{
    $request->validate([
        'gmail_price' => 'required|numeric',
        'service_status' => 'required|in:0,1',
        'gmail_password' => 'required|string'
    ]);

    $updates = [
        'GMAIL_PRICE'     => $request->gmail_price,
        'GMAIL_STATUS'    => $request->service_status,
        'GMAIL_PASSWORD'  => $request->gmail_password,
    ];

    foreach ($updates as $key => $value) {
        $this->setEnv($key, $value);
    }

    return back()->with('success', 'Gmail settings updated successfully.');
}

protected function setEnv($key, $value)
{
    $envPath = base_path('.env');
    $escapedValue = '"' . addslashes($value) . '"';

    if (File::exists($envPath)) {
        $envContent = File::get($envPath);

        $pattern = "/^$key=.*$/m";

        if (preg_match($pattern, $envContent)) {
            $envContent = preg_replace($pattern, "$key=$escapedValue", $envContent);
        } else {
            $envContent .= "\n$key=$escapedValue";
        }

        File::put($envPath, $envContent);
    }
}

public function approve($id)
{
    $sell = Gmail::findOrFail($id);

    if ($sell->status !== 'Pending') {
        return back()->with('error', 'This request has already been processed.');
    }

    $sell->status = 'Approved';
    $sell->save();

    // Add to user's total_wallet_amount
    $user = $sell->user;
    if ($user) {
        $price = env('GMAIL_PRICE', 0);
        $user->increment('main_balance', $price);
    }

    return back()->with('success', 'Gmail request approved and user credited.');
}
public function destroy($id)
{
    $sell = Gmail::findOrFail($id);


    $sell->delete();

    return back()->with('success', 'Gmail sell request deleted successfully.');
}


public function reject($id)
{
    $sell = Gmail::findOrFail($id);

    if ($sell->status !== 'Pending') {
        return back()->with('error', 'This request has already been processed.');
    }

    $sell->status = 'Rejected';
    $sell->save();

    return back()->with('success', 'Gmail request rejected.');
}


}
