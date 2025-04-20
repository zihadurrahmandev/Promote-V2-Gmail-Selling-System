Route::post('/tzsmmpay/{user_id}', [App\Http\Controllers\User\UserController::class, 'tzsmmpayCallback'])->name('tzsmmpayCallback');
