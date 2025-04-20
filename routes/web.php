Route::get('/user/gmail', 'gmailSell')->name('user.gmail');
Route::post('/user/gmail', 'gmailSellMake');
Route::get('/user/activate', 'activateAccount')->name('user.activate.account');




//ADMIN ROUTES
Route::prefix('admin/gmail-sells')->name('admin.gmail-sells.')->group(function () {
    Route::get('/', [\App\Http\Controllers\AdminGmailController::class, 'index'])->name('index');
    Route::post('/approve/{id}', [\App\Http\Controllers\AdminGmailController::class, 'approve'])->name('approve');
    Route::patch('/reject/{id}', [\App\Http\Controllers\AdminGmailController::class, 'reject'])->name('reject');
    Route::delete('/delete/{id}', [\App\Http\Controllers\AdminGmailController::class, 'destroy'])->name('delete');
    Route::post('/update', [\App\Http\Controllers\AdminGmailController::class, 'updateSettings'])->name('update');

});
