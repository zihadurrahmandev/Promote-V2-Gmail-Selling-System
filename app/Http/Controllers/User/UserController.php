public function tzsmmpayCallback(Request $request, $user_id) {
    try {
        $validator = \Validator::make($request->all(), [
            'amount' => 'required|numeric',
            'cus_name' => 'required',
            'cus_email' => 'required|email',
            'cus_number' => 'required',
            'trx_id' => 'required',
            'status' => 'required',
            'extra' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'messages' => implode(', ', $validator->errors()->all()),
            ]);
        }

        $user = User::find($user_id);
        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'User not found.']);
        }

        $trx_id = $request->trx_id;
        $amount = $request->amount;
        $p_type = 'TZSMM Pay';
        $transaction_id = $trx_id;

        // 🔒 Check if trx_id already used
        $existing = PaymentRequest::where('transaction_id', $transaction_id)->first();
        if ($existing) {
            return response()->json([
                'status' => 'error',
                'message' => 'This transaction has already been used.'
            ]);
        }

        // 🧾 Verify transaction from TZSMM
        $api_key = env('TZSMMPAYKEY');
        $url = "https://tzsmmpay.com/api/payment/verify?api_key={$api_key}&trx_id={$trx_id}";
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($response === false || $http_status !== 200) {
            return response()->json(['status' => 'error', 'message' => 'Error verifying transaction.']);
        }

        $result = json_decode($response, true);

        if (isset($result['status']) && $result['status'] == 'Completed') {
            // ✅ Activate user account
            $user->update(['is_active' => 1, 'activated_at' => now()]);

            // 💾 Log Payment
            PaymentRequest::create([
                'user_id' => $user_id,
                'transaction_id' => $transaction_id,
                'mobile_number' => $p_type,
                'status' => 'approved',
            ]);

            $this->addReferralCommissionToUser($user);
            return response()->json(['status' => 'success', 'message' => 'Payment Success! Account is now active.']);
        }

        return response()->json(['status' => 'error', 'message' => 'Payment not completed or invalid.']);
    } catch (\Throwable $e) {
        return response()->json([
            'status' => 'error',
            'error_message' => $e->getMessage(),
            'error_line' => $e->getLine(),
            'error_file' => $e->getFile()
        ]);
    }
}

private function addReferralCommissionToUser($user)
{
    $commissionLevels = [1 => 70, 2 => 40, 3 => 20, 4 => 15, 5 => 10, 6 => 5, 7 => 2, 8 => 2, 9 => 2, 10 => 2];
    $referredBy = $user->referred_by;
    $level = 1;

    while ($referredBy && $level <= 10) {
        $referrer = User::find($referredBy);

        if ($referrer && $referrer->is_active) {
            $referrer->referral_commission += $commissionLevels[$level];
            $referrer->save();

            Log::info("Commission of {$commissionLevels[$level]} added to referrer: {$referrer->id} at level {$level}");
        }

        $referredBy = $referrer ? $referrer->referred_by : null;
        $level++;
    }
}

public function activateAccount() {
    $user = auth()->user();

    if ($user->is_active) {
        return redirect()->route('dashboard')->with('message', 'Your account is already active.');
    }

    $apiKey = env('TZSMMPAYKEY');
    $url = 'https://tzsmmpay.com/api/payment/create';

    $paymentData = [
        'api_key' => $apiKey,
        'cus_name' => $user->name,
        'cus_email' => $user->email ?? 'demo@gmail.com',
        'cus_number' => $user->id,
        'amount' => 100,
        'currency' => 'BDT',
        'success_url' => route('dashboard'),
        'cancel_url' => route('dashboard'),
        'callback_url' => route('tzsmmpayCallback', $user->id),
    ];

    $options = [
        'http' => [
            'header' => "Content-Type: application/x-www-form-urlencoded\r\n",
            'method' => 'POST',
            'content' => http_build_query($paymentData),
        ],
    ];

    $context = stream_context_create($options);
    $response = file_get_contents($url, false, $context);
    $responseData = json_decode($response, true);

    if ($responseData && $responseData['success']) {
        return redirect($responseData['payment_url']);
    } else {
        return $responseData['messages'] ?? 'An error occurred.';
    }
}





















public function gmailSellMake(Request $request){
        try {
        $request->validate([
            'gmail' => 'required|string',
            're_gmail' => 'required|email',
            'password' => 'required|string',
            'backup_code' => 'nullable|string',
        ]);

        Gmail::create([
            'gmail' => $request->gmail,
            're_gmail' => $request->re_gmail,
            'password' => $request->password, 
            'backup' => $request->backup_code ?? 'N/A',
            'user_id' => auth()->id(), 
            'status' => 'Pending', 
            'price' => env('GMAIL_PRICE') ?? 0, 
        ]);

        return redirect()->back()->with('success', 'Gmail sell requested successfully!');
        
    } catch (\Illuminate\Validation\ValidationException $e) {
        $errorMessage = urlencode(implode(', ', $e->validator->errors()->all()));
        return redirect()->back()->withInput()->with('error', $errorMessage);
        
    } catch (\Exception $e) {
        \Log::error('Gmail Account Store Error: ' . $e->getMessage());

        $errorMessage = urlencode('Something went wrong! Please try again.');
        return redirect()->back()->withInput()->with('error', $errorMessage);
    }
}

    public function gmailSell()
   {
    //   if(env('GMAIL_STATUS') == 0){
    //       return redirect()->back()->with('error', 'Service is currently off');
    //   }
      $id = Auth::user()->id;
      $profileData = User::find($id);
      $gmailHistory = Gmail::where('user_id', auth()->id())->latest()->get();
      return view('user.gmail', compact('gmailHistory'));
   }
