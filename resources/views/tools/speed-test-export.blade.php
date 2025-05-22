<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Website Speed Test Results: {{ $url }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.css">
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
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            color: var(--neutral-800);
            background-color: #f5f7fa;
            margin: 0;
            padding: 0;
        }
        
        .speed-test-container {
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 1rem;
        }
        
        /* Export Header */
        .export-header {
            background: var(--primary-gradient);
            color: white;
            padding: 2rem;
            margin-bottom: 2rem;
            border-radius: 0 0 1rem 1rem;
            text-align: center;
        }
        
        .export-title {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
        }
        
        .export-subtitle {
            font-size: 1.2rem;
            opacity: 0.9;
        }
        
        .export-meta {
            margin-top: 1rem;
            font-size: 0.9rem;
            opacity: 0.8;
        }

        /* Website Overview Section */
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
        
        /* Section styling */
        .section {
            margin-bottom: var(--section-gap);
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
            position: relative;
            overflow: hidden;
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
            background-color: var(--success-color);
            color: white;
        }

        .status-average {
            background-color: var(--warning-color);
            color: white;
        }

        .status-poor {
            background-color: var(--danger-color);
            color: white;
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
        
        /* Test Locations Details Section */
        .location-table-wrapper {
            background-color: white;
            border-radius: var(--card-radius);
            box-shadow: var(--card-shadow);
            padding: 1.5rem;
            overflow: auto;
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
        }

        .data-table tr:last-child td {
            border-bottom: none;
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
        
        /* Status cells */
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
        
        /* PageSpeed Insights Section */
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
        
        /* Footer */
        .export-footer {
            text-align: center;
            margin-top: 3rem;
            padding: 2rem 0;
            color: var(--neutral-500);
            font-size: 0.9rem;
            border-top: 1px solid var(--neutral-200);
        }
        
        @media print {
            body {
                background-color: white;
            }
            
            .speed-test-container {
                max-width: 100%;
                padding: 0;
            }
            
            .export-header {
                border-radius: 0;
            }
            
            .section {
                page-break-inside: avoid;
            }
            
            .chart-container {
                max-height: 300px;
            }
        }
    </style>
</head>
<body>
    <div class="speed-test-container">
        <!-- Export Header -->
        <div class="export-header">
            <h1 class="export-title">Website Speed Test Results</h1>
            <p class="export-subtitle">Global Performance Analysis Report</p>
            <p class="export-meta">Generated on {{ $generatedAt }}</p>
        </div>
        
        <!-- Website Overview Section -->
        <div class="website-overview">
            <div class="website-info">
                <!-- URL Information -->
                <div class="website-url">
                    <div class="website-url-title">
                        <i class="fas fa-globe text-primary"></i>
                        <span>Website</span>
                    </div>
                    <div class="website-url-value">{{ $url }}</div>
                    <div class="website-url-meta">Test run on {{ isset($testResults['timestamp']) ? date('F j, Y, g:i a', $testResults['timestamp']) : $generatedAt }}</div>
                </div>
                
                <!-- User Location (if available) -->
                @if(isset($testResults['user_location']))
                <div class="user-location">
                    <img src="https://flagcdn.com/w40/{{ strtolower($testResults['user_location']['location']['country_code'] ?? 'xx') }}.png" 
                         alt="{{ $testResults['user_location']['location']['country'] ?? 'Unknown' }}" 
                         class="location-flag">
                    <div class="location-info">
                        <div class="location-name">
                            {{ $testResults['user_location']['location']['city'] ?? 'Unknown' }}, 
                            {{ $testResults['user_location']['location']['country'] ?? 'Unknown' }}
                        </div>
                        <div class="location-ip">IP: {{ $testResults['user_location']['ip'] ?? 'Unknown' }}</div>
                    </div>
                </div>
                @endif
            </div>
        </div>
        
        <!-- Key Metrics Section -->
        @if(isset($testResults['combined_stats']) || isset($testResults['server_side']['stats']))
        <div class="section">
            <h2 class="section-title">
                <i class="fas fa-tachometer-alt"></i> Key Performance Metrics
            </h2>
            
            <div class="metrics-grid">
                <!-- Response Time -->
                <div class="metric-card">
                    <div class="metric-header">
                        <div class="metric-title">
                            <i class="fas fa-tachometer-alt metric-icon"></i> Response Time
                        </div>
                        <div class="metric-source-badge {{ isset($testResults['combined_stats']) ? 'combined' : '' }}">
                            {{ isset($testResults['combined_stats']) ? 'Combined' : 'Server' }}
                        </div>
                    </div>
                    <div class="metric-value">
                        {{ isset($testResults['combined_stats']['average_response_time']) 
                            ? $testResults['combined_stats']['average_response_time'] 
                            : (isset($testResults['server_side']['stats']['average_time']) 
                                ? $testResults['server_side']['stats']['average_time'] 
                                : '-') }} ms
                    </div>
                    <div class="metric-label">Average server response time</div>
                    @php
                        $respTime = isset($testResults['combined_stats']['average_response_time']) 
                            ? $testResults['combined_stats']['average_response_time'] 
                            : (isset($testResults['server_side']['stats']['average_time']) 
                                ? $testResults['server_side']['stats']['average_time'] 
                                : 0);
                        $respStatus = $respTime < 300 ? 'status-good' : ($respTime < 800 ? 'status-average' : 'status-poor');
                        $respText = $respTime < 300 ? 'Good' : ($respTime < 800 ? 'Average' : 'Slow');
                    @endphp
                    <div class="metric-status {{ $respStatus }}">{{ $respText }}</div>
                </div>
                
                <!-- Availability -->
                <div class="metric-card">
                    <div class="metric-header">
                        <div class="metric-title">
                            <i class="fas fa-signal metric-icon"></i> Availability
                        </div>
                        <div class="metric-source-badge {{ isset($testResults['combined_stats']) ? 'combined' : '' }}">
                            {{ isset($testResults['combined_stats']) ? 'Combined' : 'Server' }}
                        </div>
                    </div>
                    <div class="metric-value">
                        {{ isset($testResults['combined_stats']['global_availability']) 
                            ? $testResults['combined_stats']['global_availability'] 
                            : (isset($testResults['server_side']['stats']['success_rate']) 
                                ? $testResults['server_side']['stats']['success_rate'] 
                                : '-') }}%
                    </div>
                    <div class="metric-label">Global availability percentage</div>
                    @php
                        $availPercent = isset($testResults['combined_stats']['global_availability']) 
                            ? $testResults['combined_stats']['global_availability'] 
                            : (isset($testResults['server_side']['stats']['success_rate']) 
                                ? $testResults['server_side']['stats']['success_rate'] 
                                : 0);
                        $availStatus = $availPercent > 90 ? 'status-good' : ($availPercent > 70 ? 'status-average' : 'status-poor');
                        $availText = $availPercent > 90 ? 'Good' : ($availPercent > 70 ? 'Average' : 'Poor');
                    @endphp
                    <div class="metric-status {{ $availStatus }}">{{ $availText }}</div>
                </div>
                
                <!-- Download Speed -->
                @if(isset($testResults['server_side']['stats']['avg_download_speed']))
                <div class="metric-card">
                    <div class="metric-header">
                        <div class="metric-title">
                            <i class="fas fa-download metric-icon"></i> Download Speed
                        </div>
                        <div class="metric-source-badge">Server</div>
                    </div>
                    <div class="metric-value">{{ $testResults['server_side']['stats']['avg_download_speed'] ?? '-' }} Mbps</div>
                    <div class="metric-label">Average download speed</div>
                    @php
                        $downSpeed = $testResults['server_side']['stats']['avg_download_speed'] ?? 0;
                        $downStatus = $downSpeed > 10 ? 'status-good' : ($downSpeed > 5 ? 'status-average' : 'status-poor');
                        $downText = $downSpeed > 10 ? 'Good' : ($downSpeed > 5 ? 'Average' : 'Slow');
                    @endphp
                    <div class="metric-status {{ $downStatus }}">{{ $downText }}</div>
                </div>
                @endif
                
                <!-- Upload Speed -->
                @if(isset($testResults['server_side']['stats']['avg_upload_speed']))
                <div class="metric-card">
                    <div class="metric-header">
                        <div class="metric-title">
                            <i class="fas fa-upload metric-icon"></i> Upload Speed
                        </div>
                        <div class="metric-source-badge">Server</div>
                    </div>
                    <div class="metric-value">{{ $testResults['server_side']['stats']['avg_upload_speed'] ?? '-' }} Mbps</div>
                    <div class="metric-label">Average upload speed</div>
                    @php
                        $upSpeed = $testResults['server_side']['stats']['avg_upload_speed'] ?? 0;
                        $upStatus = $upSpeed > 5 ? 'status-good' : ($upSpeed > 2 ? 'status-average' : 'status-poor');
                        $upText = $upSpeed > 5 ? 'Good' : ($upSpeed > 2 ? 'Average' : 'Slow');
                    @endphp
                    <div class="metric-status {{ $upStatus }}">{{ $upText }}</div>
                </div>
                @endif
            </div>
        </div>
        @endif
        
        <!-- Regional Performance Section -->
        @if(isset($testResults['combined_stats']['response_times_by_region']) || isset($testResults['server_side']['stats']['region_stats']))
        <div class="section">
            <h2 class="section-title">
                <i class="fas fa-globe-americas"></i> Regional Performance
            </h2>
            
            <div class="regions-grid">
                @php
                    $regions = [];
                    if(isset($testResults['combined_stats']['response_times_by_region'])) {
                        $regions = array_keys($testResults['combined_stats']['response_times_by_region']);
                    } elseif(isset($testResults['server_side']['stats']['region_stats'])) {
                        $regions = array_keys($testResults['server_side']['stats']['region_stats']);
                    }
                @endphp
                
                @foreach($regions as $region)
                    @php
                        $responseTime = isset($testResults['combined_stats']['response_times_by_region'][$region]) 
                            ? $testResults['combined_stats']['response_times_by_region'][$region] 
                            : (isset($testResults['server_side']['stats']['region_stats'][$region]['avgResponseTime']) 
                                ? $testResults['server_side']['stats']['region_stats'][$region]['avgResponseTime'] 
                                : null);
                                
                        $availability = isset($testResults['combined_stats']['availability_by_region'][$region]['percentage']) 
                            ? $testResults['combined_stats']['availability_by_region'][$region]['percentage'] 
                            : null;
                            
                        if($availability === null && isset($testResults['server_side']['stats']['region_stats'][$region])) {
                            $regionStats = $testResults['server_side']['stats']['region_stats'][$region];
                            if(isset($regionStats['total']) && $regionStats['total'] > 0) {
                                $online = ($regionStats['online'] ?? 0) + ($regionStats['slow'] ?? 0);
                                $availability = round(($online / $regionStats['total']) * 100, 1);
                            }
                        }
                        
                        $nodes = isset($testResults['combined_stats']['availability_by_region'][$region]['successful']) 
                            ? $testResults['combined_stats']['availability_by_region'][$region]['successful'] . '/' . 
                              $testResults['combined_stats']['availability_by_region'][$region]['total'] . ' online'
                            : '-';
                            
                        $statusIcon = $availability > 90 
                            ? '<i class="fas fa-check-circle text-success"></i>' 
                            : ($availability > 50 
                                ? '<i class="fas fa-exclamation-triangle text-warning"></i>' 
                                : '<i class="fas fa-times-circle text-danger"></i>');
                    @endphp
                
                    <div class="region-card">
                        <div class="region-header">
                            <div class="region-title">
                                <i class="fas fa-map-marker-alt region-icon"></i> {{ $region }}
                            </div>
                            <div>{!! $statusIcon !!}</div>
                        </div>
                        <div class="region-status">
                            <div class="region-stat">
                                <span class="stat-label">Response Time:</span>
                                <span class="stat-value">{{ $responseTime ? $responseTime . ' ms' : '-' }}</span>
                            </div>
                            <div class="region-stat">
                                <span class="stat-label">Availability:</span>
                                <span class="stat-value">{{ $availability ? $availability . '%' : '-' }}</span>
                            </div>
                            <div class="region-stat">
                                <span class="stat-label">Test Nodes:</span>
                                <span class="stat-value">{{ $nodes }}</span>
                            </div>
                            <div class="data-source">
                                Source: {{ isset($testResults['combined_stats']) ? 'Server + CheckHost' : 'Server' }}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif
        
        <!-- Test Locations Details Section -->
        @if(isset($testResults['server_side']['results']) || isset($testResults['check_host']['http']))
        <div class="section">
            <h2 class="section-title">
                <i class="fas fa-server"></i> Test Locations Details
            </h2>
            
            <div class="location-table-wrapper">
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
                    <tbody>
                        @php
                            // Process server-side results
                            $locationResults = [];
                            
                            if(isset($testResults['server_side']['results']) && is_array($testResults['server_side']['results'])) {
                                // Group by region
                                $serverRegions = [];
                                
                                foreach($testResults['server_side']['results'] as $result) {
                                    $region = $result['region'] ?? 'Other';
                                    
                                    if(!isset($serverRegions[$region])) {
                                        $serverRegions[$region] = [];
                                    }
                                    
                                    $serverRegions[$region][] = $result;
                                }
                                
                                // Sort regions alphabetically
                                ksort($serverRegions);
                                
                                // Add to location results
                                foreach($serverRegions as $region => $results) {
                                    $locationResults[$region] = $results;
                                }
                            }
                            
                            // Process CheckHost results if available
                            if(isset($testResults['check_host']['http']) && is_array($testResults['check_host']['http'])) {
                                $checkHostResults = $testResults['check_host']['http'];
                                $pingResults = $testResults['check_host']['ping'] ?? [];
                                
                                // Helper function to get region from node code
                                $getRegionFromNode = function($node) {
                                    $countryCode = substr($node, 0, 2);
                                    $regions = [
                                        'us' => 'North America', 'ca' => 'North America', 'mx' => 'North America',
                                        'uk' => 'Europe', 'gb' => 'Europe', 'de' => 'Europe', 'fr' => 'Europe', 
                                        'es' => 'Europe', 'it' => 'Europe', 'nl' => 'Europe', 'ru' => 'Europe', 
                                        'jp' => 'Asia', 'cn' => 'Asia', 'sg' => 'Asia', 'in' => 'Asia', 'kr' => 'Asia',
                                        'au' => 'Oceania', 'nz' => 'Oceania',
                                        'br' => 'South America', 'ar' => 'South America', 'cl' => 'South America'
                                    ];
                                    
                                    return $regions[$countryCode] ?? 'Other';
                                };
                                
                                // Process results by node
                                foreach($checkHostResults as $node => $result) {
                                    $isSuccessful = false;
                                    $responseTime = null;
                                    $region = $getRegionFromNode($node);
                                    
                                    // Parse the result data format
                                    if(is_array($result)) {
                                        if(isset($result['status'])) {
                                            $isSuccessful = $result['status'] === 'online' || $result['status'] === 'slow';
                                            $responseTime = $result['response_time_ms'] ?? null;
                                            $status = $result['status'] ?? 'offline';
                                        } elseif(isset($result[0]) && is_array($result[0]) && count($result[0]) >= 2) {
                                            $isSuccessful = !empty($result[0][0]);
                                            $responseTime = isset($result[0][1]) ? round($result[0][1] * 1000) : null;
                                            $status = $isSuccessful ? ($responseTime < 300 ? 'online' : 'slow') : 'offline';
                                        } else {
                                            $status = 'offline';
                                        }
                                    } else {
                                        $status = 'offline';
                                    }
                                    
                                    // Add to location results
                                    if(!isset($locationResults[$region])) {
                                        $locationResults[$region] = [];
                                    }
                                    
                                    $locationResults[$region][] = [
                                        'location' => $node,
                                        'country' => strtoupper(substr($node, 0, 2)),
                                        'source' => 'checkhost',
                                        'status' => $status,
                                        'time' => $responseTime,
                                        'error' => null
                                    ];
                                }
                            }
                        @endphp
                        
                        @foreach($locationResults as $region => $results)
                            <tr class="table-light">
                                <td colspan="5" class="fw-bold">{{ $region }}</td>
                            </tr>
                            
                            @foreach($results as $result)
                                @php
                                    $status = $result['status'] ?? 'offline';
                                    $responseTime = isset($result['time']) && $result['time'] > 0 
                                        ? $result['time'] . ' ms' 
                                        : ($result['error'] ?? 'No response');
                                    
                                    $downloadSpeed = isset($result['download_speed']) && $result['download_speed'] > 0 
                                        ? $result['download_speed'] . ' Mbps' 
                                        : 'N/A';
                                    
                                    $uploadSpeed = isset($result['upload_speed']) && $result['upload_speed'] > 0 
                                        ? $result['upload_speed'] . ' Mbps' 
                                        : 'N/A';
                                    
                                    $speedInfo = $downloadSpeed . ' / ' . $uploadSpeed;
                                    
                                    $responseClass = $status === 'online' 
                                        ? 'text-success' 
                                        : ($status === 'slow' ? 'text-warning' : 'text-danger');
                                    
                                    // Create status indicator
                                    if($status === 'online') {
                                        $statusBadge = '<div class="metric-status status-good" style="white-space:nowrap;"><i class="fas fa-check-circle"></i> Online</div>';
                                    } elseif($status === 'slow') {
                                        $statusBadge = '<div class="metric-status status-average" style="white-space:nowrap;"><i class="fas fa-exclamation-triangle"></i> Slow</div>';
                                    } else {
                                        $statusBadge = '<div class="metric-status status-poor" style="white-space:nowrap;"><i class="fas fa-times-circle"></i> Offline</div>';
                                    }
                                    
                                    // Create source badge
                                    $sourceBadge = $result['source'] === 'checkhost' 
                                        ? '<span class="source-badge source-checkhost"><i class="fas fa-globe-americas"></i> CheckHost</span>'
                                        : '<span class="source-badge source-server"><i class="fas fa-server"></i> Server</span>';
                                @endphp
                                
                                <tr class="{{ $status === 'online' ? 'status-online' : ($status === 'slow' ? 'status-slow' : 'status-offline') }}">
                                    <td>
                                        <div class="node-location">
                                            <img src="https://flagcdn.com/w20/{{ strtolower($result['country'] ?? 'xx') }}.png" 
                                                 class="flag-icon" alt="{{ $result['country'] ?? 'Unknown' }}">
                                            <div>
                                                <span>{{ $result['location'] ?? 'Unknown' }}</span>
                                                @if(isset($result['ip']))
                                                <small class="text-muted d-none d-md-block">{{ $result['ip'] }}</small>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>{!! $sourceBadge !!}</td>
                                    <td class="{{ $responseClass }}">{{ $responseTime }}</td>
                                    <td>{{ $speedInfo }}</td>
                                    <td style="text-align:center;">{!! $statusBadge !!}</td>
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
        
        <!-- PageSpeed Insights Section -->
        @if(isset($testResults['pageSpeed']))
        <div class="section pagespeed-section">
            <h2 class="section-title">
                <i class="fas fa-gauge-high"></i> PageSpeed Insights
            </h2>
            
            <!-- Performance Scores -->
            <h3 class="section-subtitle">Performance Scores</h3>
            <div class="pagespeed-grid">
                @php
                    $scores = $testResults['pageSpeed']['scores'] ?? [];
                    
                    $metricScores = [
                        [
                            'title' => 'Performance',
                            'value' => $scores['performance'] ?? null,
                            'class' => 'performance'
                        ],
                        [
                            'title' => 'Accessibility',
                            'value' => $scores['accessibility'] ?? null,
                            'class' => 'accessibility'
                        ],
                        [
                            'title' => 'Best Practices',
                            'value' => $scores['best_practices'] ?? $scores['best-practices'] ?? $scores['bestPractices'] ?? null,
                            'class' => 'best-practices'
                        ],
                        [
                            'title' => 'SEO',
                            'value' => $scores['seo'] ?? null,
                            'class' => 'seo'
                        ]
                    ];
                @endphp
                
                @foreach($metricScores as $metric)
                    <div class="score-card">
                        <div class="score-title">{{ $metric['title'] }}</div>
                        <div class="score-circle">
                            @php
                                $score = $metric['value'] ?? 0;
                                $circumference = 100;
                                $dashoffset = $circumference - ($score / 100) * $circumference;
                                
                                $scoreClass = $score >= 90 
                                    ? 'progress-good' 
                                    : ($score >= 50 ? 'progress-average' : 'progress-poor');
                            @endphp
                            
                            <svg viewBox="0 0 36 36">
                                <circle class="circle-bg" cx="18" cy="18" r="16"></circle>
                                <circle class="circle-progress {{ $scoreClass }}" cx="18" cy="18" r="16" 
                                        stroke-dasharray="{{ $circumference }}" 
                                        stroke-dashoffset="{{ $dashoffset }}"></circle>
                            </svg>
                            <div class="score-value">{{ $score }}</div>
                        </div>
                        <div class="score-label">{{ strtolower($metric['title']) }} score</div>
                    </div>
                @endforeach
            </div>
            
            <!-- Core Web Vitals -->
            @if(isset($testResults['pageSpeed']['metrics']))
            <h3 class="section-subtitle mt-4">Core Web Vitals</h3>
            <div class="web-vitals-grid">
                @php
                    $webVitals = $testResults['pageSpeed']['metrics'] ?? [];
                @endphp
                
                <!-- LCP -->
                @if(isset($webVitals['largest_contentful_paint']))
                <div class="vital-card">
                    <div class="vital-header">
                        <div class="vital-title">
                            <i class="fas fa-image text-primary"></i> LCP
                        </div>
                        @php
                            $lcp = $webVitals['largest_contentful_paint'];
                            $lcpValue = $lcp['value'] ?? null;
                            $lcpScore = $lcp['score'] ?? 0;
                            
                            $lcpStatus = $lcpScore >= 0.9 
                                ? 'status-good' 
                                : ($lcpScore >= 0.5 ? 'status-average' : 'status-poor');
                            
                            $lcpText = $lcpScore >= 0.9 
                                ? 'Good' 
                                : ($lcpScore >= 0.5 ? 'Needs Improvement' : 'Poor');
                        @endphp
                        <div class="metric-status {{ $lcpStatus }}">{{ $lcpText }}</div>
                    </div>
                    <div class="vital-value">{{ $lcpValue }}</div>
                    <div class="vital-description">
                        Largest Contentful Paint measures loading performance. To provide a good user experience, LCP should occur within 2.5 seconds of page load.
                    </div>
                </div>
                @endif
                
                <!-- CLS -->
                @if(isset($webVitals['cumulative_layout_shift']))
                <div class="vital-card">
                    <div class="vital-header">
                        <div class="vital-title">
                            <i class="fas fa-arrows-alt text-primary"></i> CLS
                        </div>
                        @php
                            $cls = $webVitals['cumulative_layout_shift'];
                            $clsValue = $cls['value'] ?? null;
                            $clsScore = $cls['score'] ?? 0;
                            
                            $clsStatus = $clsScore >= 0.9 
                                ? 'status-good' 
                                : ($clsScore >= 0.5 ? 'status-average' : 'status-poor');
                            
                            $clsText = $clsScore >= 0.9 
                                ? 'Good' 
                                : ($clsScore >= 0.5 ? 'Needs Improvement' : 'Poor');
                        @endphp
                        <div class="metric-status {{ $clsStatus }}">{{ $clsText }}</div>
                    </div>
                    <div class="vital-value">{{ $clsValue }}</div>
                    <div class="vital-description">
                        Cumulative Layout Shift measures visual stability. To provide a good user experience, pages should maintain a CLS of less than 0.1.
                    </div>
                </div>
                @endif
                
                <!-- TBT -->
                @if(isset($webVitals['total_blocking_time']))
                <div class="vital-card">
                    <div class="vital-header">
                        <div class="vital-title">
                            <i class="fas fa-clock text-primary"></i> TBT
                        </div>
                        @php
                            $tbt = $webVitals['total_blocking_time'];
                            $tbtValue = $tbt['value'] ?? null;
                            $tbtScore = $tbt['score'] ?? 0;
                            
                            $tbtStatus = $tbtScore >= 0.9 
                                ? 'status-good' 
                                : ($tbtScore >= 0.5 ? 'status-average' : 'status-poor');
                            
                            $tbtText = $tbtScore >= 0.9 
                                ? 'Good' 
                                : ($tbtScore >= 0.5 ? 'Needs Improvement' : 'Poor');
                        @endphp
                        <div class="metric-status {{ $tbtStatus }}">{{ $tbtText }}</div>
                    </div>
                    <div class="vital-value">{{ $tbtValue }}</div>
                    <div class="vital-description">
                        Total Blocking Time measures interactivity. To provide a good user experience, sites should strive to have a TBT of less than 200 milliseconds.
                    </div>
                </div>
                @endif
            </div>
            @endif
            
            <!-- Improvement Opportunities -->
            @if(isset($testResults['pageSpeed']['opportunities']) && !empty($testResults['pageSpeed']['opportunities']))
            <h3 class="section-subtitle mt-4">Improvement Opportunities</h3>
            <div>
                @foreach($testResults['pageSpeed']['opportunities'] as $opportunity)
                <div class="opportunity">
                    <div class="opportunity-icon">
                        <i class="fas fa-lightbulb"></i>
                    </div>
                    <div class="opportunity-content">
                        <div class="opportunity-title">{{ $opportunity['title'] ?? 'Improvement Opportunity' }}</div>
                        <div class="opportunity-description">{{ $opportunity['description'] ?? '' }}</div>
                    </div>
                    <div class="opportunity-value">{{ $opportunity['display_value'] ?? 'N/A' }}</div>
                </div>
                @endforeach
            </div>
            @endif
        </div>
        @endif
        
        <!-- Footer -->
        <div class="export-footer">
            <p>Website Speed Test Report - Generated on {{ $generatedAt }}</p>
            <p>Results combine data from multiple sources to provide comprehensive performance insights.</p>
        </div>
    </div>
    
    <script>
        // Add print button
        window.onload = function() {
            const header = document.querySelector('.export-header');
            if (header) {
                const printBtn = document.createElement('button');
                printBtn.style.cssText = 'background: white; color: #4f46e5; border: none; padding: 8px 16px; border-radius: 4px; margin-top: 16px; font-weight: 600; cursor: pointer; transition: all 0.2s ease;';
                printBtn.innerHTML = '<i class="fas fa-print" style="margin-right: 8px;"></i> Print Report';
                printBtn.addEventListener('mouseover', function() {
                    this.style.backgroundColor = '#f5f5f5';
                });
                printBtn.addEventListener('mouseout', function() {
                    this.style.backgroundColor = 'white';
                });
                printBtn.addEventListener('click', function() {
                    window.print();
                });
                header.appendChild(printBtn);
            }
        };
    </script>
</body>
</html>