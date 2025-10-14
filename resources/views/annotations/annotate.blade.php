<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Image Annotation - {{ $patient->name }}</title>
    <link rel="stylesheet" href="{{ asset('style.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <style>
        body { background-image: url('{{ asset('latar_belakang.png') }}'); }
        
        .annotation-container {
            display: grid;
            grid-template-columns: 250px 1fr;
            gap: 1.5rem;
            max-width: 1600px;
            margin: 0 auto;
        }
        
        .tools-panel {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            height: fit-content;
            position: sticky;
            top: 20px;
        }
        
        .canvas-container {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            overflow: auto;
        }
        
        #annotationCanvas {
            border: 2px solid #ddd;
            border-radius: 8px;
            max-width: 100%;
            width: 100%;
            height: auto;
            cursor: crosshair;
            display: block;
            margin: 0 auto;
        }
        
        .tool-btn {
            width: 100%;
            padding: 0.75rem;
            margin-bottom: 0.5rem;
            border: 2px solid #ddd;
            background: white;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .tool-btn:hover {
            background: #f0f9ff;
            border-color: var(--primary);
        }
        
        .tool-btn.active {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
        }
        
        .tool-section {
            margin-bottom: 1.5rem;
            padding-bottom: 1.5rem;
            border-bottom: 2px solid #f0f0f0;
        }
        
        .tool-section:last-child {
            border-bottom: none;
        }
        
        .tool-section h4 {
            margin: 0 0 1rem 0;
            color: var(--text-main);
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .calibration-input {
            width: 100%;
            padding: 0.5rem;
            border: 2px solid #ddd;
            border-radius: 6px;
            margin-top: 0.5rem;
        }
        
        .action-btn {
            width: 100%;
            padding: 0.75rem;
            margin-bottom: 0.5rem;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.2s;
        }
        
        .btn-save {
            background: var(--green);
            color: white;
        }
        
        .btn-save:hover {
            background: #218838;
        }
        
        .btn-report {
            background: var(--primary);
            color: white;
        }
        
        .btn-report:hover {
            background: #0056b3;
        }
        
        .btn-undo {
            background: #ffc107;
            color: #000;
        }
        
        .btn-clear {
            background: var(--red);
            color: white;
        }
        
        .status-message {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            font-weight: 600;
        }
        
        .status-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .status-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .measurements-list {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 1rem;
            margin-top: 1rem;
            max-height: 300px;
            overflow-y: auto;
        }
        
        .measurement-item {
            padding: 0.5rem;
            background: white;
            border-radius: 6px;
            margin-bottom: 0.5rem;
            font-size: 0.85rem;
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <div class="header-content">
                <div class="brand">
                    <img src="{{ asset('logo.png') }}" alt="Logo">
                    <div>
                        <h1>AIFI Imaging - Annotation Tool</h1>
                        <p class="subtitle">Patient: {{ $patient->name }} | Image #{{ $image->id }}</p>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <div class="page-wrapper">
        <main class="container" style="padding-top:1.5rem;">
            <a href="{{ route('patients.show', $patient) }}" class="btn btn-primary" style="width:auto; display:inline-flex; text-decoration:none; align-items:center; gap:.5rem; margin-bottom:1.5rem;">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
                Back to Patient
            </a>

            <div id="statusMessage"></div>

            <div class="annotation-container">
                <!-- Tools Panel -->
                <div class="tools-panel">
                    <div class="tool-section">
                        <h4>Annotation Tools</h4>
                        <button class="tool-btn" data-tool="arrow">
                            Arrow
                        </button>
                        <button class="tool-btn" data-tool="rectangle">
                            Rectangle
                        </button>
                        <button class="tool-btn" data-tool="circle">
                            Circle
                        </button>
                        <button class="tool-btn" data-tool="text">
                            Text
                        </button>
                        <button class="tool-btn" data-tool="highlight">
                            Highlight Region
                        </button>
                    </div>

                    <div class="tool-section">
                        <h4>Measurement Tools</h4>
                        <label style="font-size: 0.8rem; color: var(--text-sec);">
                            Calibration (px to mm):
                            <input type="number" id="calibrationInput" class="calibration-input" 
                                   value="1.0" step="0.01" min="0.01">
                        </label>
                        <button class="tool-btn" data-tool="distance" style="margin-top: 0.5rem;">
                            Distance
                        </button>
                        <button class="tool-btn" data-tool="angle">
                            Angle
                        </button>
                        <button class="tool-btn" data-tool="area">
                            Area
                        </button>
                    </div>

                    <div class="tool-section">
                        <h4>Actions</h4>
                        <button class="action-btn btn-undo" onclick="annotationCanvas.undo()">
                            Undo
                        </button>
                        <button class="action-btn btn-clear" onclick="annotationCanvas.clear()">
                            Clear All
                        </button>
                        <button class="action-btn btn-save" onclick="saveAnnotations()">
                            Save Annotations
                        </button>
                        <button class="action-btn btn-report" onclick="generateReport()">
                            Generate Report
                        </button>
                    </div>

                    <div id="measurementsList" class="measurements-list" style="display:none;">
                        <h4 style="margin-top:0; font-size:0.85rem;">Measurements:</h4>
                        <div id="measurementsContent"></div>
                    </div>
                </div>

                <!-- Canvas Container -->
                <div class="canvas-container">
                    <canvas id="annotationCanvas"></canvas>
                </div>
            </div>
        </main>
    </div>

    <script src="{{ asset('annotation-tools.js') }}"></script>
    <script>
        let annotationCanvas;
        const imageId = {{ $image->id }};
        const imageUrl = '{{ asset("storage/".$image->original_path) }}';
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        // Initialize canvas
        function initializeCanvas() {
            console.log('Initializing canvas...');
            
            try {
                annotationCanvas = new AnnotationCanvas('annotationCanvas', imageUrl);
                console.log('Canvas initialized:', annotationCanvas);
                
                // Load existing annotations
                loadExistingAnnotations();
                
                // Setup tool buttons
                const toolButtons = document.querySelectorAll('.tool-btn');
                console.log('Found tool buttons:', toolButtons.length);
                
                toolButtons.forEach(btn => {
                    btn.addEventListener('click', () => {
                        console.log('Tool button clicked:', btn.dataset.tool);
                        document.querySelectorAll('.tool-btn').forEach(b => b.classList.remove('active'));
                        btn.classList.add('active');
                        annotationCanvas.setTool(btn.dataset.tool);
                        console.log('Current tool set to:', annotationCanvas.currentTool);
                    });
                });
                
                // Setup calibration input
                document.getElementById('calibrationInput').addEventListener('change', (e) => {
                    annotationCanvas.setPixelToMm(e.target.value);
                });
                
                console.log('Canvas initialization complete!');
            } catch (error) {
                console.error('Canvas initialization failed:', error);
            }
        }
        
        // Try to initialize when DOM is ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initializeCanvas);
        } else {
            // DOM already loaded
            initializeCanvas();
        }

        async function loadExistingAnnotations() {
            try {
                const response = await fetch(`/images/${imageId}/annotations`);
                const data = await response.json();
                
                if (data.status === 'ok' && data.annotations && data.annotations.length > 0) {
                    annotationCanvas.loadAnnotations(data.annotations);
                    showStatus('Loaded existing annotations', 'success');
                }
            } catch (error) {
                console.error('Failed to load annotations:', error);
            }
        }

        async function saveAnnotations() {
            const annotations = annotationCanvas.getAnnotations();
            
            if (annotations.length === 0) {
                showStatus('No annotations to save', 'error');
                return;
            }

            try {
                const response = await fetch(`/images/${imageId}/annotations`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({ annotations })
                });

                const data = await response.json();
                
                if (data.status === 'ok') {
                    showStatus('Annotations saved successfully! Redirecting...', 'success');
                    
                    // Display measurements
                    if (data.summary && data.summary.measurements && data.summary.measurements.length > 0) {
                        displayMeasurements(data.summary.measurements);
                    }
                    
                    // Redirect back to patient page after 2 seconds
                    setTimeout(() => {
                        window.location.href = document.referrer || '/patients';
                    }, 2000);
                } else {
                    showStatus('Failed to save annotations: ' + (data.message || 'Unknown error'), 'error');
                }
            } catch (error) {
                showStatus('Error saving annotations: ' + error.message, 'error');
            }
        }

        async function generateReport() {
            showStatus('Generating report...', 'success');
            
            try {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/images/${imageId}/generate-report`;
                
                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = csrfToken;
                form.appendChild(csrfInput);
                
                document.body.appendChild(form);
                form.submit();
            } catch (error) {
                showStatus('Error generating report: ' + error.message, 'error');
            }
        }

        function showStatus(message, type) {
            const statusDiv = document.getElementById('statusMessage');
            statusDiv.innerHTML = `<div class="status-message status-${type}">${message}</div>`;
            
            setTimeout(() => {
                statusDiv.innerHTML = '';
            }, 5000);
        }

        function displayMeasurements(measurements) {
            const listDiv = document.getElementById('measurementsList');
            const contentDiv = document.getElementById('measurementsContent');
            
            contentDiv.innerHTML = '';
            
            measurements.forEach((m, idx) => {
                const item = document.createElement('div');
                item.className = 'measurement-item';
                
                let value = '';
                if (m.type === 'distance') {
                    value = `${m.distance_mm.toFixed(2)} mm`;
                } else if (m.type === 'angle') {
                    value = `${m.angle_degrees.toFixed(1)}°`;
                } else if (m.type === 'area') {
                    value = `${m.area_mm2.toFixed(2)} mm²`;
                }
                
                item.innerHTML = `
                    <strong>${idx + 1}. ${m.type.toUpperCase()}</strong><br>
                    ${m.label ? `Label: ${m.label}<br>` : ''}
                    Value: ${value}
                `;
                
                contentDiv.appendChild(item);
            });
            
            listDiv.style.display = 'block';
        }
    </script>
</body>
</html>
