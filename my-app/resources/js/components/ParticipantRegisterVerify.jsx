import React, { useState } from 'react';
import { Mail, Phone, AlertCircle, CheckCircle2, ArrowLeft, ShieldCheck } from 'lucide-react';

export function ParticipantRegisterVerify({ verificationMethod = 'email', contact = '', errors = {} }) {
    const [otp, setOtp] = useState('');
    const [isSubmitting, setIsSubmitting] = useState(false);
    const [resendCooldown, setResendCooldown] = useState(0);

    const handleSubmit = (e) => {
        e.preventDefault();
        setIsSubmitting(true);
        e.target.submit();
    };

    const handleOtpChange = (e) => {
        const value = e.target.value.replace(/\D/g, '').slice(0, 6);
        setOtp(value);
    };

    const handleResend = () => {
        // TODO: Implement resend logic
        setResendCooldown(60);
        const interval = setInterval(() => {
            setResendCooldown(prev => {
                if (prev <= 1) {
                    clearInterval(interval);
                    return 0;
                }
                return prev - 1;
            });
        }, 1000);
    };

    const getFieldError = (fieldName) => {
        if (errors[fieldName] && Array.isArray(errors[fieldName])) {
            return errors[fieldName][0];
        }
        return null;
    };

    const maskedContact = verificationMethod === 'email' 
        ? contact.replace(/(.{2})(.*)(@.*)/, '$1***$3')
        : contact.replace(/(.{3})(.*)(.{4})/, '$1***$3');

    return (
        <div className="min-h-screen bg-slate-100 flex items-center justify-center p-4">
            <div className="w-full max-w-md bg-white shadow-md rounded-xl p-8">
                <div className="mb-6">
                    <div className="flex items-center justify-center w-12 h-12 rounded-full bg-blue-100 mb-4 mx-auto">
                        <ShieldCheck className="w-6 h-6 text-blue-600" />
                    </div>
                    <h1 className="text-2xl font-semibold text-slate-800 mb-1 text-center">
                        Verify Your {verificationMethod === 'email' ? 'Email' : 'Phone'}
                    </h1>
                    <p className="text-sm text-slate-500 text-center">
                        {verificationMethod === 'email' 
                            ? `We've sent a 6-digit verification code to your email address.`
                            : `We've sent a 6-digit code to your phone number.`}
                    </p>
                </div>

                <div className="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-md">
                    <div className="flex items-center gap-3">
                        {verificationMethod === 'email' ? (
                            <Mail className="w-5 h-5 text-blue-600 flex-shrink-0" />
                        ) : (
                            <Phone className="w-5 h-5 text-blue-600 flex-shrink-0" />
                        )}
                        <div>
                            <p className="text-xs font-semibold text-slate-700 mb-0.5">
                                Code sent to:
                            </p>
                            <p className="text-sm text-slate-600 font-mono">{maskedContact}</p>
                        </div>
                    </div>
                </div>

                {Object.keys(errors).length > 0 && (
                    <div className="mb-4 rounded-md bg-rose-50 border border-rose-200 px-3 py-2 text-sm text-rose-700">
                        <div className="flex items-start gap-2">
                            <AlertCircle className="w-4 h-4 mt-0.5 flex-shrink-0" />
                            <div className="flex-1">
                                {Object.entries(errors).map(([field, messages]) => (
                                    <div key={field}>
                                        {Array.isArray(messages) ? messages.map((msg, i) => (
                                            <div key={i}>{msg}</div>
                                        )) : <div>{messages}</div>}
                                    </div>
                                ))}
                            </div>
                        </div>
                    </div>
                )}

                <form method="POST" action="/participant/register/verify" onSubmit={handleSubmit} className="space-y-4">
                    <input type="hidden" name="_token" value={document.head.querySelector('meta[name="csrf-token"]')?.content || ''} />
                    
                    <div>
                        <label className="block text-xs font-semibold text-slate-600 mb-1" htmlFor="otp">
                            Enter 6-digit verification code
                        </label>
                        <input
                            id="otp"
                            name="otp"
                            type="text"
                            value={otp}
                            onChange={handleOtpChange}
                            required
                            maxLength={6}
                            autoFocus
                            placeholder="000000"
                            className={`w-full rounded-md border ${
                                getFieldError('otp') ? 'border-rose-300' : 'border-slate-300'
                            } px-4 py-3 text-center text-2xl font-mono tracking-widest focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500`}
                        />
                        {getFieldError('otp') && (
                            <p className="mt-1 text-xs text-rose-600">{getFieldError('otp')}</p>
                        )}
                        <p className="mt-2 text-xs text-slate-500 text-center">
                            Check your {verificationMethod === 'email' ? 'email inbox' : 'phone'} for the verification code
                        </p>
                    </div>

                    <button
                        type="submit"
                        disabled={isSubmitting || otp.length !== 6}
                        className="w-full inline-flex justify-center items-center gap-2 rounded-md bg-blue-600 hover:bg-blue-700 disabled:bg-blue-400 disabled:cursor-not-allowed text-white text-sm font-medium px-4 py-2.5 transition-colors"
                    >
                        {isSubmitting ? (
                            <>
                                <div className="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin" />
                                <span>Verifying...</span>
                            </>
                        ) : (
                            <>
                                <CheckCircle2 className="w-4 h-4" />
                                <span>Verify & Complete Registration</span>
                            </>
                        )}
                    </button>

                    <div className="text-center">
                        <button
                            type="button"
                            onClick={handleResend}
                            disabled={resendCooldown > 0}
                            className="text-xs text-blue-600 hover:text-blue-700 font-medium disabled:text-slate-400 disabled:cursor-not-allowed"
                        >
                            {resendCooldown > 0 
                                ? `Resend code in ${resendCooldown}s`
                                : 'Resend verification code'}
                        </button>
                    </div>
                </form>

                <div className="mt-6 pt-6 border-t border-slate-200">
                    <a
                        href="/participant/register"
                        className="inline-flex items-center gap-2 text-xs text-slate-600 hover:text-slate-700 font-medium"
                    >
                        <ArrowLeft className="w-3 h-3" />
                        <span>Back to registration</span>
                    </a>
                </div>
            </div>
        </div>
    );
}

