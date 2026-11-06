@extends('layouts.app')

@section('title', 'Verify Registration Code — UNCART')

@section('content')
<div class="min-h-[80vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8" 
     x-data="{
         otp: '{{ old('otp', '') }}',
         secondsLeft: {{ $secondsLeft ?? 900 }},
         timerText: '15:00',
         timerInterval: null,
         init() {
             this.updateTimerText();
             this.timerInterval = setInterval(() => {
                 if (this.secondsLeft > 0) {
                     this.secondsLeft--;
                     this.updateTimerText();
                 } else {
                     clearInterval(this.timerInterval);
                 }
             }, 1000);
         },
         updateTimerText() {
             const m = Math.floor(this.secondsLeft / 60);
             const s = this.secondsLeft % 60;
             this.timerText = (m < 10 ? '0' : '') + m + ':' + (s < 10 ? '0' : '') + s;
         }
     }">
    <div class="max-w-md w-full space-y-6 bg-white p-8 sm:p-10 rounded-3xl border border-slate-100 shadow-xl" data-reveal="scale">
        
        <div class="text-center">
            <div class="mx-auto w-14 h-14 rounded-2xl bg-indigo-50 border border-indigo-100 text-indigo-600 flex items-center justify-center font-black text-2xl shadow-sm">
                📬
            </div>
            <h2 class="mt-4 text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">
                Verify Your Email
            </h2>
            <p class="mt-1 text-xs text-slate-500">
                We've sent a 6-digit one-time code to:
            </p>
            <p class="text-xs font-bold text-slate-900 font-mono mt-0.5">
                {{ $email }}
            </p>
            @if(!empty($role) && $role === 'vendor')
                <span class="inline-block mt-2 px-3 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-amber-50 text-amber-800 border border-amber-200">
                    🏪 Merchant Storefront Setup
                </span>
            @endif
        </div>

        @if(session('success'))
            <div class="p-3.5 rounded-2xl bg-emerald-50 border border-emerald-200 text-xs text-emerald-800 flex items-start gap-2.5">
                <span class="text-emerald-600 text-sm font-bold">✓</span>
                <span class="leading-relaxed">{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="p-3.5 rounded-2xl bg-amber-50 border border-amber-200 text-xs text-amber-800 flex items-start gap-2.5">
                <span class="text-amber-600 text-sm font-bold">⚠️</span>
                <span class="leading-relaxed">{{ session('error') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="p-3 rounded-xl bg-rose-50 border border-rose-200 text-xs text-rose-700">
                {{ $errors->first() }}
            </div>
        @endif

        <!-- OTP Verification Form -->
        <form action="{{ $verifyRoute ?? route('register.otp.verify') }}" method="POST" class="space-y-5">
            @csrf

            <div>
                <label for="otp" class="block text-xs font-semibold text-slate-700 mb-2 text-center uppercase tracking-wider text-[11px]">
                    Enter 6-Digit Code
                </label>
                <div class="relative">
                    <input id="otp" 
                           name="otp" 
                           type="text" 
                           required 
                           autofocus
                           maxlength="6" 
                           pattern="[0-9]{6}"
                           inputmode="numeric"
                           autocomplete="one-time-code"
                           x-model="otp"
                           placeholder="••••••"
                           class="w-full text-center font-mono tracking-[0.75em] text-2xl font-black rounded-2xl border border-slate-200 bg-slate-50 py-3.5 text-slate-900 focus:outline-none focus:border-indigo-600 focus:ring-2 focus:ring-indigo-600/20 transition-all placeholder:tracking-normal placeholder:font-normal placeholder:text-slate-300">
                </div>
                <div class="flex items-center justify-between text-[11px] text-slate-500 mt-2 px-1">
                    <span>Code expires in:</span>
                    <span class="font-mono font-bold" :class="secondsLeft < 120 ? 'text-rose-600' : 'text-slate-700'" x-text="timerText">15:00</span>
                </div>
            </div>

            <button type="submit" 
                    :disabled="otp.length < 6"
                    class="w-full py-3.5 px-4 rounded-full bg-slate-900 hover:bg-indigo-600 disabled:opacity-50 disabled:hover:bg-slate-900 text-white font-bold text-xs uppercase tracking-wider transition-all shadow-md active:scale-[0.98]">
                Confirm & Activate Account &rarr;
            </button>
        </form>

        <!-- Resend and Cancel options -->
        <div class="pt-4 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs">
            <form action="{{ $resendRoute ?? route('register.otp.resend') }}" method="POST">
                @csrf
                <button type="submit" class="font-bold text-indigo-600 hover:text-indigo-700 transition">
                    🔄 Resend Code
                </button>
            </form>

            <a href="{{ route('register') }}" class="text-slate-400 hover:text-slate-700 transition">
                Change Email / Restart
            </a>
        </div>

    </div>
</div>
@endsection
