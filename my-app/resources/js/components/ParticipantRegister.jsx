import React, { useState } from 'react';
import { Mail, Lock, User, Phone, AlertCircle, UserPlus, ArrowRight } from 'lucide-react';

export function ParticipantRegister({ errors = {}, oldValues = {} }) {
    const [registrationMethod, setRegistrationMethod] = useState('email'); // 'email' or 'phone'
    const [formData, setFormData] = useState({
        name: oldValues.name || '',
        email: oldValues.email || '',
        phone: oldValues.phone || '',
        password: '',
        password_confirmation: '',
    });
    const [isSubmitting, setIsSubmitting] = useState(false);

    const handleChange = (e) => {
        const { name, value } = e.target;
        setFormData(prev => ({ ...prev, [name]: value }));
    };

    const handleMethodChange = (method) => {
        setRegistrationMethod(method);
        // Clear the other field when switching
        if (method === 'email') {
            setFormData(prev => ({ ...prev, phone: '' }));
        } else {
            setFormData(prev => ({ ...prev, email: '' }));
        }
    };

    const handleSubmit = (e) => {
        e.preventDefault();
        setIsSubmitting(true);
        e.target.submit();
    };

    const hasErrors = Object.keys(errors).length > 0;
    const getFieldError = (fieldName) => {
        if (errors[fieldName] && Array.isArray(errors[fieldName])) {
            return errors[fieldName][0];
        }
        return null;
    };

    return (
        <div className="min-h-screen bg-slate-100 flex items-center justify-center p-4">
            <div className="w-full max-w-md bg-white shadow-md rounded-xl p-8">
                <div className="mb-6">
                    <h1 className="text-2xl font-semibold text-slate-800 mb-1">
                        Participant Registration
                    </h1>
                    <p className="text-sm text-slate-500">
                        Create an account to access training modules and simulation events.
                    </p>
                </div>

                {/* Registration Method Toggle */}
                <div className="mb-6">
                    <label className="block text-xs font-semibold text-slate-600 mb-2">
                        Register with:
                    </label>
                    <div className="grid grid-cols-2 gap-2">
                        <button
                            type="button"
                            onClick={() => handleMethodChange('email')}
                            className={`flex items-center justify-center gap-2 px-4 py-2.5 rounded-md text-sm font-medium transition-colors ${
                                registrationMethod === 'email'
                                    ? 'bg-blue-600 text-white'
                                    : 'bg-slate-100 text-slate-700 hover:bg-slate-200'
                            }`}
                        >
                            <Mail className="w-4 h-4" />
                            <span>Email</span>
                        </button>
                        <button
                            type="button"
                            onClick={() => handleMethodChange('phone')}
                            className={`flex items-center justify-center gap-2 px-4 py-2.5 rounded-md text-sm font-medium transition-colors ${
                                registrationMethod === 'phone'
                                    ? 'bg-blue-600 text-white'
                                    : 'bg-slate-100 text-slate-700 hover:bg-slate-200'
                            }`}
                        >
                            <Phone className="w-4 h-4" />
                            <span>Phone</span>
                        </button>
                    </div>
                </div>

                {hasErrors && Object.keys(errors).some(key => !['name', 'email', 'phone', 'password', 'password_confirmation'].includes(key)) && (
                    <div className="mb-4 rounded-md bg-rose-50 border border-rose-200 px-3 py-2 text-sm text-rose-700">
                        <div className="flex items-start gap-2">
                            <AlertCircle className="w-4 h-4 mt-0.5 flex-shrink-0" />
                            <div className="flex-1">
                                {Object.entries(errors)
                                    .filter(([key]) => !['name', 'email', 'phone', 'password', 'password_confirmation'].includes(key))
                                    .map(([field, messages]) => (
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

                <form method="POST" action="/participant/register/start" onSubmit={handleSubmit} className="space-y-4">
                    <input type="hidden" name="_token" value={document.head.querySelector('meta[name="csrf-token"]')?.content || ''} />

                    <div>
                        <label className="block text-xs font-semibold text-slate-600 mb-1" htmlFor="name">
                            Full Name
                        </label>
                        <div className="relative">
                            <User className="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" />
                            <input
                                id="name"
                                name="name"
                                type="text"
                                value={formData.name}
                                onChange={handleChange}
                                required
                                autoFocus
                                className={`w-full rounded-md border ${
                                    getFieldError('name') ? 'border-rose-300' : 'border-slate-300'
                                } pl-10 pr-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500`}
                            />
                        </div>
                        {getFieldError('name') && (
                            <p className="mt-1 text-xs text-rose-600">{getFieldError('name')}</p>
                        )}
                    </div>

                    {registrationMethod === 'email' ? (
                        <div>
                            <label className="block text-xs font-semibold text-slate-600 mb-1" htmlFor="email">
                                Email <span className="text-rose-500">*</span>
                            </label>
                            <div className="relative">
                                <Mail className="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" />
                                <input
                                    id="email"
                                    name="email"
                                    type="email"
                                    value={formData.email}
                                    onChange={handleChange}
                                    required
                                    className={`w-full rounded-md border ${
                                        getFieldError('email') ? 'border-rose-300' : 'border-slate-300'
                                    } pl-10 pr-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500`}
                                />
                            </div>
                            {getFieldError('email') && (
                                <p className="mt-1 text-xs text-rose-600">{getFieldError('email')}</p>
                            )}
                        </div>
                    ) : (
                        <div>
                            <label className="block text-xs font-semibold text-slate-600 mb-1" htmlFor="phone">
                                Phone Number <span className="text-rose-500">*</span>
                            </label>
                            <div className="relative">
                                <Phone className="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" />
                                <input
                                    id="phone"
                                    name="phone"
                                    type="tel"
                                    value={formData.phone}
                                    onChange={handleChange}
                                    required
                                    placeholder="+1234567890"
                                    className={`w-full rounded-md border ${
                                        getFieldError('phone') ? 'border-rose-300' : 'border-slate-300'
                                    } pl-10 pr-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500`}
                                />
                            </div>
                            {getFieldError('phone') && (
                                <p className="mt-1 text-xs text-rose-600">{getFieldError('phone')}</p>
                            )}
                        </div>
                    )}

                    <div>
                        <label className="block text-xs font-semibold text-slate-600 mb-1" htmlFor="password">
                            Password
                        </label>
                        <div className="relative">
                            <Lock className="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" />
                            <input
                                id="password"
                                name="password"
                                type="password"
                                value={formData.password}
                                onChange={handleChange}
                                required
                                minLength={8}
                                className={`w-full rounded-md border ${
                                    getFieldError('password') ? 'border-rose-300' : 'border-slate-300'
                                } pl-10 pr-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500`}
                            />
                        </div>
                        {getFieldError('password') && (
                            <p className="mt-1 text-xs text-rose-600">{getFieldError('password')}</p>
                        )}
                        <p className="mt-1 text-xs text-slate-500">Must be at least 8 characters</p>
                    </div>

                    <div>
                        <label className="block text-xs font-semibold text-slate-600 mb-1" htmlFor="password_confirmation">
                            Confirm Password
                        </label>
                        <div className="relative">
                            <Lock className="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" />
                            <input
                                id="password_confirmation"
                                name="password_confirmation"
                                type="password"
                                value={formData.password_confirmation}
                                onChange={handleChange}
                                required
                                className={`w-full rounded-md border ${
                                    getFieldError('password_confirmation') ? 'border-rose-300' : 'border-slate-300'
                                } pl-10 pr-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500`}
                            />
                        </div>
                        {getFieldError('password_confirmation') && (
                            <p className="mt-1 text-xs text-rose-600">{getFieldError('password_confirmation')}</p>
                        )}
                    </div>

                    <button
                        type="submit"
                        disabled={isSubmitting}
                        className="w-full inline-flex justify-center items-center gap-2 rounded-md bg-blue-600 hover:bg-blue-700 disabled:bg-blue-400 disabled:cursor-not-allowed text-white text-sm font-medium px-4 py-2.5 transition-colors"
                    >
                        {isSubmitting ? (
                            <>
                                <div className="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin" />
                                <span>Processing...</span>
                            </>
                        ) : (
                            <>
                                <span>Continue</span>
                                <ArrowRight className="w-4 h-4" />
                            </>
                        )}
                    </button>
                </form>

                <div className="mt-6 space-y-2">
                    <p className="text-xs text-slate-500 text-center">
                        Already have an account?{' '}
                        <a href="/participant/login" className="text-blue-600 hover:text-blue-700 font-medium">
                            Login here
                        </a>
                    </p>
                    <p className="text-xs text-slate-500 text-center">
                        Are you an admin or trainer?{' '}
                        <a href="/login" className="text-slate-600 hover:text-slate-700 font-medium">
                            Admin/Trainer Login
                        </a>
                    </p>
                </div>
            </div>
        </div>
    );
}
