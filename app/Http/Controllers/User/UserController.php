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
