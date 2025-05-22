@extends('layouts.master')
  
@section('title')
    Website Speed Test - Global Performance Analytics
@endsection
  
@section('css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.21.0/dist/sweetalert2.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
<style>
    /* Core Variables */
:root {
    --primary-color: #4f46e5;
    --primary-light: #818cf8;
    --primary-dark: #3730a3;
    --primary-gradient: linear-gradient(135deg, #4f46e5, #3730a3);
    --success-color: #10b981;
    --warning-color: #f59e0b;
    --danger-color: #ef4444;
    --info-color: #3b82f6;
    
    --neutral-50: #fafafa;
    --neutral-100: #f5f5f5;
    --neutral-200: #e5e5e5;
    --neutral-300: #d4d4d4;
    --neutral-400: #a3a3a3;
    --neutral-500: #737373;
    --neutral-600: #525252;
    --neutral-700: #404040;
    --neutral-800: #262626;
    --neutral-900: #171717;
    
    --card-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    --card-shadow-hover: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    --section-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    
    --transition-normal: all 0.3s ease;
    --transition-fast: all 0.15s ease;
    --transition-slow: all 0.5s ease;
    
    --section-gap: 3rem;
    --card-radius: 1rem;
    --btn-radius: 0.5rem;
}

/* Base layout */
.speed-test-container {
    max-width: 1280px;
    margin: 0 auto;
    padding: 0 1rem;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
    color: var(--neutral-800);
}

/* Header Section */
.hero-section {
    position: relative;
    background: var(--primary-gradient);
    color: white;
    padding: 3rem 0;
    margin-bottom: 2rem;
    border-radius: 1rem;
    overflow: hidden;
    text-align: center;
}

.hero-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
    z-index: 0;
}

.hero-content {
    position: relative;
    z-index: 1;
}

.hero-title {
    font-size: 3rem;
    font-weight: 800;
    color: #fff;
    margin-bottom: 1rem;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.hero-description {
    max-width: 800px;
    margin: 0 auto 1.5rem;
    font-size: 1.2rem;
    line-height: 1.6;
    opacity: 0.9;
}

/* Input Form */
.input-section {
    position: relative;
    margin: -3rem auto 0;
    max-width: 900px;
    padding: 0 1rem;
    z-index: 10;
}

.url-input-container {
    background-color: white;
    padding: 2rem;
    border-radius: var(--card-radius);
    box-shadow: var(--card-shadow);
    transition: var(--transition-normal);
}

.url-input-container:hover {
    box-shadow: var(--card-shadow-hover);
}

.url-input-form {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
}

.url-input-wrapper {
    flex: 1;
    min-width: 250px;
    position: relative;
}

.url-input-icon {
    position: absolute;
    left: 1rem;
    top: 50%;
    transform: translateY(-50%);
    color: var(--neutral-400);
    transition: var(--transition-normal);
}

.url-input {
    width: 100%;
    padding: 1rem 1rem 1rem 2.75rem;
    border: 1px solid var(--neutral-300);
    border-radius: var(--btn-radius);
    font-size: 1.1rem;
    transition: var(--transition-normal);
}

.url-input:focus {
    border-color: var(--primary-color);
    outline: none;
    box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.2);
}

.url-input:focus + .url-input-icon {
    color: var(--primary-color);
}

.url-submit-btn {
    padding: 1rem 2rem;
    background-color: var(--primary-color);
    color: white;
    border: none;
    border-radius: var(--btn-radius);
    font-weight: 600;
    font-size: 1.1rem;
    cursor: pointer;
    transition: var(--transition-normal);
    display: flex;
    align-items: center;
    gap: 0.5rem;
    position: relative;
    overflow: hidden;
}

.url-submit-btn:hover {
    background-color: var(--primary-dark);
    transform: translateY(-2px);
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

.url-submit-btn:active {
    transform: translateY(1px);
}

/* Cancel button for loading state */
.cancel-test-btn {
    margin-top: 1rem;
    padding: 0.5rem 1rem;
    background-color: var(--neutral-200);
    color: var(--neutral-700);
    border: none;
    border-radius: var(--btn-radius);
    font-weight: 500;
    font-size: 0.9rem;
    cursor: pointer;
    transition: var(--transition-normal);
}

.cancel-test-btn:hover {
    background-color: var(--neutral-300);
}

/* Test Options - MODIFIED */
.test-options {
    display: flex;
    flex-direction: row;
    flex-wrap: nowrap;
    align-items: center;
    justify-content: space-between;
    margin-top: 1.5rem;
    padding: 1.25rem;
    background-color: white;
    border: 1px solid var(--neutral-200);
    border-radius: 0.75rem;
    gap: 1.5rem;
}

.option-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    position: relative;
    flex: 0 0 auto;
    min-height: 42px;
}

.option-help-text {
    margin-left: 0.25rem;
    color: var(--neutral-500);
    cursor: help;
    transition: color 0.2s ease;
    font-size: 0.8rem;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 16px;
    height: 16px;
    border-radius: 50%;
    background-color: var(--neutral-200);
}

.option-help-text:hover {
    color: var(--primary-color);
    background-color: var(--neutral-300);
}

/* Toggle Switch - MODIFIED */
.toggle-switch {
    position: relative;
    display: inline-block;
    width: 44px;
    height: 24px;
    margin: 0;
    vertical-align: middle;
}

.toggle-switch input {
    opacity: 0;
    width: 0;
    height: 0;
}

.toggle-slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: var(--neutral-300);
    transition: .4s cubic-bezier(0.68, -0.55, 0.27, 1.55);
    border-radius: 34px;
}

.toggle-slider:before {
    position: absolute;
    content: "";
    height: 18px;
    width: 18px;
    left: 3px;
    bottom: 3px;
    background-color: white;
    transition: .4s cubic-bezier(0.68, -0.55, 0.27, 1.55);
    border-radius: 50%;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.toggle-switch input:checked + .toggle-slider {
    background-color: var(--primary-color);
}

.toggle-switch input:focus + .toggle-slider {
    box-shadow: 0 0 1px var(--primary-color);
}

.toggle-switch input:checked + .toggle-slider:before {
    transform: translateX(20px);
}

.option-label {
    font-size: 1rem;
    color: var(--neutral-700);
    font-weight: 500;
    margin-bottom: 0;
}

.device-selector {
    padding: 0.5rem 1rem;
    border: 1px solid var(--neutral-300);
    border-radius: 0.375rem;
    background-color: white;
    color: var(--neutral-700);
    font-size: 1rem;
    transition: all 0.2s ease;
    font-weight: 500;
}

.device-selector:focus {
    border-color: var(--primary-color);
    outline: none;
    box-shadow: 0 0 0 2px rgba(79, 70, 229, 0.2);
}

/* Test limit indicator */
.test-limit {
    text-align: center;
    margin-top: 0.75rem;
    padding-top: 0.5rem;
    border-top: 1px solid var(--neutral-200);
    font-size: 0.875rem;
    color: var(--neutral-500);
}

.test-dots {
    display: flex;
    gap: 0.3rem;
    justify-content: center;
    margin-top: 0.5rem;
}

.test-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background-color: var(--neutral-300);
    transition: background-color 0.3s ease, transform 0.3s ease;
}

.test-dot.active {
    background-color: var(--primary-color);
}

/* Loading container - Fixed step transition */
.loading-container {
    display: none;
    padding: 3rem;
    text-align: center;
    background-color: white;
    border-radius: var(--card-radius);
    box-shadow: var(--card-shadow);
    margin: 2rem auto;
    max-width: 600px;
}

.loading-spinner {
    width: 70px;
    height: 70px;
    border: 3px solid rgba(79, 70, 229, 0.2);
    border-radius: 50%;
    border-top-color: var(--primary-color);
    animation: spin 1s linear infinite;
    margin: 0 auto 1.5rem;
    position: relative;
}

.loading-spinner::before,
.loading-spinner::after {
    content: '';
    position: absolute;
    top: -3px;
    left: -3px;
    right: -3px;
    bottom: -3px;
    border: 3px solid transparent;
    border-radius: 50%;
    animation: pulse 2s ease-in-out infinite;
}

.loading-spinner::after {
    animation-delay: 1s;
}

@keyframes spin {
    to {
        transform: rotate(360deg);
    }
}

@keyframes pulse {
    0% {
        transform: scale(1);
        opacity: 0.5;
    }
    50% {
        transform: scale(1.15);
        opacity: 0;
    }
    100% {
        transform: scale(1);
        opacity: 0;
    }
}

.loading-text {
    font-weight: 600;
    font-size: 1.5rem;
    color: var(--neutral-700);
    margin-bottom: 1.5rem;
}

/* Loading steps - Fixed transition */
.test-steps {
    display: flex;
    flex-direction: column;
    margin: 1.5rem 0;
    padding: 0 0.5rem;
}

.test-step {
    display: flex;
    align-items: center;
    margin-bottom: 1.25rem;
    opacity: 0.5;
    transition: opacity 0.3s ease;
}

.test-step.active {
    opacity: 1;
}

.test-step.completed .step-icon {
    background-color: var(--success-color);
}

.test-step.active .step-icon {
    background-color: var(--primary-color);
}

.test-step:not(.completed):not(.active) .step-icon {
    background-color: var(--neutral-400);
}

.step-icon {
    width: 38px;
    height: 38px;
    min-width: 38px;
    border-radius: 50%;
    margin-right: 1rem;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 18px;
    font-weight: 600;
    transition: background-color 0.3s ease;
}

.test-step.completed .step-icon i {
    font-size: 16px;
}

.step-info {
    font-size: 1rem;
    color: var(--neutral-700);
    font-weight: 500;
}

/* Fix for step transition smoothness */
.loading-steps-container {
    position: relative;
    min-height: 240px; /* Adjust based on your step heights */
    overflow: hidden;
}

/* Results container */
.results-container {
    display: none;
}

/* Section styling */
.section {
    margin-bottom: var(--section-gap);
    animation: fadeInUp 0.5s ease;
}

.section-title {
    font-size: 1.75rem;
    font-weight: 700;
    margin-bottom: 1.5rem;
    color: var(--neutral-800);
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.section-title i {
    color: var(--primary-color);
}

.section-subtitle {
    font-size: 1.25rem;
    font-weight: 600;
    margin-bottom: 1rem;
    color: var(--neutral-700);
}

/* Overview section */
.website-overview {
    background-color: white;
    padding: 1.5rem;
    border-radius: var(--card-radius);
    box-shadow: var(--card-shadow);
    margin-bottom: 2rem;
}

.website-info {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
}

.website-url {
    flex: 2;
    min-width: 280px;
}

.website-url-title {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 0.5rem;
    font-weight: 600;
    color: var(--neutral-600);
}

.website-url-value {
    font-size: 1.5rem;
    font-weight: 700;
    word-break: break-all;
}

.website-url-meta {
    margin-top: 0.5rem;
    font-size: 0.875rem;
    color: var(--neutral-500);
}

.user-location {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    background-color: var(--neutral-50);
    border-radius: 0.75rem;
    flex: 1;
    min-width: 200px;
}

.location-flag {
    width: 32px;
    height: 20px;
    object-fit: cover;
    border-radius: 2px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.location-info {
    font-size: 0.9rem;
}

.location-name {
    font-weight: 600;
    margin-bottom: 0.25rem;
}

.location-ip {
    color: var(--neutral-500);
    font-size: 0.875rem;
}

/* Key metrics section */
.metrics-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.metric-card {
    background-color: white;
    border-radius: var(--card-radius);
    box-shadow: var(--card-shadow);
    padding: 1.5rem;
    transition: var(--transition-normal);
    position: relative;
    overflow: hidden;
}

.metric-card:hover {
    transform: translateY(-5px);
    box-shadow: var(--card-shadow-hover);
}

.metric-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 1rem;
}

.metric-title {
    font-weight: 600;
    font-size: 1.1rem;
    color: var(--neutral-700);
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.metric-icon {
    color: var(--primary-color);
    font-size: 1.25rem;
    transition: transform 0.3s ease;
}

.metric-card:hover .metric-icon {
    transform: scale(1.2);
}

.metric-source-badge {
    font-size: 0.75rem;
    padding: 0.2rem 0.5rem;
    border-radius: 12px;
    background-color: rgba(79, 70, 229, 0.1);
    color: var(--primary-color);
}

.metric-source-badge.combined {
    background-color: rgba(59, 130, 246, 0.1);
    color: var(--info-color);
}

.metric-source-badge.checkhost {
    background-color: rgba(16, 185, 129, 0.1);
    color: var(--success-color);
}

.metric-value {
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
    color: var(--neutral-800);
}

.metric-label {
    font-size: 0.95rem;
    color: var(--neutral-500);
    margin-bottom: 1.25rem;
}

.metric-status {
    display: inline-block;
    padding: 0.3rem 0.75rem;
    border-radius: 0.25rem;
    font-size: 0.85rem;
    font-weight: 600;
}

.status-good {
    background-color: rgba(16, 185, 129, 0.1);
    color: var(--success-color);
}

.status-average {
    background-color: rgba(245, 158, 11, 0.1);
    color: var(--warning-color);
}

.status-poor {
    background-color: rgba(239, 68, 68, 0.1);
    color: var(--danger-color);
}

/* Chart section */
.chart-section {
    background-color: white;
    border-radius: var(--card-radius);
    box-shadow: var(--card-shadow);
    padding: 1.5rem;
    margin-bottom: 2rem;
}

.chart-container {
    width: 100%;
    height: 400px;
    position: relative;
}

/* Regions section */
.regions-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.region-card {
    background-color: white;
    border-radius: var(--card-radius);
    box-shadow: var(--card-shadow);
    padding: 1.5rem;
    transition: var(--transition-normal);
}

.region-card:hover {
    transform: translateY(-5px);
    box-shadow: var(--card-shadow-hover);
}

.region-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
    padding-bottom: 0.75rem;
    border-bottom: 1px solid var(--neutral-200);
}

.region-title {
    font-weight: 600;
    font-size: 1.1rem;
    color: var(--neutral-700);
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.region-icon {
    color: var(--primary-color);
}

.region-status {
    display: flex;
    flex-direction: column;
    gap: 1.25rem;
}

.region-stat {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.stat-label {
    font-size: 0.95rem;
    color: var(--neutral-600);
}

.stat-value {
    font-size: 1.1rem;
    font-weight: 600;
    color: var(--neutral-800);
}

.data-source {
    font-size: 0.8rem;
    color: var(--neutral-500);
    margin-top: 0.75rem;
    font-style: italic;
}

.data-source-info {
    font-size: 0.8rem;
    padding: 0.2rem 0.6rem;
    border-radius: 12px;
    background-color: rgba(79, 70, 229, 0.1);
    color: var(--primary-color);
    margin-left: 0.75rem;
    vertical-align: middle;
    font-weight: normal;
}

/* PageSpeed section */
.pagespeed-section {
    margin-bottom: var(--section-gap);
    transition: opacity 0.3s ease, transform 0.3s ease;
}

.pagespeed-section[style="display: none"] {
    opacity: 0;
    transform: translateY(10px);
    pointer-events: none;
    height: 0;
    margin: 0;
}

.pagespeed-section[style="display: block"] {
    opacity: 1;
    transform: translateY(0);
    pointer-events: auto;
}

.pagespeed-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 1.5rem;
}

.score-card {
    background-color: white;
    border-radius: var(--card-radius);
    box-shadow: var(--card-shadow);
    padding: 1.5rem;
    text-align: center;
    transition: var(--transition-normal);
}

.score-card:hover {
    transform: translateY(-5px);
    box-shadow: var(--card-shadow-hover);
}

.score-title {
    font-weight: 600;
    font-size: 1.1rem;
    color: var(--neutral-700);
    margin-bottom: 1rem;
}

.score-circle {
    width: 120px;
    height: 120px;
    position: relative;
    margin: 0 auto 1rem;
}

.score-circle svg {
    transform: rotate(-90deg);
}

.circle-bg {
    fill: none;
    stroke: var(--neutral-200);
    stroke-width: 4;
}

.circle-progress {
    fill: none;
    stroke-width: 4;
    stroke-linecap: round;
    transition: stroke-dashoffset 1s ease;
}

.progress-good {
    stroke: var(--success-color);
}

.progress-average {
    stroke: var(--warning-color);
}

.progress-poor {
    stroke: var(--danger-color);
}

.score-value {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    font-size: 2rem;
    font-weight: 700;
}

.score-label {
    font-size: 0.95rem;
    color: var(--neutral-500);
}

/* Web vitals section */
.web-vitals-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.vital-card {
    background-color: white;
    border-radius: var(--card-radius);
    box-shadow: var(--card-shadow);
    padding: 1.5rem;
    transition: var(--transition-normal);
}

.vital-card:hover {
    transform: translateY(-5px);
    box-shadow: var(--card-shadow-hover);
}

.vital-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 1rem;
}

.vital-title {
    font-weight: 600;
    font-size: 1.1rem;
    color: var(--neutral-700);
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.vital-value {
    font-size: 2rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
    color: var(--neutral-800);
}

.vital-description {
    font-size: 0.9rem;
    color: var(--neutral-500);
    margin-bottom: 1.25rem;
    line-height: 1.5;
}

/* Server locations section */
.location-section {
    margin-bottom: var(--section-gap);
}

.location-table-wrapper {
    background-color: white;
    border-radius: var(--card-radius);
    box-shadow: var(--card-shadow);
    padding: 1.5rem;
    overflow: hidden;
}

.data-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
}

.data-table th {
    text-align: left;
    padding: 1rem;
    background-color: var(--neutral-50);
    color: var(--neutral-700);
    font-weight: 600;
    position: sticky;
    top: 0;
    z-index: 10;
    border-bottom: 1px solid var(--neutral-200);
}

.data-table td {
    padding: 1rem;
    border-bottom: 1px solid var(--neutral-200);
    color: var(--neutral-700);
    transition: background-color 0.3s ease;
}

.data-table tr:last-child td {
    border-bottom: none;
}

/* Fix for green background issue */
.data-table tr.status-online td,
.data-table tr.result-row.status-online td {
    background-color: transparent !important;
}

.data-table tr.status-online:hover td,
.data-table tr.result-row.status-online:hover td {
    background-color: rgba(245, 247, 250, 0.5) !important;
}

.node-location {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.flag-icon {
    width: 22px;
    height: 14px;
    object-fit: cover;
    border-radius: 2px;
    box-shadow: 0 1px 2px rgba(0,0,0,0.1);
}

.source-badge {
    font-size: 0.75rem;
    padding: 0.2rem 0.5rem;
    border-radius: 1rem;
}

.source-server {
    background-color: rgba(79, 70, 229, 0.1);
    color: var(--primary-color);
}

.source-checkhost {
    background-color: rgba(16, 185, 129, 0.1);
    color: var(--success-color);
}

.data-source-controls {
    display: flex;
    gap: 0.5rem;
    margin-left: auto;
}

.source-filter {
    font-size: 0.8rem;
    padding: 0.3rem 0.75rem;
    border-radius: 0.3rem;
    background-color: var(--neutral-100);
    border: 1px solid var(--neutral-200);
    color: var(--neutral-600);
    cursor: pointer;
    transition: var(--transition-fast);
}

.source-filter:hover {
    background-color: var(--neutral-200);
}

.source-filter.active {
    background-color: var(--primary-color);
    color: white;
    border-color: var(--primary-color);
}

.status-cell {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.status-indicator {
    width: 10px;
    height: 10px;
    border-radius: 50%;
}

.status-indicator.status-online {
    background-color: var(--success-color);
    box-shadow: 0 0 5px var(--success-color);
}

.status-indicator.status-slow {
    background-color: var(--warning-color);
    box-shadow: 0 0 5px var(--warning-color);
}

.status-indicator.status-offline {
    background-color: var(--danger-color);
    box-shadow: 0 0 5px var(--danger-color);
}

/* Improved status badges */
.metric-status.status-good {
    color: white;
    background-color: var(--success-color);
}

.metric-status.status-average {
    color: white;
    background-color: var(--warning-color);
}

.metric-status.status-poor {
    color: white;
    background-color: var(--danger-color);
}

/* Opportunities section */
.opportunities-section {
    margin-bottom: var(--section-gap);
}

.opportunity {
    background-color: white;
    border-radius: 0.75rem;
    box-shadow: var(--section-shadow);
    padding: 1.25rem;
    margin-bottom: 1rem;
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    transition: var(--transition-normal);
}

.opportunity:hover {
    transform: translateY(-3px);
    box-shadow: var(--card-shadow);
}

.opportunity-icon {
    width: 40px;
    height: 40px;
    min-width: 40px;
    background-color: rgba(79, 70, 229, 0.1);
    color: var(--primary-color);
    border-radius: 0.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
}

.opportunity-content {
    flex: 1;
}

.opportunity-title {
    font-weight: 600;
    font-size: 1.05rem;
    color: var(--neutral-800);
    margin-bottom: 0.5rem;
}

.opportunity-description {
    font-size: 0.95rem;
    color: var(--neutral-600);
    line-height: 1.5;
}

.opportunity-value {
    font-weight: 600;
    color: var(--primary-color);
    white-space: nowrap;
    padding-left: 1rem;
}

/* Global map section */
.map-section {
    margin-bottom: var(--section-gap);
}

.map-container {
    background-color: white;
    border-radius: var(--card-radius);
    box-shadow: var(--card-shadow);
    padding: 1.5rem;
    overflow: hidden;
}

.map-summary {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.summary-card {
    background-color: var(--neutral-50);
    border-radius: 0.75rem;
    padding: 1.25rem;
    text-align: center;
}

.summary-title {
    font-size: 1rem;
    color: var(--neutral-600);
    margin-bottom: 0.5rem;
}

.summary-value {
    font-size: 2rem;
    font-weight: 700;
    color: var(--neutral-800);
}

.summary-desc {
    font-size: 0.85rem;
    color: var(--neutral-500);
    margin-top: 0.5rem;
}

.global-map {
    height: 400px;
    background-color: var(--neutral-50);
    border-radius: 0.75rem;
    position: relative;
}

/* CheckHost badge */
.checkhost-badge {
    display: inline-flex;
    align-items: center;
    font-size: 0.8rem;
    padding: 0.25rem 0.75rem;
    border-radius: 12px;
    background-color: rgba(16, 185, 129, 0.1);
    color: var(--success-color);
    margin-left: 0.75rem;
    font-weight: normal;
}

.checkhost-badge.disabled {
    background-color: rgba(239, 68, 68, 0.1);
    color: var(--danger-color);
}

.checkhost-badge::before {
    content: '';
    display: inline-block;
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background-color: var(--success-color);
    margin-right: 0.5rem;
}

.checkhost-badge.disabled::before {
    background-color: var(--danger-color);
}

/* Data sources info section */
.data-sources-info {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
    margin-top: 1.5rem;
    padding: 1rem;
    background-color: var(--neutral-50);
    border-radius: 0.5rem;
}

.source-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.9rem;
    color: var(--neutral-600);
}

.source-item i {
    color: var(--primary-color);
}

/* Action buttons */
.action-buttons {
    display: flex;
    justify-content: center;
    flex-wrap: wrap;
    gap: 1rem;
    margin: 3rem 0;
}

.action-button {
    padding: 1rem 2rem;
    border-radius: var(--btn-radius);
    font-weight: 600;
    font-size: 1.1rem;
    cursor: pointer;
    transition: var(--transition-normal);
    display: flex;
    align-items: center;
    gap: 0.5rem;
    min-width: 180px;
    justify-content: center;
}

.btn-primary {
    background-color: var(--primary-color);
    color: white;
    border: none;
}

.btn-primary:hover {
    background-color: var(--primary-dark);
    transform: translateY(-3px);
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

.btn-primary:active {
    transform: translateY(1px);
}

.btn-outline {
    background-color: transparent;
    color: var(--primary-color);
    border: 2px solid var(--primary-color);
}

.btn-outline:hover {
    background-color: rgba(79, 70, 229, 0.05);
    transform: translateY(-3px);
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
}

.btn-outline:active {
    transform: translateY(1px);
}

/* Footer note */
.footer-note {
    text-align: center;
    padding: 2rem 0;
    color: var(--neutral-500);
    font-size: 0.95rem;
    border-top: 1px solid var(--neutral-200);
}

/* Animation utilities */
.fade-in {
    animation: fadeInUp 0.6s forwards;
    opacity: 0;
    transform: translateY(20px);
}

.delay-100 { animation-delay: 0.1s; }
.delay-200 { animation-delay: 0.2s; }
.delay-300 { animation-delay: 0.3s; }
.delay-400 { animation-delay: 0.4s; }
.delay-500 { animation-delay: 0.5s; }

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Loading error message */
.loading-error {
    background-color: #fff5f5;
    color: var(--danger-color);
    padding: 1rem;
    border-radius: 0.5rem;
    margin-top: 1rem;
    font-size: 0.9rem;
    display: none;
}

.loading-error.active {
    display: block;
    animation: fadeIn 0.3s forwards;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

/* Pulse animation for step transitions */
@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); }
}

.pulse {
    animation: pulse 0.5s ease-in-out;
}

/* Mobile responsive improvements */
@media (max-width: 991px) {
    .hero-title {
        font-size: 2.2rem;
    }
    
    .hero-description {
        font-size: 1rem;
    }
    
    .option-label {
        font-size: 0.95rem;
    }
}

@media (max-width: 767px) {
    .input-section {
        margin-top: -2rem;
    }
    
    .url-input-container {
        padding: 1.5rem 1rem;
    }
    
    .url-input-form {
        flex-direction: column;
    }
    
    .url-input {
        padding: 0.8rem 0.8rem 0.8rem 2.5rem;
        font-size: 1rem;
    }
    
    .url-submit-btn {
        width: 100%;
        padding: 0.8rem 1.5rem;
        font-size: 1rem;
    }
    
    /* Test options - MODIFIED for mobile */
    .test-options {
        flex-direction: column;
        align-items: flex-start;
        padding: 0.8rem 0.75rem;
        gap: 0.5rem;
    }
    
    .option-item {
        width: 100%;
        justify-content: space-between;
        min-height: 36px; /* Reduced height */
        margin-bottom: 0.35rem !important;
    }
    
    /* Smaller option labels */
    .option-label {
        font-size: 0.85rem; /* Smaller font size */
        margin-right: 0.25rem; /* Less space */
    }
    
    /* Smaller toggle switches */
    .toggle-switch {
        width: 36px; /* Reduced from 44px */
        height: 20px; /* Reduced from 24px */
    }
    
    /* Adjust toggle slider size */
    .toggle-slider:before {
        height: 14px; /* Reduced from 18px */
        width: 14px; /* Reduced from 18px */
    }
    
    /* Adjust the translation distance for the toggle dot */
    .toggle-switch input:checked + .toggle-slider:before {
        transform: translateX(16px); /* Adjusted from 20px */
    }
    
    /* Compact question mark icons */
    .option-help-text {
        font-size: 0.7rem;
        width: 14px;
        height: 14px;
        position: static; /* Keep in normal flow */
        margin-left: 0.15rem;
    }
    
    /* Device selector smaller */
    .device-selector {
        padding: 0.3rem 0.6rem;
        font-size: 0.85rem;
    }
    
    .loading-container {
        padding: 2rem 1rem;
    }
    
    .loading-spinner {
        width: 60px;
        height: 60px;
    }
    
    .loading-text {
        font-size: 1.2rem;
    }
    
    .website-info {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .website-url-value {
        font-size: 1.2rem;
    }
    
    .website-url-meta {
        font-size: 0.8rem;
    }
    
    .user-location {
        padding: 0.75rem;
    }
    
    .metrics-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .metric-card {
        padding: 1.25rem;
    }
    
    .metric-value {
        font-size: 2rem;
    }
    
    .chart-container {
        height: 300px;
    }
    
    .regions-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .region-card {
        padding: 1rem;
    }
    
    .location-table-wrapper {
        padding: 1rem;
        overflow-x: auto;
    }
    
    .data-table th, 
    .data-table td {
        padding: 0.75rem 0.5rem;
        font-size: 0.85rem;
    }

    .node-location {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.25rem;
    }
    
    .data-source-controls {
        margin-left: 0;
        margin-top: 0.5rem;
        width: 100%;
        justify-content: center;
    }
    
    .section-title {
        flex-wrap: wrap;
    }
    
    .data-sources-info {
        flex-direction: column;
    }
    
    .pagespeed-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .score-card {
        padding: 1rem;
    }
    
    .score-circle {
        width: 100px;
        height: 100px;
    }
    
    .score-value {
        font-size: 1.75rem;
    }
    
    .web-vitals-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .vital-card {
        padding: 1rem;
    }
    
    .vital-value {
        font-size: 1.75rem;
    }
    
    .opportunity {
        flex-direction: column;
        padding: 1rem;
    }
    
    .opportunity-value {
        padding-left: 0;
        margin-top: 0.5rem;
    }

    .action-buttons {
        flex-direction: column;
        width: 100%;
    }
    
    .action-button {
        width: 100%;
    }
}
</style>
@endsection
  
@section('content')
<div class="speed-test-container">
    <!-- Hero Section -->
    <div class="hero-section">
        <div class="hero-content">
            <h1 class="hero-title">Website Speed Test</h1>
            <p class="hero-description">Test your website's performance from multiple global locations and get comprehensive insights on loading speed, response time, and user experience.</p>
        </div>
    </div>

    <!-- Input Section -->
    <div class="input-section">
        <div class="url-input-container">
            <form id="speedTestForm" class="url-input-form">
                <div class="url-input-wrapper">
                    <input type="text" id="websiteUrl" class="url-input" placeholder="Enter website domain (e.g., example.com)" required>
                    <div class="url-input-icon">
                        <i class="fas fa-globe"></i>
                    </div>
                </div>
                <button type="submit" class="url-submit-btn" id="runTestBtn">
                    <i class="fas fa-bolt"></i> Run Test
                </button>
            </form>

            <!-- Test options -->
            <div class="test-options">
                <div class="option-item">
                    <label for="testStrategy" class="option-label">Device:</label>
                    <select id="testStrategy" class="device-selector">
                        <option value="mobile" {{ isset($defaultStrategy) && $defaultStrategy == 'mobile' ? 'selected' : '' }}>Mobile</option>
                        <option value="desktop" {{ isset($defaultStrategy) && $defaultStrategy == 'desktop' ? 'selected' : '' }}>Desktop</option>
                    </select>
                </div>
                
                @if(\App\Models\Setting::get('enable_pagespeed', '0') === '1')
                <div class="option-item">
                    <label class="option-label">Include PageSpeed Insights:</label>
                    <label class="toggle-switch">
                        <input type="checkbox" id="enablePageSpeed" checked>
                        <span class="toggle-slider"></span>
                    </label>
                    <span class="option-help-text" title="Provides detailed performance metrics and optimization suggestions">
                        <i class="fas fa-question-circle"></i>
                    </span>
                </div>
                @endif
                
                <div class="option-item">
                    <label class="option-label">CheckHost Integration:</label>
                    <label class="toggle-switch">
                        <input type="checkbox" id="enableGlobalTest" checked>
                        <span class="toggle-slider"></span>
                    </label>
                    <span class="option-help-text" title="Tests your website from multiple CheckHost nodes globally">
                        <i class="fas fa-question-circle"></i>
                    </span>
                </div>
            </div>

            <div class="test-limit">
                <span>Tests remaining: <span id="testsCounter">3</span>/3</span>
                <div class="test-dots">
                    <div class="test-dot active" data-test="1"></div>
                    <div class="test-dot active" data-test="2"></div>
                    <div class="test-dot active" data-test="3"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Loading Container -->
    <div id="loadingContainer" class="loading-container">
        <div class="loading-spinner"></div>
        <div class="loading-text">Analyzing website performance...</div>
        
        <!-- Test steps list with fixed container for smooth transitions -->
        <div class="loading-steps-container">
            <div class="test-steps" id="testSteps">
                <div class="test-step" data-step="1">
                    <div class="step-icon">1</div>
                    <div class="step-info">Initializing tests...</div>
                </div>
                <div class="test-step" data-step="2">
                    <div class="step-icon">2</div>
                    <div class="step-info">Testing from global locations...</div>
                </div>
                <div class="test-step pagespeed-step" data-step="3" style="display: none;">
                    <div class="step-icon">3</div>
                    <div class="step-info">Running PageSpeed analysis...</div>
                </div>
                <div class="test-step" data-step="4">
                    <div class="step-icon">4</div>
                    <div class="step-info">Processing results...</div>
                </div>
            </div>
        </div>
        
        <!-- Loading error message -->
        <div id="loadingError" class="loading-error">
            <i class="fas fa-exclamation-circle"></i> An error occurred during testing. Please try again.
        </div>
        
        <!-- Cancel button -->
        <button id="cancelTestBtn" class="cancel-test-btn">
            <i class="fas fa-times"></i> Cancel Test
        </button>
    </div>

    <!-- Results Container - All in one page -->
    <div id="resultsContainer" class="results-container">
        <!-- Website Overview Section -->
        <div class="website-overview">
            <div id="websiteInfoContainer" class="website-info">
                <!-- Will be filled by JavaScript -->
                <div class="website-url">
                    <div class="website-url-title">
                        <i class="fas fa-globe text-primary"></i>
                        <span>Website</span>
                    </div>
                    <div class="website-url-value">example.com</div>
                    <div class="website-url-meta">Test run on April 4, 2025</div>
                </div>
                
                <div class="user-location">
                    <img src="/build/images/flags/us.png" alt="United States" class="location-flag">
                    <div class="location-info">
                        <div class="location-name">New York, United States</div>
                        <div class="location-ip">IP: 192.168.1.1</div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Key Metrics Section -->
        <div class="section">
            <h2 class="section-title">
                <i class="fas fa-tachometer-alt"></i> Key Performance Metrics
            </h2>
            
            <div class="metrics-grid">
                <!-- Response Time -->
                <div class="metric-card fade-in delay-100">
                    <div class="metric-header">
                        <div class="metric-title">
                            <i class="fas fa-tachometer-alt metric-icon"></i> Response Time
                        </div>
                        <div class="metric-badge metric-source-badge" id="responseTimeBadge">Server</div>
                    </div>
                    <div class="metric-value" id="avgResponseTime">-</div>
                    <div class="metric-label">Average server response time</div>
                    <div class="metric-status status-good" id="responseTimeStatus">-</div>
                </div>
                
                <!-- Availability -->
                <div class="metric-card fade-in delay-200">
                    <div class="metric-header">
                        <div class="metric-title">
                            <i class="fas fa-signal metric-icon"></i> Availability
                        </div>
                        <div class="metric-badge metric-source-badge" id="availabilityBadge">Server</div>
                    </div>
                    <div class="metric-value" id="availabilityScore">-</div>
                    <div class="metric-label">Global availability percentage</div>
                    <div class="metric-status status-good" id="availabilityStatus">-</div>
                </div>
                
                <!-- Download Speed -->
                <div class="metric-card fade-in delay-300">
                    <div class="metric-header">
                        <div class="metric-title">
                            <i class="fas fa-download metric-icon"></i> Download Speed
                        </div>
                        <div class="metric-badge metric-source-badge">Server</div>
                    </div>
                    <div class="metric-value" id="downloadSpeed">-</div>
                    <div class="metric-label">Average download speed</div>
                    <div class="metric-status status-good" id="downloadStatus">-</div>
                </div>
                
                <!-- Upload Speed -->
                <div class="metric-card fade-in delay-400">
                    <div class="metric-header">
                        <div class="metric-title">
                            <i class="fas fa-upload metric-icon"></i> Upload Speed
                        </div>
                        <div class="metric-badge metric-source-badge">Server</div>
                    </div>
                    <div class="metric-value" id="uploadSpeed">-</div>
                    <div class="metric-label">Average upload speed</div>
                    <div class="metric-status status-good" id="uploadStatus">-</div>
                </div>
            </div>
        </div>
        
        <!-- Response Time Chart Section -->
        <div class="section">
            <h2 class="section-title">
                <i class="fas fa-chart-line"></i> Response Time Analysis
            </h2>
            
            <div class="chart-section">
                <div class="chart-container">
                    <canvas id="responseTimeChart"></canvas>
                </div>
            </div>
        </div>
        
        <!-- Regional Performance Section -->
        <div class="section">
            <h2 class="section-title">
                <i class="fas fa-globe-americas"></i> Regional Performance
                <span class="data-source-info" id="regionSourceInfo">Server + CheckHost</span>
            </h2>
            
            <div class="regions-grid">
                <!-- North America -->
                <div class="region-card fade-in delay-100">
                    <div class="region-header">
                        <div class="region-title">
                            <i class="fas fa-map-marker-alt region-icon"></i> North America
                        </div>
                        <div id="namericaStatus">
                            <i class="fas fa-circle-notch fa-spin text-primary"></i>
                        </div>
                    </div>
                    <div class="region-status">
                        <div class="region-stat">
                            <span class="stat-label">Response Time:</span>
                            <span class="stat-value" id="namericaResponseTime">-</span>
                        </div>
                        <div class="region-stat">
                            <span class="stat-label">Availability:</span>
                            <span class="stat-value" id="namericaAvailability">-</span>
                        </div>
                        <div class="region-stat">
                            <span class="stat-label">Test Nodes:</span>
                            <span class="stat-value" id="namericaNodes">-</span>
                        </div>
                        <div class="data-source" id="namericaSource">Source: -</div>
                    </div>
                </div>
                
                <!-- Europe -->
                <div class="region-card fade-in delay-200">
                    <div class="region-header">
                        <div class="region-title">
                            <i class="fas fa-map-marker-alt region-icon"></i> Europe
                        </div>
                        <div id="europeStatus">
                            <i class="fas fa-circle-notch fa-spin text-primary"></i>
                        </div>
                    </div>
                    <div class="region-status">
                        <div class="region-stat">
                            <span class="stat-label">Response Time:</span>
                            <span class="stat-value" id="europeResponseTime">-</span>
                        </div>
                        <div class="region-stat">
                            <span class="stat-label">Availability:</span>
                            <span class="stat-value" id="europeAvailability">-</span>
                        </div>
                        <div class="region-stat">
                            <span class="stat-label">Test Nodes:</span>
                            <span class="stat-value" id="europeNodes">-</span>
                        </div>
                        <div class="data-source" id="europeSource">Source: -</div>
                    </div>
                </div>
                
                <!-- Asia -->
                <div class="region-card fade-in delay-300">
                    <div class="region-header">
                        <div class="region-title">
                            <i class="fas fa-map-marker-alt region-icon"></i> Asia
                        </div>
                        <div id="asiaStatus">
                            <i class="fas fa-circle-notch fa-spin text-primary"></i>
                        </div>
                    </div>
                    <div class="region-status">
                        <div class="region-stat">
                            <span class="stat-label">Response Time:</span>
                            <span class="stat-value" id="asiaResponseTime">-</span>
                        </div>
                        <div class="region-stat">
                            <span class="stat-label">Availability:</span>
                            <span class="stat-value" id="asiaAvailability">-</span>
                        </div>
                        <div class="region-stat">
                            <span class="stat-label">Test Nodes:</span>
                            <span class="stat-value" id="asiaNodes">-</span>
                        </div>
                        <div class="data-source" id="asiaSource">Source: -</div>
                    </div>
                </div>
                
                <!-- Oceania -->
                <div class="region-card fade-in delay-400">
                    <div class="region-header">
                        <div class="region-title">
                            <i class="fas fa-map-marker-alt region-icon"></i> Oceania
                        </div>
                        <div id="oceaniaStatus">
                            <i class="fas fa-circle-notch fa-spin text-primary"></i>
                        </div>
                    </div>
                    <div class="region-status">
                        <div class="region-stat">
                            <span class="stat-label">Response Time:</span>
                            <span class="stat-value" id="oceaniaResponseTime">-</span>
                        </div>
                        <div class="region-stat">
                            <span class="stat-label">Availability:</span>
                            <span class="stat-value" id="oceaniaAvailability">-</span>
                        </div>
                        <div class="region-stat">
                            <span class="stat-label">Test Nodes:</span>
                            <span class="stat-value" id="oceaniaNodes">-</span>
                        </div>
                        <div class="data-source" id="oceaniaSource">Source: -</div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Global Map Section -->
        <div class="section map-section">
            <h2 class="section-title">
                <i class="fas fa-globe"></i> Global Performance Map
                <div class="checkhost-badge" id="checkHostBadge">CheckHost Enabled</div>
            </h2>
            
            <div class="map-container">
                <div class="map-summary">
                    <div class="summary-card">
                        <div class="summary-title">Total Nodes</div>
                        <div class="summary-value" id="totalNodesCount">-</div>
                        <div class="summary-desc">Testing points worldwide</div>
                    </div>
                    
                    <div class="summary-card">
                        <div class="summary-title">Global Response</div>
                        <div class="summary-value" id="globalResponseTime">-</div>
                        <div class="summary-desc">Average across all regions</div>
                    </div>
                    
                    <div class="summary-card">
                        <div class="summary-title">Availability</div>
                        <div class="summary-value" id="globalAvailability">-</div>
                        <div class="summary-desc">Percentage of successful tests</div>
                    </div>
                </div>
                
                <div id="globalTestMapContainer" class="global-map">
                    <!-- Map visualization will be inserted here -->
                </div>
                
                <div class="data-sources-info">
                    <div class="source-item">
                        <i class="fas fa-server"></i> Server Test: Our server tests website availability from multiple global locations
                    </div>
                    <div class="source-item">
                        <i class="fas fa-globe"></i> CheckHost: Independent third-party service that tests your website from global nodes
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Test Locations Details Section -->
        <div class="section location-section">
            <h2 class="section-title">
                <i class="fas fa-server"></i> Test Locations Details
                <div class="data-source-controls">
                    <button class="source-filter active" data-source="all">All Sources</button>
                    <button class="source-filter" data-source="server">Server Only</button>
                    <button class="source-filter" data-source="checkhost">CheckHost Only</button>
                </div>
            </h2>
            
            <div class="location-table-wrapper">
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Location</th>
                                <th>Source</th>
                                <th>Response Time</th>
                                <th>Download/Upload</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="detailedResultsBody">
                            <!-- Will be filled by JavaScript -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <!-- PageSpeed Insights Section -->
        <div class="section pagespeed-section" style="display: none;">
            <h2 class="section-title">
                <i class="fas fa-gauge-high"></i> PageSpeed Insights
            </h2>
            
            <!-- Performance Scores -->
            <h3 class="section-subtitle">Performance Scores</h3>
            <div class="pagespeed-grid">
                <!-- Performance -->
                <div class="score-card fade-in delay-100">
                    <div class="score-title">Performance</div>
                    <div class="score-circle">
                        <svg viewBox="0 0 36 36">
                            <circle class="circle-bg" cx="18" cy="18" r="16"></circle>
                            <circle class="circle-progress performance-circle" cx="18" cy="18" r="16" stroke-dasharray="100" stroke-dashoffset="100"></circle>
                        </svg>
                        <div class="score-value performance-value">-</div>
                    </div>
                    <div class="score-label">Overall performance score</div>
                </div>
                
                <!-- Accessibility -->
                <div class="score-card fade-in delay-200">
                    <div class="score-title">Accessibility</div>
                    <div class="score-circle">
                        <svg viewBox="0 0 36 36">
                            <circle class="circle-bg" cx="18" cy="18" r="16"></circle>
                            <circle class="circle-progress accessibility-circle" cx="18" cy="18" r="16" stroke-dasharray="100" stroke-dashoffset="100"></circle>
                        </svg>
                        <div class="score-value accessibility-value">-</div>
                    </div>
                    <div class="score-label">Accessibility score</div>
                </div>
                
                <!-- Best Practices -->
                <div class="score-card fade-in delay-300">
                    <div class="score-title">Best Practices</div>
                    <div class="score-circle">
                        <svg viewBox="0 0 36 36">
                            <circle class="circle-bg" cx="18" cy="18" r="16"></circle>
                            <circle class="circle-progress best-practices-circle" cx="18" cy="18" r="16" stroke-dasharray="100" stroke-dashoffset="100"></circle>
                        </svg>
                        <div class="score-value best-practices-value">-</div>
                    </div>
                    <div class="score-label">Best practices score</div>
                </div>
                
                <!-- SEO -->
                <div class="score-card fade-in delay-400">
                    <div class="score-title">SEO</div>
                    <div class="score-circle">
                        <svg viewBox="0 0 36 36">
                            <circle class="circle-bg" cx="18" cy="18" r="16"></circle>
                            <circle class="circle-progress seo-circle" cx="18" cy="18" r="16" stroke-dasharray="100" stroke-dashoffset="100"></circle>
                        </svg>
                        <div class="score-value seo-value">-</div>
                    </div>
                    <div class="score-label">Search engine optimization</div>
                </div>
            </div>
            
            <!-- Core Web Vitals -->
            <h3 class="section-subtitle mt-4">Core Web Vitals</h3>
            <div class="web-vitals-grid">
                <!-- LCP -->
                <div class="vital-card fade-in delay-100">
                    <div class="vital-header">
                        <div class="vital-title">
                            <i class="fas fa-image text-primary"></i> LCP
                        </div>
                        <div class="metric-status status-average lcp-status">-</div>
                    </div>
                    <div class="vital-value lcp-value">-</div>
                    <div class="vital-description">
                        Largest Contentful Paint measures loading performance. To provide a good user experience, LCP should occur within 2.5 seconds of page load.
                    </div>
                </div>
                
                <!-- CLS -->
                <div class="vital-card fade-in delay-200">
                    <div class="vital-header">
                        <div class="vital-title">
                            <i class="fas fa-arrows-alt text-primary"></i> CLS
                        </div>
                        <div class="metric-status status-average cls-status">-</div>
                    </div>
                    <div class="vital-value cls-value">-</div>
                    <div class="vital-description">
                        Cumulative Layout Shift measures visual stability. To provide a good user experience, pages should maintain a CLS of less than 0.1.
                    </div>
                </div>
                
                <!-- TBT -->
                <div class="vital-card fade-in delay-300">
                    <div class="vital-header">
                        <div class="vital-title">
                            <i class="fas fa-clock text-primary"></i> TBT
                        </div>
                        <div class="metric-status status-average tbt-status">-</div>
                    </div>
                    <div class="vital-value tbt-value">-</div>
                    <div class="vital-description">
                        Total Blocking Time measures interactivity. To provide a good user experience, sites should strive to have a TBT of less than 200 milliseconds.
                    </div>
                </div>
            </div>
            
            <!-- Improvement Opportunities -->
            <h3 class="section-subtitle mt-4">Improvement Opportunities</h3>
            <div id="opportunitiesList">
                <div class="text-center p-4">
                    <p class="mb-0">Run a test with PageSpeed Insights enabled to see improvement opportunities.</p>
                </div>
            </div>
        </div>
        
        <!-- Action Buttons -->
        <div class="action-buttons">
            <button id="testAgainBtn" class="action-button btn-primary">
                <i class="fas fa-redo"></i> Test Again
            </button>
            <button id="exportResultsBtn" class="action-button btn-outline">
                <i class="fas fa-download"></i> Export Results
            </button>
        </div>
        
        <!-- Footer Note -->
        <div class="footer-note">
            <p>Website Speed Test combines data from multiple sources to provide comprehensive performance insights.</p>
        </div>
    </div>
</div>
@endsection
  
@section('script')
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.21.0/dist/sweetalert2.all.min.js"></script>
<!-- Chart.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>

<!-- JavaScript for the Website Speed Test Tool -->
<script>
/**
 * Website Speed Test Tool - Enhanced Implementation
 */
document.addEventListener('DOMContentLoaded', function() {
    // Get CSRF token from meta tag
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    // Global variables
    const MAX_TESTS = 3;
    let testCount = 0;
    let currentTestResults = null;
    let responseTimeChart = null;
    let isExporting = false;
    let testInProgress = false;
    let globalTestTimeout = null;

    // DOM Elements
    const elements = {
        speedTestForm: document.getElementById('speedTestForm'),
        websiteUrl: document.getElementById('websiteUrl'),
        testStrategy: document.getElementById('testStrategy'),
        enablePageSpeed: document.getElementById('enablePageSpeed'),
        enableGlobalTest: document.getElementById('enableGlobalTest'),
        testsCounter: document.getElementById('testsCounter'),
        testDots: document.querySelectorAll('.test-dot'),
        loadingContainer: document.getElementById('loadingContainer'),
        resultsContainer: document.getElementById('resultsContainer'),
        testAgainBtn: document.getElementById('testAgainBtn'),
        exportResultsBtn: document.getElementById('exportResultsBtn'),
        testSteps: document.querySelectorAll('.test-step'),
        pageSpeedStep: document.querySelector('.test-step.pagespeed-step'),
        websiteInfoContainer: document.getElementById('websiteInfoContainer'),
        cancelTestBtn: document.getElementById('cancelTestBtn'),
        loadingError: document.getElementById('loadingError'),
        runTestBtn: document.getElementById('runTestBtn')
    };

    // Initialize app
    initializeApp();

    /**
     * Initialize all application components
     */
    function initializeApp() {
        initEventListeners();
        updatePageSpeedStepVisibility();
        checkUrlParameters();
        
        // Check PageSpeed toggle to hide/show section initially
        const pageSpeedEnabled = elements.enablePageSpeed ? elements.enablePageSpeed.checked : false;
        const pageSpeedSection = document.querySelector('.pagespeed-section');
        
        if (pageSpeedSection) {
            pageSpeedSection.style.display = pageSpeedEnabled ? 'block' : 'none';
        }
        
        // Listen for PageSpeed toggle changes to update visibility
        if (elements.enablePageSpeed) {
            elements.enablePageSpeed.addEventListener('change', function() {
                const pageSpeedSection = document.querySelector('.pagespeed-section');
                if (pageSpeedSection) {
                    pageSpeedSection.style.display = this.checked ? 'block' : 'none';
                }
                
                // Also update the loading steps visibility
                updatePageSpeedStepVisibility();
            });
        }
    }

    /**
     * Initialize event listeners for user interactions
     */
    function initEventListeners() {
        // Form submission
        elements.speedTestForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (testInProgress) {
                showAlert('Test In Progress', 'Please wait for the current test to complete or cancel it.', 'info');
                return;
            }
            
            if (testCount >= MAX_TESTS) {
                showAlert('Test Limit Reached', 
                          'You have reached the maximum number of tests. Please reload the page to run more tests.', 
                          'warning');
                return;
            }
            
            runSpeedTest();
        });
        
        // PageSpeed toggle change - update step visibility
        if (elements.enablePageSpeed) {
            elements.enablePageSpeed.addEventListener('change', updatePageSpeedStepVisibility);
        }
        
        // Test again button
        elements.testAgainBtn.addEventListener('click', function() {
            if (testInProgress) {
                showAlert('Test In Progress', 'Please wait for the current test to complete or cancel it.', 'info');
                return;
            }
            
            if (testCount >= MAX_TESTS) {
                showAlert('Test Limit Reached', 
                          'You have reached the maximum number of tests. Please reload the page to run more tests.', 
                          'warning');
                return;
            }
            
            runSpeedTest();
        });
        
        // Cancel test button
        elements.cancelTestBtn.addEventListener('click', function() {
            cancelTest();
        });
        
        // Export results button
        elements.exportResultsBtn.addEventListener('click', function() {
            if (isExporting) return;
            
            isExporting = true;
            exportResults();
            
            // Reset flag after a delay
            setTimeout(() => {
                isExporting = false;
            }, 1000);
        });
    }

    /**
     * Cancel the current test
     */
    function cancelTest() {
        if (globalTestTimeout) {
            clearTimeout(globalTestTimeout);
        }
        resetTestState();
        elements.loadingContainer.style.display = 'none';
        elements.loadingError.classList.remove('active');
        elements.runTestBtn.disabled = false;
        showAlert('Test Cancelled', 'The speed test was cancelled.', 'info');
    }

    /**
     * Reset the test state
     */
    function resetTestState() {
        testInProgress = false;
        resetLoadingSteps();
    }

    /**
     * Update PageSpeed step visibility based on checkbox state
     */
    function updatePageSpeedStepVisibility() {
        if (!elements.pageSpeedStep || !elements.enablePageSpeed) return;
        
        // Get current visibility state
        const currentlyVisible = elements.pageSpeedStep.style.display !== 'none';
        const shouldBeVisible = elements.enablePageSpeed.checked;
        
        // Only update if the state needs to change
        if (currentlyVisible !== shouldBeVisible) {
            // Update visibility with proper display mode to maintain layout
            elements.pageSpeedStep.style.display = shouldBeVisible ? 'flex' : 'none';
            
            // Update step numbers properly
            updateStepNumbers();
        }
    }
    
    /**
     * Update step numbers to be consecutive
     */
    function updateStepNumbers() {
        // Get all visible steps
        const visibleSteps = Array.from(elements.testSteps).filter(step => 
            step.style.display !== 'none'
        );
        
        // Update step numbers in a single batch operation
        requestAnimationFrame(() => {
            visibleSteps.forEach((step, index) => {
                const newStepNumber = index + 1;
                step.setAttribute('data-step', newStepNumber);
                const iconEl = step.querySelector('.step-icon');
                if (iconEl && !iconEl.innerHTML.includes('fa-')) {
                    iconEl.textContent = newStepNumber;
                }
            });
        });
    }

    /**
     * Check URL parameters for auto-testing
     */
    function checkUrlParameters() {
        const urlParams = new URLSearchParams(window.location.search);
        const testUrl = urlParams.get('url');
        
        if (testUrl && !testInProgress) {
            elements.websiteUrl.value = testUrl;
            setTimeout(() => {
                runSpeedTest();
            }, 500);
        }
    }

    /**
     * Run speed test with improved error handling
     */
    function runSpeedTest() {
        const url = elements.websiteUrl.value.trim();
        
        // Validate URL
        if (!url) {
            showAlert('Input Error', 'Please enter a website URL to test', 'error');
            return;
        }
        
        // Prevent multiple simultaneous tests
        if (testInProgress) {
            return;
        }
        
        testInProgress = true;
        elements.runTestBtn.disabled = true;
        
        // Hide any previous error messages
        elements.loadingError.classList.remove('active');
        
        // Destroy any existing chart to prevent canvas reuse issues
        if (responseTimeChart) {
            try {
                responseTimeChart.destroy();
                responseTimeChart = null;
            } catch (e) {
                console.error("Error destroying chart:", e);
            }
        }
        
        // Update test count
        updateTestCount();
        
        // Show loading with animation
        elements.loadingContainer.style.display = 'block';
        elements.resultsContainer.style.display = 'none';
        
        // Set global timeout to prevent infinite loading - increased to 3 minutes
        if (globalTestTimeout) {
            clearTimeout(globalTestTimeout);
        }
        globalTestTimeout = setTimeout(() => {
            if (testInProgress) {
                showLoadingError("Test timeout exceeded (3 minutes). Please try again with a smaller website or disable CheckHost integration.");
            }
        }, 180000); // 3-minute timeout
        
        // Update loading text and reset steps
        updateLoadingStatus("Verifying domain connectivity...");
        resetLoadingSteps();
        setLoadingStep(1);
        
        // Pre-check domain first
        fetch('/tools/website-speed-test/pre-check', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({
                url: url
            })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`Server returned ${response.status}: ${response.statusText}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // Domain is reachable, proceed with test
                let testUrl = url;
                if (!testUrl.startsWith('http://') && !testUrl.startsWith('https://')) {
                    testUrl = (data.protocol || 'https') + '://' + url;
                }
                
                // First ensure PageSpeed step visibility is correctly set
                updatePageSpeedStepVisibility();
                
                // Add a slight delay to show animations
                setTimeout(() => {
                    // Update loading status
                    updateLoadingStatus("Running comprehensive speed tests...");
                    setLoadingStep(2);
                    
                    // Run combined test
                    runCombinedTest(testUrl);
                }, 500);
            } else {
                // Domain is not reachable
                showLoadingError(data.message || "Unable to connect to domain");
            }
        })
        .catch(error => {
            console.error('Pre-check error:', error);
            showLoadingError("Could not verify domain connectivity: " + error.message);
        });
    }

    /**
     * Run combined website speed test with improved error handling
     */
    function runCombinedTest(testUrl) {
        // Get test options
        const testStrategy = elements.testStrategy.value;
        const pageSpeedEnabled = elements.enablePageSpeed ? elements.enablePageSpeed.checked : false;
        const checkHostEnabled = elements.enableGlobalTest ? elements.enableGlobalTest.checked : true;
        
        // Reset current results
        currentTestResults = null;
        
        // Display initial waiting message
        updateLoadingStatus("Sending test request to servers...");
        
        // Call the combined test API with cache buster to prevent cached results
        fetch('/tools/website-speed-test/combined', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({
                url: testUrl,
                strategy: testStrategy,
                use_check_host: checkHostEnabled,
                use_all_nodes: false,
                _cache_buster: new Date().getTime() // Add cache buster
            }),
            // Add client-side timeout
            timeout: 160000 // 160 seconds = ~2.7 minutes (slightly less than server timeout)
        })
        .then(response => {
            // Check for HTTP errors
            if (!response.ok) {
                console.error(`Server returned error status: ${response.status}`);
                // Try to get the error message from response
                return response.text().then(text => {
                    try {
                        const jsonError = JSON.parse(text);
                        throw new Error(jsonError.message || `Server returned ${response.status}: ${response.statusText}`);
                    } catch (e) {
                        throw new Error(`Server returned ${response.status}: ${response.statusText}`);
                    }
                });
            }
            return response.json();
        })
        .then(data => {
            if (!data.success) {
                throw new Error(data.message || 'Unknown error');
            }
            
            // Ensure test_results exists to prevent undefined errors
            if (!data.test_results) {
                throw new Error('Missing test results in response');
            }
            
            // Store the test results
            currentTestResults = data.test_results;
            
            try {
                // Process the server side results
                if (data.test_results.server_side) {
                    processServerSideResults(data.test_results.server_side, testUrl);
                } else {
                    console.warn("No server-side results available");
                }
                
                // Process the combined results if CheckHost was used
                if (checkHostEnabled && data.test_results.check_host) {
                    try {
                        processCombinedResults(data.test_results);
                    } catch (combError) {
                        console.error("Error processing combined results:", combError);
                        // Continue without crashing the whole process
                    }
                }
                
                // Continue with PageSpeed test if enabled
                if (pageSpeedEnabled) {
                    // Get the current step number for PageSpeed
                    const pageSpeedStepNumber = getVisibleStepNumber();
                    setLoadingStep(pageSpeedStepNumber);
                    updateLoadingStatus("Running PageSpeed analysis...");
                    
                    runPageSpeedTest(testUrl, testStrategy)
                        .then(() => {
                            finishTest();
                        })
                        .catch(error => {
                            console.error('PageSpeed error:', error);
                            finishTest();
                        });
                } else {
                    // Skip to finish without PageSpeed
                    finishTest();
                }
            } catch (error) {
                console.error("Error processing results:", error);
                showLoadingError("Error processing test results: " + error.message);
            }
        })
        .catch(error => {
            console.error('Combined test error:', error);
            showLoadingError("Error running combined tests: " + error.message);
        });
    }

    /**
     * Get visible step number for PageSpeed
     */
    function getVisibleStepNumber() {
        // First check if PageSpeed step is visible
        if (elements.pageSpeedStep && elements.pageSpeedStep.style.display !== 'none') {
            return parseInt(elements.pageSpeedStep.getAttribute('data-step'));
        }
        
        // Otherwise return the next sequential step number
        const visibleSteps = Array.from(elements.testSteps).filter(step => 
            step.style.display !== 'none'
        );
        
        // Find current active step
        const activeStepIndex = visibleSteps.findIndex(step => 
            step.classList.contains('active')
        );
        
        return activeStepIndex + 2; // Next step after current
    }

    /**
     * Finish test and show results
     */
    function finishTest() {
        // Set the final step
        const finalStep = getFinalStepNumber();
        setLoadingStep(finalStep);
        updateLoadingStatus("Preparing results...");
        
        // Show results after a small delay
        setTimeout(() => {
            clearTimeout(globalTestTimeout);
            testInProgress = false;
            elements.runTestBtn.disabled = false;
            showResults();
        }, 800);
    }
    
    /**
     * Get the final step number
     */
    function getFinalStepNumber() {
        const visibleSteps = Array.from(elements.testSteps).filter(step => 
            step.style.display !== 'none'
        );
        return visibleSteps.length;
    }

    /**
     * Display loading error
     */
    function showLoadingError(message) {
        clearTimeout(globalTestTimeout);
        testInProgress = false;
        elements.runTestBtn.disabled = false;
        elements.loadingError.textContent = message;
        elements.loadingError.classList.add('active');
        console.error("Loading error:", message);
    }

    /**
     * Run PageSpeed Insights test
     */
    function runPageSpeedTest(url, strategy) {
        return fetch('/tools/website-speed-test/pagespeed', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({
                url: url,
                strategy: strategy
            })
        })
        .then(response => {
            if (!response.ok) {
                if (response.status === 400) {
                    console.warn('PageSpeed test unavailable');
                    return { success: false, message: 'PageSpeed Insights not available' };
                }
                
                return response.text().then(text => {
                    console.error("PageSpeed error response:", text);
                    throw new Error(`PageSpeed API error: ${response.status}`);
                });
            }
            
            return response.json();
        })
        .then(data => {
            if (data.success && !data.has_error) {
                // Process PageSpeed results
                try {
                    processPageSpeedResults(data.metrics);
                    
                    // Store PageSpeed results in currentTestResults for export
                    if (currentTestResults) {
                        currentTestResults.pageSpeed = data.metrics;
                    }
                } catch (error) {
                    console.error("Error processing PageSpeed results:", error);
                }
            } else {
                // Handle PageSpeed errors
                console.warn('PageSpeed error:', data.message);
                showPageSpeedError(data.message || 'PageSpeed Insights results not available');
            }
            
            return data;
        });
    }

    /**
     * Process server-side speed test results
     */
    function processServerSideResults(results, url) {
        // Display user location and website info
        displayWebsiteInfo(results.user_location, url);
        
        // Update key metrics
        updateKeyMetrics(results.stats);
        
        // Update region status
        updateRegionStatus(results.stats.region_stats);
        
        // Display detailed results
        displayDetailedResults(results.results);
        
        // Create response time chart
        createResponseTimeChart(results.results);
    }

    /**
     * Process combined results
     */
    function processCombinedResults(results) {
        console.log("Processing combined results:", results);
        
        if (!results) {
            console.error('No results provided to processCombinedResults');
            return;
        }
        
        if (!results.combined_stats) {
            console.error('Missing combined_stats in results', results);
            return;
        }
        
        const stats = results.combined_stats;
        const checkHostEnabled = results.check_host !== null && typeof results.check_host === 'object';
        
        console.log("CheckHost enabled:", checkHostEnabled);
        if (checkHostEnabled) {
            console.log("CheckHost data:", results.check_host);
        }
        
        // Update source badge on global map
        const checkHostBadge = document.getElementById('checkHostBadge');
        if (checkHostBadge) {
            if (checkHostEnabled) {
                checkHostBadge.textContent = 'CheckHost Enabled';
                checkHostBadge.classList.remove('disabled');
            } else {
                checkHostBadge.textContent = 'CheckHost Disabled';
                checkHostBadge.classList.add('disabled');
            }
        }
        
        // Update region source info
        const regionSourceInfo = document.getElementById('regionSourceInfo');
        if (regionSourceInfo) {
            regionSourceInfo.textContent = checkHostEnabled ? 'Server + CheckHost' : 'Server Only';
        }
        
        // Update key metrics with combined data
        if (stats.average_response_time) {
            const avgResponseTimeEl = document.getElementById('avgResponseTime');
            const responseTimeStatusEl = document.getElementById('responseTimeStatus');
            const responseTimeBadge = document.getElementById('responseTimeBadge');
            
            if (avgResponseTimeEl) {
                avgResponseTimeEl.textContent = stats.average_response_time + ' ms';
            }
            
            // Update source badge
            if (responseTimeBadge && checkHostEnabled) {
                responseTimeBadge.textContent = 'Combined';
                responseTimeBadge.classList.add('combined');
            }
            
            // Set status with appropriate class
            if (responseTimeStatusEl) {
                if (stats.average_response_time < 300) {
                    responseTimeStatusEl.className = 'metric-status status-good';
                    responseTimeStatusEl.textContent = 'Good';
                } else if (stats.average_response_time < 800) {
                    responseTimeStatusEl.className = 'metric-status status-average';
                    responseTimeStatusEl.textContent = 'Average';
                } else {
                    responseTimeStatusEl.className = 'metric-status status-poor';
                    responseTimeStatusEl.textContent = 'Slow';
                }
            }
        }
        
        // Update availability
        if (stats.global_availability) {
            const availabilityScoreEl = document.getElementById('availabilityScore');
            const availabilityStatusEl = document.getElementById('availabilityStatus');
            const availabilityBadge = document.getElementById('availabilityBadge');
            
            if (availabilityScoreEl) {
                availabilityScoreEl.textContent = stats.global_availability + '%';
            }
            
            // Update source badge
            if (availabilityBadge && checkHostEnabled) {
                availabilityBadge.textContent = 'Combined';
                availabilityBadge.classList.add('combined');
            }
            
            // Set status with appropriate class
            if (availabilityStatusEl) {
                if (stats.global_availability > 90) {
                    availabilityStatusEl.className = 'metric-status status-good';
                    availabilityStatusEl.textContent = 'Good';
                } else if (stats.global_availability > 70) {
                    availabilityStatusEl.className = 'metric-status status-average';
                    availabilityStatusEl.textContent = 'Average';
                } else {
                    availabilityStatusEl.className = 'metric-status status-poor';
                    availabilityStatusEl.textContent = 'Poor';
                }
            }
        }
        
        // Update region status with combined data
        if (stats.response_times_by_region && stats.availability_by_region) {
            updateRegionsWithCombinedData(stats, checkHostEnabled);
        }
        
        // Update Global Data
        updateGlobalData(results);
        
        // Initialize source filtering
        initializeSourceFiltering(checkHostEnabled);
        
        // Process CheckHost results
        if (checkHostEnabled) {
            console.log("Processing CheckHost results...");
            try {
                addCheckHostResultsToTable(results.check_host);
            } catch (err) {
                console.error("Error processing CheckHost results:", err);
            }
        } else {
            console.log("CheckHost is disabled or not available");
        }
    }
    
    /**
     * Update regions with combined data
     */
    function updateRegionsWithCombinedData(stats, checkHostEnabled) {
        const regions = {
            'North America': {
                id: 'namerica',
                icon: 'fas fa-map-marker-alt'
            },
            'Europe': {
                id: 'europe',
                icon: 'fas fa-map-marker-alt'
            },
            'Asia': {
                id: 'asia',
                icon: 'fas fa-map-marker-alt'
            },
            'Oceania': {
                id: 'oceania',
                icon: 'fas fa-map-marker-alt'
            },
            'South America': {
                id: 'samerica',
                icon: 'fas fa-map-marker-alt'
            },
            'Africa': {
                id: 'africa',
                icon: 'fas fa-map-marker-alt'
            }
        };
        
        // Process each region
        Object.keys(regions).forEach(region => {
            const responseTime = stats.response_times_by_region[region];
            const availability = stats.availability_by_region[region]?.percentage || 0;
            
            if (responseTime || availability) {
                // Pass the stats object to updateRegionDataDisplay
                updateRegionDataDisplay(region, regions[region].id, responseTime, availability, checkHostEnabled, stats);
            }
        });
    }

    /**
     * Update region data display with values
     */
    function updateRegionDataDisplay(regionName, regionId, responseTime, availability, checkHostEnabled, combinedStats = null) {
        const responseTimeEl = document.getElementById(`${regionId}ResponseTime`);
        const availabilityEl = document.getElementById(`${regionId}Availability`);
        const statusEl = document.getElementById(`${regionId}Status`);
        const sourceEl = document.getElementById(`${regionId}Source`);
        const nodesEl = document.getElementById(`${regionId}Nodes`);
        
        // Apply transition effect for smooth updates
        if (responseTimeEl && responseTime !== undefined && responseTime !== null) {
            fadeElement(responseTimeEl, function() {
                responseTimeEl.textContent = `${responseTime} ms`;
            });
        }
        
        if (availabilityEl && availability) {
            fadeElement(availabilityEl, function() {
                availabilityEl.textContent = `${availability}%`;
            });
        }
        
        if (statusEl) {
            fadeElement(statusEl, function() {
                if (availability > 90) {
                    statusEl.innerHTML = '<i class="fas fa-check-circle text-success"></i>';
                } else if (availability > 50) {
                    statusEl.innerHTML = '<i class="fas fa-exclamation-triangle text-warning"></i>';
                } else {
                    statusEl.innerHTML = '<i class="fas fa-times-circle text-danger"></i>';
                }
            });
        }
        
        // Update source info
        if (sourceEl) {
            fadeElement(sourceEl, function() {
                sourceEl.textContent = checkHostEnabled ? 'Source: Server + CheckHost' : 'Source: Server';
            });
        }
        
        // Update nodes count if available
        if (nodesEl && availability && combinedStats && 
            combinedStats.availability_by_region && 
            combinedStats.availability_by_region[regionName]) {
            
            const regionData = combinedStats.availability_by_region[regionName];
            fadeElement(nodesEl, function() {
                nodesEl.textContent = `${regionData.successful || 0}/${regionData.total || 0} online`;
            });
        } else if (nodesEl) {
            // If no detailed data available, show a simpler message
            fadeElement(nodesEl, function() {
                nodesEl.textContent = 'Data available';
            });
        }
    }

    /**
     * Utility function to fade an element, change its content, then fade it back in
     */
    function fadeElement(element, callback) {
        if (!element) return;
        
        element.style.transition = 'opacity 0.2s ease';
        element.style.opacity = '0';
        
        setTimeout(() => {
            callback();
            setTimeout(() => {
                element.style.opacity = '1';
            }, 20);
        }, 200);
    }

    /**
     * Update global data with combined results
     */
    function updateGlobalData(results) {
        const combinedStats = results.combined_stats;
        
        // Update summary metrics
        fadeElement(document.getElementById('totalNodesCount'), function() {
            document.getElementById('totalNodesCount').textContent = combinedStats.total_nodes_tested || '-';
        });
        
        fadeElement(document.getElementById('globalResponseTime'), function() {
            document.getElementById('globalResponseTime').textContent = 
                combinedStats.average_response_time ? `${combinedStats.average_response_time} ms` : '-';
        });
        
        fadeElement(document.getElementById('globalAvailability'), function() {
            document.getElementById('globalAvailability').textContent = 
                combinedStats.global_availability ? `${combinedStats.global_availability}%` : '-';
        });
        
        // Create global map if container exists
        if (document.getElementById('globalTestMapContainer')) {
            createGlobalMap(results);
        }
    }
    
    /**
     * Create a global map visualization with improved region display
     */
    function createGlobalMap(results) {
        const container = document.getElementById('globalTestMapContainer');
        if (!container) return;
        
        // For now, create a simple region-based visualization
        const regionData = results.combined_stats.response_times_by_region || {};
        const availabilityData = results.combined_stats.availability_by_region || {};
        
        // Prepare HTML with responsive design
        let html = `
            <div style="background-color: #f0f4f8; border-radius: 8px; padding: 20px; height: 100%;">
                <div class="row">
                    <div class="col-12 mb-4">
                        <h5 class="text-center mb-3">Global Response Times</h5>
                    </div>
                </div>
                
                <div class="row justify-content-center mb-4">
                    <div class="col-md-4 col-sm-6 text-center mb-3">
                        <h6>North America</h6>
                        <div class="h3 mb-0 text-${getSpeedClass(regionData['North America'])}">
                            ${regionData['North America'] ? regionData['North America'] + ' ms' : '-'}
                        </div>
                        <small class="text-muted">
                            ${availabilityData['North America'] ? availabilityData['North America'].percentage + '% available' : ''}
                        </small>
                    </div>
                    <div class="col-md-4 col-sm-6 text-center mb-3">
                        <h6>Europe</h6>
                        <div class="h3 mb-0 text-${getSpeedClass(regionData['Europe'])}">
                            ${regionData['Europe'] ? regionData['Europe'] + ' ms' : '-'}
                        </div>
                        <small class="text-muted">
                            ${availabilityData['Europe'] ? availabilityData['Europe'].percentage + '% available' : ''}
                        </small>
                    </div>
                    <div class="col-md-4 col-sm-6 text-center mb-3">
                        <h6>Asia</h6>
                        <div class="h3 mb-0 text-${getSpeedClass(regionData['Asia'])}">
                            ${regionData['Asia'] ? regionData['Asia'] + ' ms' : '-'}
                        </div>
                        <small class="text-muted">
                            ${availabilityData['Asia'] ? availabilityData['Asia'].percentage + '% available' : ''}
                        </small>
                    </div>
                </div>
                
                <div class="row justify-content-center">
                    <div class="col-md-4 col-sm-6 text-center mb-3">
                        <h6>South America</h6>
                        <div class="h3 mb-0 text-${getSpeedClass(regionData['South America'])}">
                            ${regionData['South America'] ? regionData['South America'] + ' ms' : '-'}
                        </div>
                        <small class="text-muted">
                            ${availabilityData['South America'] ? availabilityData['South America'].percentage + '% available' : ''}
                        </small>
                    </div>
                    <div class="col-md-4 col-sm-6 text-center mb-3">
                        <h6>Oceania</h6>
                        <div class="h3 mb-0 text-${getSpeedClass(regionData['Oceania'])}">
                            ${regionData['Oceania'] ? regionData['Oceania'] + ' ms' : '-'}
                        </div>
                        <small class="text-muted">
                            ${availabilityData['Oceania'] ? availabilityData['Oceania'].percentage + '% available' : ''}
                        </small>
                    </div>
                    <div class="col-md-4 col-sm-6 text-center mb-3">
                        <h6>Africa</h6>
                        <div class="h3 mb-0 text-${getSpeedClass(regionData['Africa'])}">
                            ${regionData['Africa'] ? regionData['Africa'] + ' ms' : '-'}
                        </div>
                        <small class="text-muted">
                            ${availabilityData['Africa'] ? availabilityData['Africa'].percentage + '% available' : ''}
                        </small>
                    </div>
                </div>
            </div>
        `;
        
        // Update with fade effect
        fadeElement(container, function() {
            container.innerHTML = html;
        });
    }

    /**
     * Get CSS class based on speed
     */
    function getSpeedClass(speed) {
        if (!speed) return 'muted';
        
        if (speed < 300) return 'success';
        if (speed < 800) return 'warning';
        return 'danger';
    }
    
    /**
     * Initialize source filtering for the results table
     */
    function initializeSourceFiltering(checkHostEnabled) {
        const filterButtons = document.querySelectorAll('.source-filter');
        if (!filterButtons.length) return;
        
        // Disable CheckHost filter if not enabled
        if (!checkHostEnabled) {
            const checkHostButton = document.querySelector('.source-filter[data-source="checkhost"]');
            if (checkHostButton) {
                checkHostButton.disabled = true;
                checkHostButton.style.opacity = 0.5;
                checkHostButton.style.cursor = 'not-allowed';
            }
        }
        
        // Add click event listeners
        filterButtons.forEach(button => {
            button.addEventListener('click', function() {
                // Skip if disabled
                if (this.disabled) return;
                
                // Update active state
                filterButtons.forEach(btn => btn.classList.remove('active'));
                this.classList.add('active');
                
                // Get selected source
                const source = this.dataset.source;
                
                // Filter rows
                const rows = document.querySelectorAll('.result-row');
                rows.forEach(row => {
                    if (source === 'all' || row.dataset.source === source) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
        });
    }
    
    /**
     * Add CheckHost results to the results table
     */
    function addCheckHostResultsToTable(checkHostResults) {
        // Check if we have CheckHost results
        if (!checkHostResults || !checkHostResults.http) {
            console.log("No CheckHost results available");
            return;
        }
        
        const detailedResultsBody = document.getElementById('detailedResultsBody');
        if (!detailedResultsBody) {
            console.error("Detailed results body element not found");
            return;
        }
        
        const httpResults = checkHostResults.http;
        const pingResults = checkHostResults.ping || {};
        
        console.log("Processing CheckHost results:", httpResults);
        
        // Process each CheckHost result
        Object.entries(httpResults).forEach(([node, result]) => {
            // Get corresponding ping result for this node if available
            const pingResult = pingResults[node] ? pingResults[node][0] : null;
            
            console.log(`Processing node ${node} with result:`, result);
            
            let isSuccessful = false;
            let responseTime = null;
            let statusMessage = null;
            let statusCode = null;
            
            // Handle various CheckHost response formats based on API documentation
            if (result) {
                // First check if we have a status directly in result
                if (typeof result.status === 'string') {
                    isSuccessful = result.status === 'online' || result.status === 'slow';
                    responseTime = result.response_time_ms;
                }
                // Handle array format [[1, 0.13, "OK", "200", "94.242.206.94"]]
                else if (Array.isArray(result) && result.length > 0 && Array.isArray(result[0])) {
                    if (result[0].length >= 2) {
                        isSuccessful = result[0][0] === 1 || result[0][0] === true;
                        responseTime = result[0][1] ? Math.round(result[0][1] * 1000) : null;
                        statusMessage = result[0].length > 2 ? result[0][2] : null;
                        statusCode = result[0].length > 3 ? result[0][3] : null;
                    }
                } 
                // Alternative format: [1, 0.13, "OK", "200", "94.242.206.94"]
                else if (Array.isArray(result) && result.length >= 2) {
                    isSuccessful = result[0] === 1 || result[0] === true;
                    responseTime = result[1] ? Math.round(result[1] * 1000) : null;
                    statusMessage = result.length > 2 ? result[2] : null;
                    statusCode = result.length > 3 ? result[3] : null;
                }
            }
            
            // Log parsed result
            console.log(`Parsed result for ${node}: success=${isSuccessful}, time=${responseTime}ms, status=${statusMessage}, code=${statusCode}`);
            
            // Determine status based on response time and success
            let status;
            if (!isSuccessful) {
                status = 'offline';
            } else if (responseTime && responseTime < 300) {
                status = 'online';
            } else {
                status = 'slow';
            }
            
            // Extract country code from node (first 2 letters)
            const countryCode = node.substring(0, 2).toUpperCase();
            const region = getRegionFromNode(node);
            
            // Create row element
            const row = document.createElement('tr');
            row.className = 'result-row';
            row.dataset.source = 'checkhost'; // Mark as CheckHost source
            
            if (status === 'online') {
                row.classList.add('status-online');
            } else if (status === 'slow') {
                row.classList.add('status-slow', 'table-warning');
            } else {
                row.classList.add('status-offline', 'table-danger');
            }
            
            // Format response time text
            let responseTimeText;
            if (isSuccessful && responseTime) {
                responseTimeText = `${responseTime} ms${statusCode ? ` (HTTP ${statusCode})` : ''}`;
                if (statusMessage) {
                    responseTimeText += `<br><small class="text-muted">${statusMessage}</small>`;
                }
            } else if (statusMessage) {
                responseTimeText = `<span class="text-danger">${statusMessage}</span>`;
            } else {
                responseTimeText = '<span class="text-danger">No response</span>';
            }
            
            // Add ping info if available
            if (pingResult) {
                if (Array.isArray(pingResult) && pingResult.length >= 2) {
                    if (pingResult[0] === 'OK' || pingResult[0] === true || pingResult[0] === 1) {
                        responseTimeText += `<br><small class="text-muted">Ping: ${Math.round(pingResult[1] * 1000)} ms</small>`;
                    }
                }
            }
            
            // Create badge based on status
            let statusBadge;
            if (status === 'online') {
                statusBadge = '<div class="metric-status status-good" style="white-space:nowrap;"><i class="fas fa-check-circle"></i> <span class="d-none d-md-inline">Online</span></div>';
            } else if (status === 'slow') {
                statusBadge = '<div class="metric-status status-average" style="white-space:nowrap;"><i class="fas fa-exclamation-triangle"></i> <span class="d-none d-md-inline">Slow</span></div>';
            } else {
                statusBadge = '<div class="metric-status status-poor" style="white-space:nowrap;"><i class="fas fa-times-circle"></i> <span class="d-none d-md-inline">Offline</span></div>';
            }
            
            // Create source badge with icon
            const sourceBadge = '<span class="source-badge source-checkhost"><i class="fas fa-globe-americas"></i> CheckHost</span>';
            
            // Get node location text
            const locationText = nodeToLocation(node);
            
            // Create row content with responsive design
            row.innerHTML = `
                <td>
                    <div class="node-location">
                        <img src="/build/images/flags/${countryCode.toLowerCase()}.png" onerror="this.src='/build/images/flags/xx.png'" class="flag-icon" alt="${countryCode}">
                        <span>${locationText}</span>
                    </div>
                </td>
                <td>${sourceBadge}</td>
                <td class="${status === 'online' ? 'text-success' : status === 'slow' ? 'text-warning' : 'text-danger'}">
                    ${responseTimeText}
                </td>
                <td>N/A</td>
                <td style="text-align:center;">${statusBadge}</td>
            `;
            
            // Find the right position to insert this row (within its region)
            addRowToRegionSection(detailedResultsBody, row, region);
        });
    }
    
    /**
     * Helper function to add row to the correct region section
     */
    function addRowToRegionSection(tableBody, row, region) {
        // First check if the region header already exists
        let regionHeaderRow = null;
        let lastRowInRegion = null;
        let rows = Array.from(tableBody.querySelectorAll('tr'));
        
        // Search for the region header
        for (let i = 0; i < rows.length; i++) {
            const currentRow = rows[i];
            
            // Check if this is a region header with matching region name
            if (currentRow.firstElementChild && 
                currentRow.firstElementChild.colSpan === 5 && 
                currentRow.firstElementChild.textContent.trim() === region) {
                
                regionHeaderRow = currentRow;
                lastRowInRegion = regionHeaderRow; // Start with header row
                
                // Find the last row in this region
                for (let j = i + 1; j < rows.length; j++) {
                    // If we hit another region header, stop
                    if (rows[j].firstElementChild && rows[j].firstElementChild.colSpan === 5) {
                        break;
                    }
                    lastRowInRegion = rows[j];
                }
                break;
            }
        }
        
        if (regionHeaderRow && lastRowInRegion) {
            // Region exists, insert after the last row in this region
            lastRowInRegion.after(row);
            console.log(`Added row to existing region: ${region}`);
        } else {
            // Region header doesn't exist, create it
            
            // Determine where to insert the new region (maintain alphabetical order)
            let insertPosition = tableBody.children.length; // Default to the end
            let currentRegionName = '';
            
            // Get all existing region headers
            const regionHeaders = [];
            for (let i = 0; i < rows.length; i++) {
                if (rows[i].firstElementChild && rows[i].firstElementChild.colSpan === 5) {
                    const headerName = rows[i].firstElementChild.textContent.trim();
                    regionHeaders.push({
                        name: headerName,
                        position: i
                    });
                }
            }
            
            // Find where to insert the new region alphabetically
            let insertAfterRow = null;
            for (let i = 0; i < regionHeaders.length; i++) {
                if (region.localeCompare(regionHeaders[i].name) < 0) {
                    // Insert before this region
                    insertPosition = regionHeaders[i].position;
                    insertAfterRow = i === 0 ? null : rows[regionHeaders[i].position - 1];
                    break;
                }
                
                // If we're at the last header and still haven't found a place,
                // insert after this region
                if (i === regionHeaders.length - 1) {
                    // Find the last row of the last region
                    let lastRow = rows[regionHeaders[i].position];
                    for (let j = regionHeaders[i].position + 1; j < rows.length; j++) {
                        if (rows[j].firstElementChild && rows[j].firstElementChild.colSpan === 5) {
                            break;
                        }
                        lastRow = rows[j];
                    }
                    insertAfterRow = lastRow;
                }
            }
            
            // Create the new region header
            const headerRow = document.createElement('tr');
            headerRow.className = 'table-light';
            headerRow.innerHTML = `<td colspan="5" class="fw-bold">${region}</td>`;
            
            // Insert the region header and the data row
            if (insertAfterRow) {
                insertAfterRow.after(headerRow);
                headerRow.after(row);
            } else if (rows.length > 0) {
                // Insert at the beginning
                tableBody.insertBefore(row, rows[0]);
                tableBody.insertBefore(headerRow, row);
            } else {
                // Empty table
                tableBody.appendChild(headerRow);
                tableBody.appendChild(row);
            }
            
            console.log(`Created new region header: ${region}`);
        }
    }

    /**
     * Convert node code to readable location
     */
    function nodeToLocation(node) {
        // Dictionary of known node locations with more descriptive names
        const locations = {
            'us1': 'United States (East)',
            'us2': 'United States (West)',
            'us3': 'United States (Central)',
            'uk1': 'United Kingdom (London)',
            'de1': 'Germany (Frankfurt)',
            'fr1': 'France (Paris)',
            'nl1': 'Netherlands (Amsterdam)',
            'jp1': 'Japan (Tokyo)',
            'sg1': 'Singapore',
            'au1': 'Australia (Sydney)',
            'br1': 'Brazil (São Paulo)',
            'ca1': 'Canada (Toronto)',
            'in1': 'India (Mumbai)',
            'ru1': 'Russia (Moscow)',
            'ru2': 'Russia (St. Petersburg)',
            'ua1': 'Ukraine (Kyiv)',
            'pl1': 'Poland (Warsaw)',
            'it1': 'Italy (Milan)',
            'es1': 'Spain (Madrid)',
            'tr1': 'Turkey (Istanbul)',
            'za1': 'South Africa (Johannesburg)',
            'th1': 'Thailand (Bangkok)',
            'vn1': 'Vietnam (Ho Chi Minh)',
            'hk1': 'Hong Kong',
            'kr1': 'South Korea (Seoul)',
            'mx1': 'Mexico (Mexico City)',
            'ar1': 'Argentina (Buenos Aires)',
            'cl1': 'Chile (Santiago)',
            'ae1': 'UAE (Dubai)'
        };
        
        return locations[node] || node;
    }

    /**
     * Get region from node code
     */
    function getRegionFromNode(node) {
        // Extract country code from node name
        const countryCode = node.substring(0, 2);
        
        // Classify by country code
        const regions = {
            // North America
            'us': 'North America',
            'ca': 'North America',
            'mx': 'North America',
            
            // Europe
            'uk': 'Europe',
            'gb': 'Europe',
            'de': 'Europe',
            'fr': 'Europe',
            'es': 'Europe',
            'it': 'Europe',
            'nl': 'Europe',
            'ru': 'Europe',
            'pl': 'Europe',
            
            // Asia
            'jp': 'Asia',
            'cn': 'Asia',
            'sg': 'Asia',
            'in': 'Asia',
            'kr': 'Asia',
            'hk': 'Asia',
            'th': 'Asia',
            'vn': 'Asia',
            
            // Oceania
            'au': 'Oceania',
            'nz': 'Oceania',
            
            // South America
            'br': 'South America',
            'ar': 'South America',
            'cl': 'South America',
            
            // Africa
            'za': 'Africa'
        };
        
        return regions[countryCode] || 'Other';
    }
    
    /**
     * Display detailed results with source information and improved styling
     */
    function displayDetailedResults(results) {
        const detailedResultsBody = document.getElementById('detailedResultsBody');
        
        if (!detailedResultsBody || !results || !results.length) {
            return;
        }
        
        // Clear previous results
        detailedResultsBody.innerHTML = '';
        
        // Store check host status for later
        window.hasCheckHostResults = false;
        if (currentTestResults && currentTestResults.check_host) {
            window.hasCheckHostResults = true;
        }
        
        // Sort results by region and response time
        results.sort((a, b) => {
            // First by region
            if (a.region !== b.region) {
                return a.region.localeCompare(b.region);
            }
            
            // Then by status (online first, then slow, then offline)
            const statusOrder = { 'online': 0, 'slow': 1, 'offline': 2 };
            if (statusOrder[a.status] !== statusOrder[b.status]) {
                return statusOrder[a.status] - statusOrder[b.status];
            }
            
            // Finally by response time (fastest first)
            if (a.status !== 'offline' && b.status !== 'offline') {
                return a.time - b.time;
            }
            
            return 0;
        });
        
        // Create rows for each result
        let currentRegion = '';
        
        results.forEach(result => {
            // Add region header if new region
            if (result.region !== currentRegion) {
                currentRegion = result.region;
                
                const headerRow = document.createElement('tr');
                headerRow.className = 'table-light';
                headerRow.innerHTML = `
                    <td colspan="5" class="fw-bold">${currentRegion}</td>
                `;
                
                detailedResultsBody.appendChild(headerRow);
            }
            
            // Format row based on status
            const row = document.createElement('tr');
            row.className = 'result-row';
            row.dataset.source = 'server'; // Mark as server source
            
            if (result.status === 'online') {
                row.classList.add('status-online');
            } else if (result.status === 'slow') {
                row.classList.add('status-slow', 'table-warning');
            } else {
                row.classList.add('status-offline', 'table-danger');
            }
            
            // Format speed text
            const responseTime = result.status === 'offline' ? result.error || 'No response' : `${result.time} ms`;
            const downloadSpeed = result.download_speed ? `${result.download_speed} Mbps` : 'N/A';
            const uploadSpeed = result.upload_speed ? `${result.upload_speed} Mbps` : 'N/A';
            const speedInfo = `${downloadSpeed} / ${uploadSpeed}`;
            
            // Create badge based on status
            let statusBadge;
            if (result.status === 'online') {
                statusBadge = '<div class="metric-status status-good" style="white-space:nowrap;"><i class="fas fa-check-circle"></i> <span class="d-none d-md-inline">Online</span></div>';
            } else if (result.status === 'slow') {
                statusBadge = '<div class="metric-status status-average" style="white-space:nowrap;"><i class="fas fa-exclamation-triangle"></i> <span class="d-none d-md-inline">Slow</span></div>';
            } else {
                statusBadge = '<div class="metric-status status-poor" style="white-space:nowrap;"><i class="fas fa-times-circle"></i> <span class="d-none d-md-inline">Offline</span></div>';
            }
            
            // Create source badge
            const sourceBadge = '<span class="source-badge source-server"><i class="fas fa-server"></i> Server</span>';
            
            // Create row content
            row.innerHTML = `
                <td>
                    <div class="node-location">
                        <img src="/build/images/flags/${result.country.toLowerCase()}.png" class="flag-icon" alt="${result.country}">
                        <div>
                            <span>${result.location}</span>
                            ${result.ip ? `<small class="text-muted d-none d-md-block">${result.ip}</small>` : ''}
                        </div>
                    </div>
                </td>
                <td>${sourceBadge}</td>
                <td class="${result.status === 'online' ? 'text-success' : result.status === 'slow' ? 'text-warning' : 'text-danger'}">
                    ${responseTime}
                </td>
                <td>${speedInfo}</td>
                <td style="text-align:center;">${statusBadge}</td>
            `;
            
            detailedResultsBody.appendChild(row);
        });
    }
    
    /**
     * Create response time chart
     */
    function createResponseTimeChart(results) {
        if (!results || !results.length) {
            return;
        }
        
        // Get canvas context
        const canvas = document.getElementById('responseTimeChart');
        if (!canvas) {
            console.error("Canvas element not found");
            return;
        }
        
        const ctx = canvas.getContext('2d');
        
        // Destroy existing chart if any
        if (responseTimeChart) {
            try {
                responseTimeChart.destroy();
            } catch (e) {
                console.error("Error destroying existing chart:", e);
            }
            responseTimeChart = null;
        }
        
        // Clear canvas manually to be sure
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        
        // Filter out offline results
        const onlineResults = results.filter(result => result.status === 'online' || result.status === 'slow');
        
        if (!onlineResults.length) {
            return;
        }
        
        // Sort by response time (fastest first)
        onlineResults.sort((a, b) => a.time - b.time);
        
        // Take only top 10 for readability
        const topResults = onlineResults.slice(0, 10);
        
        // Prepare chart data
        const labels = topResults.map(result => {
            // Format location name for display
            if (result.location.length > 15) {
                return result.location.substring(0, 12) + '...';
            }
            return result.location;
        });
        
        const responseTimes = topResults.map(result => result.time);
        
        // Set background colors based on response time
        const responseTimeColors = responseTimes.map(time => {
            if (time < 300) return 'rgba(16, 185, 129, 0.7)';  // Green
            if (time < 800) return 'rgba(245, 158, 11, 0.7)';  // Yellow
            return 'rgba(239, 68, 68, 0.7)';  // Red
        });
        
        // Create chart with responsive options
        try {
            responseTimeChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Response Time (ms)',
                        data: responseTimes,
                        backgroundColor: responseTimeColors,
                        borderColor: responseTimeColors.map(color => color.replace('0.7', '1')),
                        borderWidth: 1,
                        borderRadius: 8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        title: {
                            display: false
                        },
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const result = topResults[context.dataIndex];
                                    let label = `${context.parsed.y} ms`;
                                    if (result.download_speed) {
                                        label += ` | ${result.download_speed} Mbps`;
                                    }
                                    return label;
                                },
                                afterLabel: function(context) {
                                    const result = topResults[context.dataIndex];
                                    if (result.country) {
                                        return `Location: ${result.country}, ${result.region}`;
                                    }
                                    return '';
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Response Time (ms)',
                                font: {
                                    weight: 'bold'
                                }
                            },
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                maxRotation: 45,
                                minRotation: 45,
                                font: {
                                    size: window.innerWidth < 768 ? 10 : 12
                                }
                            }
                        }
                    },
                    animation: {
                        duration: 1000,
                        easing: 'easeOutQuart'
                    }
                }
            });
        } catch (error) {
            console.error("Error creating chart:", error);
        }
    }
    
    /**
     * Display website information with user location
     */
    function displayWebsiteInfo(locationInfo, url) {
        if (!elements.websiteInfoContainer) return;
        
        try {
            const countryCode = (locationInfo.location?.country_code || locationInfo.location?.countryCode || 'xx').toLowerCase();
            const country = locationInfo.location?.country || 'Unknown';
            const city = locationInfo.location?.city || 'Unknown';
            const ip = locationInfo.ip || 'Unknown';
            
            // Create site info HTML
            elements.websiteInfoContainer.innerHTML = `
                <div class="website-url">
                    <div class="website-url-title">
                        <i class="fas fa-globe text-primary"></i>
                        <span>Website</span>
                    </div>
                    <div class="website-url-value">${url}</div>
                    <div class="website-url-meta">Test run on ${new Date().toLocaleString()}</div>
                </div>
                
                <div class="user-location">
                    <img src="/build/images/flags/${countryCode}.png" onerror="this.src='/build/images/flags/xx.png'" alt="${country}" class="location-flag">
                    <div class="location-info">
                        <div class="location-name">${city !== 'Unknown' ? city + ', ' : ''}${country}</div>
                        <div class="location-ip">IP: ${ip}</div>
                    </div>
                </div>
            `;
        } catch (error) {
            console.error('Error displaying website info:', error);
            elements.websiteInfoContainer.innerHTML = `
                <div class="website-url">
                    <div class="website-url-title">
                        <i class="fas fa-globe text-primary"></i>
                        <span>Website</span>
                    </div>
                    <div class="website-url-value">${url}</div>
                    <div class="website-url-meta">Test run on ${new Date().toLocaleString()}</div>
                </div>
                
                <div class="user-location">
                    <div class="location-info">
                        <div class="location-name">Unknown Location</div>
                    </div>
                </div>
            `;
        }
    }

    /**
     * Update key metrics in the dashboard
     */
    function updateKeyMetrics(stats) {
        // Average response time
        const avgResponseTimeEl = document.getElementById('avgResponseTime');
        const responseTimeStatusEl = document.getElementById('responseTimeStatus');
        
        if (avgResponseTimeEl && stats) {
            avgResponseTimeEl.textContent = stats.average_time + ' ms';
            
            // Set status with appropriate class
            if (responseTimeStatusEl) {
                if (stats.average_time < 300) {
                    responseTimeStatusEl.className = 'metric-status status-good';
                    responseTimeStatusEl.textContent = 'Good';
                } else if (stats.average_time < 800) {
                    responseTimeStatusEl.className = 'metric-status status-average';
                    responseTimeStatusEl.textContent = 'Average';
                } else {
                    responseTimeStatusEl.className = 'metric-status status-poor';
                    responseTimeStatusEl.textContent = 'Slow';
                }
            }
        }
        
        // Availability score
        const availabilityScoreEl = document.getElementById('availabilityScore');
        const availabilityStatusEl = document.getElementById('availabilityStatus');
        
        if (availabilityScoreEl && stats) {
            availabilityScoreEl.textContent = stats.success_rate + '%';
            
            // Set status with appropriate class
            if (availabilityStatusEl) {
                if (stats.success_rate > 90) {
                    availabilityStatusEl.className = 'metric-status status-good';
                    availabilityStatusEl.textContent = 'Good';
                } else if (stats.success_rate > 70) {
                    availabilityStatusEl.className = 'metric-status status-average';
                    availabilityStatusEl.textContent = 'Average';
                } else {
                    availabilityStatusEl.className = 'metric-status status-poor';
                    availabilityStatusEl.textContent = 'Poor';
                }
            }
        }
        
        // Download speed
        const downloadSpeedEl = document.getElementById('downloadSpeed');
        const downloadStatusEl = document.getElementById('downloadStatus');
        
        if (downloadSpeedEl && stats && stats.avg_download_speed !== null) {
            downloadSpeedEl.textContent = stats.avg_download_speed + ' Mbps';
            
            // Set status with appropriate class
            if (downloadStatusEl) {
                if (stats.avg_download_speed > 10) {
                    downloadStatusEl.className = 'metric-status status-good';
                    downloadStatusEl.textContent = 'Good';
                } else if (stats.avg_download_speed > 5) {
                    downloadStatusEl.className = 'metric-status status-average';
                    downloadStatusEl.textContent = 'Average';
                } else {
                    downloadStatusEl.className = 'metric-status status-poor';
                    downloadStatusEl.textContent = 'Slow';
                }
            }
        } else if (downloadSpeedEl) {
            downloadSpeedEl.textContent = 'N/A';
            if (downloadStatusEl) {
                downloadStatusEl.className = 'metric-status status-average';
                downloadStatusEl.textContent = 'Unknown';
            }
        }
        
        // Upload speed
        const uploadSpeedEl = document.getElementById('uploadSpeed');
        const uploadStatusEl = document.getElementById('uploadStatus');
        
        if (uploadSpeedEl && stats && stats.avg_upload_speed !== null) {
            uploadSpeedEl.textContent = stats.avg_upload_speed + ' Mbps';
            
            // Set status with appropriate class
            if (uploadStatusEl) {
                if (stats.avg_upload_speed > 5) {
                    uploadStatusEl.className = 'metric-status status-good';
                    uploadStatusEl.textContent = 'Good';
                } else if (stats.avg_upload_speed > 2) {
                    uploadStatusEl.className = 'metric-status status-average';
                    uploadStatusEl.textContent = 'Average';
                } else {
                    uploadStatusEl.className = 'metric-status status-poor';
                    uploadStatusEl.textContent = 'Slow';
                }
            }
        } else if (uploadSpeedEl) {
            uploadSpeedEl.textContent = 'N/A';
            if (uploadStatusEl) {
                uploadStatusEl.className = 'metric-status status-average';
                uploadStatusEl.textContent = 'Unknown';
            }
        }
    }

    /**
     * Update region status in the dashboard
     */
    function updateRegionStatus(regionStats) {
        // Update North America
        updateRegionStatusDisplay('namerica', regionStats['North America']);
        
        // Update Europe
        updateRegionStatusDisplay('europe', regionStats['Europe']);
        
        // Update Asia
        updateRegionStatusDisplay('asia', regionStats['Asia']);
        
        // Update Oceania if available
        if (regionStats['Oceania']) {
            updateRegionStatusDisplay('oceania', regionStats['Oceania']);
        }
    }

    /**
     * Update region status display
     */
    function updateRegionStatusDisplay(regionId, stats) {
        const statusElement = document.getElementById(`${regionId}Status`);
        const responseTimeEl = document.getElementById(`${regionId}ResponseTime`);
        const availabilityEl = document.getElementById(`${regionId}Availability`);
        const nodesEl = document.getElementById(`${regionId}Nodes`);
        
        if (!statusElement || !stats) {
            return;
        }
        
        if (stats.total === 0) {
            statusElement.innerHTML = '<i class="fas fa-minus text-muted"></i>';
            if (nodesEl) nodesEl.textContent = 'No nodes available';
            return;
        }
        
        const availability = Math.round(((stats.online + stats.slow) / stats.total) * 100);
        
        // Set response time with fade effect
        if (responseTimeEl && stats.avgResponseTime) {
            fadeElement(responseTimeEl, function() {
                responseTimeEl.textContent = `${stats.avgResponseTime} ms`;
            });
        }
        
        // Set availability with fade effect
        if (availabilityEl) {
            fadeElement(availabilityEl, function() {
                availabilityEl.textContent = `${availability}%`;
            });
        }
        
        // Set nodes status with fade effect
        if (nodesEl) {
            fadeElement(nodesEl, function() {
                nodesEl.textContent = `${stats.online + stats.slow}/${stats.total} online`;
            });
        }
        
        // Apply status icon with animation
        statusElement.style.transform = 'scale(0)';
        setTimeout(() => {
            if (availability > 90) {
                statusElement.innerHTML = '<i class="fas fa-check-circle text-success"></i>';
            } else if (availability > 50) {
                statusElement.innerHTML = '<i class="fas fa-exclamation-triangle text-warning"></i>';
            } else {
                statusElement.innerHTML = '<i class="fas fa-times-circle text-danger"></i>';
            }
            
            statusElement.style.transform = 'scale(1)';
        }, 200);
    }
    
    /**
     * Process PageSpeed Insights results
     */
    function processPageSpeedResults(metrics) {
        if (!metrics) {
            showPageSpeedError('Could not get PageSpeed Insights results');
            return;
        }
        
        // Update performance score
        updateScoreCircle('performance', metrics.scores.performance);
        
        // Update accessibility score
        updateScoreCircle('accessibility', metrics.scores.accessibility);
        
        // Update best practices score
        const bestPracticesScore = metrics.scores.best_practices || metrics.scores['best-practices'] || metrics.scores.bestPractices;
        updateScoreCircle('best-practices', bestPracticesScore);
        
        // Update SEO score
        updateScoreCircle('seo', metrics.scores.seo);
        
        // Update Core Web Vitals
        updateCoreWebVitals(metrics);
        
        // Update opportunities
        updateOpportunities(metrics.opportunities || []);
    }
    
    /**
     * Update score circle for PageSpeed Insights
     */
    function updateScoreCircle(type, score) {
        // Normalize selector type for CSS
        const selectorType = type.replace('_', '-');
        
        const circle = document.querySelector(`.${selectorType}-circle`);
        const value = document.querySelector(`.${selectorType}-value`);
        
        if (!circle || !value) {
            console.error(`Elements for ${selectorType} not found`);
            return;
        }
        
        // Handle null/undefined score
        if (score === null || score === undefined) {
            value.textContent = "-";
            circle.style.strokeDasharray = 100;
            circle.style.strokeDashoffset = 100;
            circle.classList.remove('progress-good', 'progress-average', 'progress-poor');
            return;
        }
        
        // Round score to whole number for display
        const displayScore = Math.round(score);
        
        // Set the score value with fade effect
        fadeElement(value, function() {
            value.textContent = displayScore;
        });
        
        // Calculate the dashoffset for circle animation
        const circumference = 100;
        const dashoffset = circumference - (displayScore / 100) * circumference;
        
        // Remove existing classes
        circle.classList.remove('progress-good', 'progress-average', 'progress-poor');
        value.classList.remove('text-success', 'text-warning', 'text-danger');
        
        // Add appropriate class based on score
        let scoreClass = '', textClass = '';
        if (displayScore >= 90) {
            scoreClass = 'progress-good';
            textClass = 'text-success';
        } else if (displayScore >= 50) {
            scoreClass = 'progress-average';
            textClass = 'text-warning';
        } else {
            scoreClass = 'progress-poor';
            textClass = 'text-danger';
        }
        
        circle.classList.add(scoreClass);
        value.classList.add(textClass);
        
        // Set the dashoffset with animation
        circle.style.strokeDasharray = circumference;
        circle.style.transition = 'stroke-dashoffset 1.5s ease';
        
        // Force layout reflow to ensure animation works
        void circle.getBoundingClientRect();
        
        setTimeout(() => {
            circle.style.strokeDashoffset = dashoffset;
        }, 100);
    }

    /**
     * Update Core Web Vitals display
     */
    function updateCoreWebVitals(metrics) {
        if (!metrics || !metrics.metrics) {
            return;
        }
        
        // Update LCP (Largest Contentful Paint)
        if (metrics.metrics.largest_contentful_paint) {
            const lcp = metrics.metrics.largest_contentful_paint;
            const lcpValue = document.querySelector('.lcp-value');
            const lcpStatus = document.querySelector('.lcp-status');
            
            if (lcpValue) {
                fadeElement(lcpValue, function() {
                    lcpValue.textContent = lcp.value;
                });
            }
            
            if (lcpStatus) {
                const lcpTime = extractTimeValue(lcp.value);
                
                fadeElement(lcpStatus, function() {
                    if (lcpTime <= 2.5) {
                        lcpStatus.className = 'metric-status status-good';
                        lcpStatus.textContent = 'Good';
                    } else if (lcpTime <= 4.0) {
                        lcpStatus.className = 'metric-status status-average';
                        lcpStatus.textContent = 'Needs Improvement';
                    } else {
                        lcpStatus.className = 'metric-status status-poor';
                        lcpStatus.textContent = 'Poor';
                    }
                });
            }
        }
        
        // Update CLS (Cumulative Layout Shift)
        if (metrics.metrics.cumulative_layout_shift) {
            const cls = metrics.metrics.cumulative_layout_shift;
            const clsValue = document.querySelector('.cls-value');
            const clsStatus = document.querySelector('.cls-status');
            
            if (clsValue) {
                fadeElement(clsValue, function() {
                    clsValue.textContent = cls.value;
                });
            }
            
            if (clsStatus) {
                const clsScore = parseFloat(cls.value);
                
                fadeElement(clsStatus, function() {
                    if (clsScore <= 0.1) {
                        clsStatus.className = 'metric-status status-good';
                        clsStatus.textContent = 'Good';
                    } else if (clsScore <= 0.25) {
                        clsStatus.className = 'metric-status status-average';
                        clsStatus.textContent = 'Needs Improvement';
                    } else {
                        clsStatus.className = 'metric-status status-poor';
                        clsStatus.textContent = 'Poor';
                    }
                });
            }
        }
        
        // Update TBT (Total Blocking Time)
        if (metrics.metrics.total_blocking_time) {
            const tbt = metrics.metrics.total_blocking_time;
            const tbtValue = document.querySelector('.tbt-value');
            const tbtStatus = document.querySelector('.tbt-status');
            
            if (tbtValue) {
                fadeElement(tbtValue, function() {
                    tbtValue.textContent = tbt.value;
                });
            }
            
            if (tbtStatus) {
                const tbtTime = extractTimeValue(tbt.value);
                
                fadeElement(tbtStatus, function() {
                    if (tbtTime <= 200) {
                        tbtStatus.className = 'metric-status status-good';
                        tbtStatus.textContent = 'Good';
                    } else if (tbtTime <= 600) {
                        tbtStatus.className = 'metric-status status-average';
                        tbtStatus.textContent = 'Needs Improvement';
                    } else {
                        tbtStatus.className = 'metric-status status-poor';
                        tbtStatus.textContent = 'Poor';
                    }
                });
            }
        }
    }

    /**
     * Extract time value from string (e.g., "2.3 s" -> 2.3)
     */
    function extractTimeValue(timeString) {
        if (!timeString) return 0;
        
        const match = timeString.match(/(\d+(\.\d+)?)/);
        return match ? parseFloat(match[0]) : 0;
    }

    /**
     * Update opportunities list
     */
    function updateOpportunities(opportunities) {
        const opportunitiesList = document.getElementById('opportunitiesList');
        
        if (!opportunitiesList) {
            return;
        }
        
        // Clear with fade out
        opportunitiesList.style.opacity = '0';
        
        setTimeout(function() {
            if (!opportunities || opportunities.length === 0) {
                opportunitiesList.innerHTML = `
                    <div class="text-center p-4">
                        <i class="fas fa-check-circle text-success fs-4 mb-3"></i>
                        <p class="mb-0">No opportunities found! Your website is already well optimized.</p>
                    </div>
                `;
            } else {
                let html = '';
                
                opportunities.forEach((opportunity, index) => {
                    html += `
                        <div class="opportunity fade-in" style="animation-delay: ${index * 0.1}s">
                            <div class="opportunity-icon">
                                <i class="fas fa-lightbulb"></i>
                            </div>
                            <div class="opportunity-content">
                                <div class="opportunity-title">${opportunity.title}</div>
                                <div class="opportunity-description">${opportunity.description}</div>
                            </div>
                            <div class="opportunity-value">${opportunity.display_value || 'N/A'}</div>
                        </div>
                    `;
                });
                
                opportunitiesList.innerHTML = html;
            }
            
            // Fade back in
            setTimeout(function() {
                opportunitiesList.style.opacity = '1';
            }, 100);
        }, 300);
    }

    /**
     * Show PageSpeed error
     */
    function showPageSpeedError(message) {
        // Update score circles with error state
        document.querySelectorAll('.score-value').forEach(el => {
            el.textContent = '-';
        });
        
        document.querySelectorAll('.circle-progress').forEach(el => {
            el.style.strokeDashoffset = 100;
            el.classList.remove('progress-good', 'progress-average', 'progress-poor');
        });
        
        // Update Core Web Vitals with error state
        document.querySelectorAll('.vital-value').forEach(el => {
            el.textContent = '-';
        });
        
        document.querySelectorAll('.metric-status').forEach(el => {
            if (el.classList.contains('lcp-status') || el.classList.contains('tbt-status') || el.classList.contains('cls-status')) {
                el.innerHTML = 'N/A';
                el.className = 'metric-status status-average';
            }
        });
        
        // Show error message in opportunities section
        const opportunitiesList = document.getElementById('opportunitiesList');
        if (opportunitiesList) {
            opportunitiesList.innerHTML = `
                <div class="text-center p-4">
                    <i class="fas fa-exclamation-triangle text-warning fs-4 mb-3"></i>
                    <p class="mb-0">${message}</p>
                </div>
            `;
        }
    }

    /**
     * Reset loading steps
     */
    function resetLoadingSteps() {
        if (!elements.testSteps) return;
        
        // First update the visibility of PageSpeed step
        updatePageSpeedStepVisibility();
        
        // Then reset all steps in a single operation
        elements.testSteps.forEach(step => {
            step.classList.remove('active', 'completed');
            const icon = step.querySelector('.step-icon');
            if (icon) {
                // Only update if it's not already a number
                if (icon.innerHTML.includes('fa-')) {
                    const stepNum = step.getAttribute('data-step');
                    icon.textContent = stepNum;
                }
            }
        });
    }

    /**
     * Set active loading step
     */
    function setLoadingStep(stepNumber) {
        if (!elements.testSteps) return;
        
        // Get all visible steps
        const visibleSteps = Array.from(elements.testSteps).filter(step => 
            step.style.display !== 'none'
        );
        
        // Prepare state changes first to apply them all at once
        const updates = visibleSteps.map(step => {
            const stepNum = parseInt(step.getAttribute('data-step'));
            const iconEl = step.querySelector('.step-icon');
            
            let newState = {
                element: step,
                iconElement: iconEl,
                addClass: [],
                removeClass: ['active', 'completed'],
                iconContent: null
            };
            
            if (stepNum < stepNumber) {
                // Previous steps are completed
                newState.addClass.push('completed');
                newState.iconContent = '<i class="fas fa-check"></i>';
            } else if (stepNum === stepNumber) {
                // Current step is active
                newState.addClass.push('active');
                newState.iconContent = stepNum.toString();
            } else {
                // Future steps remain as is
                newState.iconContent = stepNum.toString();
            }
            
            return newState;
        });
        
        // Apply all changes together to avoid visual jumps
        requestAnimationFrame(() => {
            updates.forEach(update => {
                // Apply classes
                update.element.classList.remove(...update.removeClass);
                update.element.classList.add(...update.addClass);
                
                // Update icon if needed
                if (update.iconElement && update.iconContent) {
                    update.iconElement.innerHTML = update.iconContent;
                }
            });
        });
    }

    /**
     * Update loading status text with transition effect
     */
    function updateLoadingStatus(message) {
        const loadingText = document.querySelector('.loading-text');
        if (loadingText) {
            fadeElement(loadingText, function() {
                loadingText.textContent = message;
            });
        }
    }

    /**
     * Show results after test completion
     */
    function showResults() {
        elements.loadingContainer.style.display = 'none';
        elements.resultsContainer.style.display = 'block';
        
        // Check if PageSpeed is enabled and hide/show the PageSpeed section accordingly
        const pageSpeedEnabled = elements.enablePageSpeed ? elements.enablePageSpeed.checked : false;
        const pageSpeedSection = document.querySelector('.pagespeed-section');
        
        if (pageSpeedSection) {
            pageSpeedSection.style.display = pageSpeedEnabled ? 'block' : 'none';
        }
        
        // Animate sections in sequence for visual appeal
        const sections = document.querySelectorAll('.section:not(.pagespeed-section), .pagespeed-section[style="display: block"]');
        sections.forEach((section, index) => {
            setTimeout(() => {
                section.classList.add('fade-in');
            }, 100 * index);
        });
        
        // Apply fixes for status cells and other UI elements
        setTimeout(() => {
            fixStatusDisplay();
            enhanceCheckHostBadges();
            enhanceTableResponsiveness();
        }, 500);
        
        // Scroll to top of results
        window.scrollTo({ top: elements.resultsContainer.offsetTop - 20, behavior: 'smooth' });
    }

    /**
     * Fix status display in test locations section
     */
    function fixStatusDisplay() {
        // Apply better styles to status cells
        document.querySelectorAll('.data-table tr.status-online td').forEach(cell => {
            cell.style.backgroundColor = 'transparent';
        });
        
        // Make status badges more visible
        document.querySelectorAll('.metric-status.status-good').forEach(badge => {
            badge.style.backgroundColor = 'var(--success-color)';
            badge.style.color = 'white';
        });
        
        document.querySelectorAll('.metric-status.status-average').forEach(badge => {
            badge.style.backgroundColor = 'var(--warning-color)';
            badge.style.color = 'white';
        });
        
        document.querySelectorAll('.metric-status.status-poor').forEach(badge => {
            badge.style.backgroundColor = 'var(--danger-color)';
            badge.style.color = 'white';
        });
    }

    /**
     * Enhance CheckHost badges
     */
    function enhanceCheckHostBadges() {
        const checkHostBadge = document.getElementById('checkHostBadge');
        if (checkHostBadge) {
            // Add icon if not already present
            if (!checkHostBadge.querySelector('i')) {
                const icon = document.createElement('i');
                icon.className = 'fas fa-globe-americas mr-1';
                icon.style.marginRight = '5px';
                checkHostBadge.prepend(icon);
            }
        }
    }

    /**
     * Enhance table responsiveness for mobile devices
     */
    function enhanceTableResponsiveness() {
        // Check if we're on mobile
        const isMobile = window.innerWidth < 768;
        
        if (isMobile) {
            // Add responsive wrapper to table if not already present
            const tableCont = document.querySelector('.location-table-wrapper');
            if (tableCont) {
                const tableEl = tableCont.querySelector('.data-table');
                if (tableEl && !tableCont.querySelector('.table-responsive')) {
                    const respWrapper = document.createElement('div');
                    respWrapper.className = 'table-responsive';
                    respWrapper.style.overflowX = 'auto';
                    tableCont.insertBefore(respWrapper, tableEl);
                    respWrapper.appendChild(tableEl);
                }
            }
            
            // Make badges more compact
            document.querySelectorAll('.metric-status').forEach(badge => {
                const icon = badge.querySelector('i');
                const text = badge.textContent.trim();
                
                if (icon && badge.parentElement && badge.parentElement.tagName === 'TD') {
                    badge.innerHTML = '';
                    badge.appendChild(icon);
                    badge.title = text;
                }
            });
        }
    }

    /**
     * Update test count with animation
     */
    function updateTestCount() {
        testCount++;
        
        // Update counter with animation
        if (elements.testsCounter) {
            elements.testsCounter.textContent = MAX_TESTS - testCount;
            elements.testsCounter.classList.add('pulse');
            setTimeout(() => {
                elements.testsCounter.classList.remove('pulse');
            }, 500);
        }
        
        // Update dots with animation
        elements.testDots.forEach(dot => {
            const dotCount = parseInt(dot.getAttribute('data-test'));
            if (dotCount <= (MAX_TESTS - testCount)) {
                dot.classList.add('active');
            } else {
                dot.classList.remove('active');
                
                // Add animation to the dot being removed
                dot.classList.add('pulse-out');
                setTimeout(() => {
                    dot.classList.remove('pulse-out');
                }, 500);
            }
        });
        
        // Disable test again button if limit reached with animation
        if (testCount >= MAX_TESTS) {
            elements.testAgainBtn.disabled = true;
            elements.testAgainBtn.classList.add('opacity-50');
            elements.testAgainBtn.classList.add('shake');
            setTimeout(() => {
                elements.testAgainBtn.classList.remove('shake');
            }, 500);
        }
    }

     /**
     * Export results to CSV
     */
    function exportResults() {
        // If no current results, show a message
        if (!currentTestResults) {
            showAlert('No Results', 'No test results available to export', 'warning');
            isExporting = false; // Reset flag
            return;
        }
        
        // Get the basic data
        const url = currentTestResults.url || elements.websiteUrl.value;
        const timestamp = currentTestResults.timestamp || new Date().toISOString();
        
        // Get speed test data
        const serverSideResults = currentTestResults.server_side || {};
        const stats = serverSideResults.stats || {};
        const combinedStats = currentTestResults.combined_stats || {};
        
        // Get response times
        const avgResponseTime = stats.average_time ? `${stats.average_time} ms` : 'N/A';
        const combinedResponseTime = combinedStats.average_response_time ? `${combinedStats.average_response_time} ms` : 'N/A';
        
        const availability = stats.success_rate ? `${stats.success_rate}%` : 'N/A';
        const combinedAvailability = combinedStats.global_availability ? `${combinedStats.global_availability}%` : 'N/A';
        
        const downloadSpeed = stats.avg_download_speed ? `${stats.avg_download_speed} Mbps` : 'N/A';
        const uploadSpeed = stats.avg_upload_speed ? `${stats.avg_upload_speed} Mbps` : 'N/A';
        
        // Get PageSpeed scores if available
        const pageSpeedResults = currentTestResults.pageSpeed || {};
        const scores = pageSpeedResults.scores || {};
        
        const isPageSpeedTested = Object.keys(scores).length > 0;
        const performanceScore = isPageSpeedTested ? (scores.performance || 'N/A') : 'Not Tested';
        const accessibilityScore = isPageSpeedTested ? (scores.accessibility || 'N/A') : 'Not Tested';
        const bestPracticesScore = isPageSpeedTested ? (scores.best_practices || scores['best-practices'] || scores.bestPractices || 'N/A') : 'Not Tested';
        const seoScore = isPageSpeedTested ? (scores.seo || 'N/A') : 'Not Tested';
        
        // Create CSV header
        let csv = 'URL,Timestamp,Server Response Time,Combined Response Time,Availability,Combined Availability,Download Speed,Upload Speed';
        
        // Add PageSpeed headers if applicable
        if (isPageSpeedTested) {
            csv += ',Performance Score,Accessibility Score,Best Practices Score,SEO Score';
        }
        csv += '\n';
        
        // Add data row
        csv += `"${url}","${timestamp}","${avgResponseTime}","${combinedResponseTime}","${availability}","${combinedAvailability}","${downloadSpeed}","${uploadSpeed}"`;
        
        // Add PageSpeed data if applicable
        if (isPageSpeedTested) {
            csv += `,"${performanceScore}","${accessibilityScore}","${bestPracticesScore}","${seoScore}"`;
        }
        csv += '\n\n';
        
        // Add combined regional data
        if (combinedStats.response_times_by_region) {
            csv += 'Regional Response Times (Combined Data)\n';
            csv += 'Region,Response Time,Availability\n';
            
            Object.entries(combinedStats.response_times_by_region).forEach(([region, time]) => {
                const availability = combinedStats.availability_by_region[region]?.percentage || 0;
                csv += `"${region}","${time} ms","${availability}%"\n`;
            });
            
            csv += '\n';
        }
        
        // Add server test results if available
        const detailedResults = serverSideResults.results || [];
        
        if (detailedResults.length > 0) {
            csv += 'Detailed Test Results\n';
            csv += 'Location,Country,IP,Region,Response Time,Download Speed,Upload Speed,Status\n';
            
            detailedResults.forEach(result => {
                const location = result.location || 'Unknown';
                const country = result.country || 'XX';
                const ip = result.ip || 'Unknown';
                const region = result.region || 'Unknown';
                const responseTime = result.time ? `${result.time} ms` : 'N/A';
                const downloadSpeed = result.download_speed ? `${result.download_speed} Mbps` : 'N/A';
                const uploadSpeed = result.upload_speed ? `${result.upload_speed} Mbps` : 'N/A';
                const status = result.status || 'Unknown';
                
                csv += `"${location}","${country}","${ip}","${region}","${responseTime}","${downloadSpeed}","${uploadSpeed}","${status}"\n`;
            });
        }
        
        // Create and trigger download
        try {
            const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
            const url_download = URL.createObjectURL(blob);
            const link = document.createElement('a');
            
            // Set download attributes
            link.href = url_download;
            link.setAttribute('download', `website_performance_${new Date().toISOString().slice(0, 10)}.csv`);
            link.style.display = 'none';
            
            // Append to body, trigger click, then remove
            document.body.appendChild(link);
            link.click();
            
            // Cleanup
            setTimeout(() => {
                document.body.removeChild(link);
                URL.revokeObjectURL(url_download); // Free memory
                isExporting = false; // Reset export flag
            }, 100);
            
            // Provide user feedback for successful export
            showAlert('Export Successful', 'Test results have been exported to CSV', 'success');
        } catch (e) {
            console.error('Export error:', e);
            showAlert('Export Error', 'Failed to export results: ' + e.message, 'error');
            isExporting = false; // Reset flag on error
        }
    }

    /**
     * Show alert using SweetAlert2
     */
    function showAlert(title, message, type = 'info') {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: title,
                text: message,
                icon: type,
                confirmButtonText: 'OK',
                confirmButtonColor: 'var(--primary-color)',
                showClass: {
                    popup: 'animate__animated animate__fadeInDown'
                },
                hideClass: {
                    popup: 'animate__animated animate__fadeOutUp'
                }
            });
        } else {
            alert(`${title}: ${message}`);
        }
    }
    
    // Initialize fixes on page load
    window.addEventListener('load', function() {
        // Apply initial fixes
        fixStatusDisplay();
        enhanceCheckHostBadges();
        
        // Fix for toggle buttons size
        document.querySelectorAll('.toggle-switch').forEach(toggle => {
            const slider = toggle.querySelector('.toggle-slider');
            if (slider) {
                slider.style.transition = 'all 0.3s cubic-bezier(0.68, -0.55, 0.27, 1.55)';
            }
        });
        
        // Fix step transitions
        const loadingStepsContainer = document.querySelector('.loading-steps-container');
        if (loadingStepsContainer) {
            loadingStepsContainer.style.position = 'relative';
            loadingStepsContainer.style.overflow = 'hidden';
        }
    });
});
</script>
@endsection