import React from 'react';
import ReactDOM from 'react-dom/client';
import '../css/app.css';
import { ParticipantLogin } from './components/ParticipantLogin';
import { ParticipantRegister } from './components/ParticipantRegister';
import { ParticipantRegisterVerify } from './components/ParticipantRegisterVerify';

// Participant Login
const participantLoginRoot = document.getElementById('participant-login-root');
if (participantLoginRoot) {
    const errorsJson = participantLoginRoot.getAttribute('data-errors') || '[]';
    const oldEmail = participantLoginRoot.getAttribute('data-old-email') || '';
    const errors = JSON.parse(errorsJson);
    const errorsObj = {};
    if (errors.length > 0) {
        errorsObj.email = errors[0];
    }
    
    const root = ReactDOM.createRoot(participantLoginRoot);
    root.render(React.createElement(ParticipantLogin, { errors: errorsObj, oldEmail }));
}

// Participant Register
const participantRegisterRoot = document.getElementById('participant-register-root');
if (participantRegisterRoot) {
    const errorsJson = participantRegisterRoot.getAttribute('data-errors') || '{}';
    const oldValuesJson = participantRegisterRoot.getAttribute('data-old-values') || '{}';
    let errors = {};
    let oldValues = {};
    
    try {
        errors = JSON.parse(errorsJson);
    } catch (e) {
        console.error('Error parsing errors JSON:', e);
    }
    
    try {
        oldValues = JSON.parse(oldValuesJson);
    } catch (e) {
        console.error('Error parsing old values JSON:', e);
    }
    
    // Laravel validation errors come as { field: [messages] }
    // Pass them directly to the component
    const root = ReactDOM.createRoot(participantRegisterRoot);
    root.render(React.createElement(ParticipantRegister, { errors, oldValues }));
}

// Participant Register Verify
const participantRegisterVerifyRoot = document.getElementById('participant-register-verify-root');
if (participantRegisterVerifyRoot) {
    const verificationMethod = participantRegisterVerifyRoot.getAttribute('data-verification-method') || 'email';
    const contact = participantRegisterVerifyRoot.getAttribute('data-contact') || '';
    const errorsJson = participantRegisterVerifyRoot.getAttribute('data-errors') || '{}';
    let errors = {};
    
    try {
        errors = JSON.parse(errorsJson);
    } catch (e) {
        console.error('Error parsing errors JSON:', e);
    }
    
    const root = ReactDOM.createRoot(participantRegisterVerifyRoot);
    root.render(React.createElement(ParticipantRegisterVerify, { 
        verificationMethod, 
        contact, 
        errors 
    }));
}

