<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Patient {{ $patient->name }}</title>
    <link rel="stylesheet" href="{{ asset('style.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <style>
        body { background-image: url('{{ asset('latar_belakang.png') }}'); }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <div class="header-content">
                <div class="brand">
                    <img src="{{ asset('logo.png') }}" alt="Logo">
                    <div>
                        <h1>AIFI Imaging</h1>
                        <p class="subtitle">Patient: {{ $patient->name }} ({{ $patient->identifier }})</p>
                    </div>
                </div>
                <div id="status-badge" class="status-badge status-active">
                    <div id="status-dot" class="status-dot" style="background:var(--green);"></div>
                    <span id="status-text">ACTIVE</span>
                </div>
            </div>
        </div>
    </header>

    <div class="page-wrapper">
    <main class="container" style="padding-top:1.5rem;">
        <a href="{{ route('patients.index') }}" class="btn btn-primary" style="width:auto; display:inline-flex; text-decoration:none; align-items:center; gap:.5rem; margin-bottom:1.5rem;">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
            Back to Patients
        </a>

        <div class="card" style="max-width:900px; margin:0 auto 1.5rem;">
            <h2 style="margin:0 0 1.5rem 0; color:var(--text-main); font-size:1.5rem;">Patient Details</h2>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:1.5rem;">
                <div>
                    <label style="font-weight:700; font-size:.8rem; color:var(--text-sec); text-transform:uppercase; display:block; margin-bottom:.5rem; letter-spacing:.5px;">Name</label>
                    <div style="padding:1rem; background:#fff; border-radius:10px; border:2px solid var(--gray-300); font-size:1.05rem; font-weight:600;">{{ $patient->name }}</div>
                </div>
                <div>
                    <label style="font-weight:700; font-size:.8rem; color:var(--text-sec); text-transform:uppercase; display:block; margin-bottom:.5rem; letter-spacing:.5px;">Email</label>
                    <div style="padding:1rem; background:#fff; border-radius:10px; border:2px solid var(--gray-300); font-size:1.05rem; font-weight:600;">{{ $patient->identifier }}</div>
                </div>
            </div>
            
            <div style="margin-top:1.5rem;">
                <label style="font-weight:700; font-size:.8rem; color:var(--text-sec); text-transform:uppercase; display:block; margin-bottom:.5rem; letter-spacing:.5px;">Notes</label>
                @auth
                <form method="post" action="{{ route('patients.update', $patient) }}" style="margin:0;">
                    @csrf
                    @method('PUT')
                    <textarea name="notes" style="min-height:120px; resize:vertical; font-size:.95rem;" placeholder="Add notes about this patient...">{{ $patient->notes }}</textarea>
                    <button type="submit" class="btn btn-primary" style="margin-top:1rem; width:auto;">Save</button>
                </form>
                @else
                <div style="padding:1.25rem; background:#fff; border-left:4px solid var(--primary); border-radius:10px; line-height:1.7; min-height:80px; border:2px solid var(--gray-300);">
                    {{ $patient->notes ?: 'No notes available.' }}
                </div>
                @endauth
            </div>
        </div>

        <div class="main-grid" style="max-width:1200px; margin:0 auto;">
            <div class="col-span-3 space-y-6">
                <div class="card">
                    <h3 style="font-weight:bold; margin-top:0;">Upload Image</h3>
                    @auth
                    <form method="post" action="{{ route('images.upload', $patient) }}" enctype="multipart/form-data">
                        @csrf
                        <input type="file" name="image" accept="image/*,.dcm" required />
                        <button type="submit" class="btn btn-primary" style="margin-top:.75rem; width:auto;">Upload</button>
                        <p style="font-size:0.85rem; color:var(--text-sec); margin-top:0.5rem;">Supported: PNG, JPG, JPEG, DCM, BMP, GIF, WEBP (max 20MB)</p>
                    </form>
                    @else
                        <a href="{{ route('auth.login') }}" class="btn btn-primary" style="width:auto; display:inline-block; text-decoration:none; text-align:center;">Login to upload</a>
                    @endauth
                    @if(session('status'))
                        <p style="margin-top:.75rem; color:var(--green);">{{ session('status') }}</p>
                    @endif
                    @if(session('error'))
                        <p style="margin-top:.75rem; color:var(--red);">{{ session('error') }}</p>
                    @endif
                </div>

                <div id="image-preview-section" class="card" style="padding:0; {{ $patient->images->count() ? '' : 'display:none;' }}">
                    <div class="preview-header">
                        <h3 style="font-weight:bold; margin:0;">Image Preview</h3>
                        <div class="preview-toggle">
                            <button id="view-original-btn" class="active">NORMAL</button>
                            <button id="view-processed-btn">FILTER</button>
                        </div>
                    </div>
                    <div class="preview-content">
                        @php $first = $patient->images->first(); @endphp
                        @if($first)
                        <img id="original-image" src="{{ asset('storage/'.$first->original_path) }}" alt="Original">
                        <img id="processed-image" src="{{ $first->processed_path ? asset('storage/'.$first->processed_path) : '' }}" alt="Processed" class="{{ $first->processed_path ? 'hidden' : 'hidden' }}">
                        @endif
                    </div>
                </div>

                <div class="card">
                    <h3 style="font-weight:bold; margin-top:0;">Images ({{ $patient->images->count() }})</h3>
                    <div class="space-y-2">
                        @forelse($patient->images as $img)
                        <div class="card" style="padding: .75rem;">
                            <div class="row" style="justify-content: space-between;">
                                <div style="color:var(--text-sec);">#{{ $img->id }}</div>
                                @auth
                                <form method="post" action="{{ route('images.destroy', $img) }}" onsubmit="return confirm('Delete this image?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger" style="width:auto; font-size:.85rem; padding:.35rem .7rem;">Delete</button>
                                </form>
                                @endauth
                            </div>
                            <div class="row" style="margin-top:.5rem; gap: .75rem; flex-wrap: wrap;">
                                <img src="{{ asset('storage/'.$img->original_path) }}" alt="original" style="max-width:120px; border-radius:.375rem;">
                                @if($img->processed_path)
                                <img src="{{ asset('storage/'.$img->processed_path) }}" alt="processed" style="max-width:120px; border-radius:.375rem; border:2px solid #28a745;">
                                @endif
                                <div style="display:flex; flex-direction:column; gap:.5rem;">
                                    <button class="btn btn-primary" style="width:auto; font-size:.85rem; padding:.35rem .7rem;" onclick="selectImage({{ $img->id }}, '{{ asset('storage/'.$img->original_path) }}', '{{ $img->processed_path ? asset('storage/'.$img->processed_path) : '' }}')">Preview</button>
                                    @auth
                                    <form method="post" action="{{ route('images.report.generate', $img) }}" style="margin:0;">
                                        @csrf
                                        <button type="submit" class="btn btn-primary" style="width:100%; font-size:.85rem; padding:.35rem .7rem; background:#17a2b8; border-color:#17a2b8;">Generate Report</button>
                                    </form>
                                    @endauth
                                </div>
                            </div>
                            @if($img->features_text && $img->processed_path)
                                {{-- Show current filter features --}}
                                <div style="margin-top:.75rem; padding:1rem; background:#f0f9ff; border-left:4px solid #28a745; border-radius:.375rem;">
                                    <h4 style="margin:0 0 .5rem 0; color:#28a745; font-size:.9rem;">{{ $img->method }}</h4>
                                    <pre style="white-space:pre-wrap; color:var(--text-main); font-size:.85rem; line-height:1.6; margin:0;">{{ $img->features_text }}</pre>
                                </div>
                            @endif

                            @if($img->processing_history && count($img->processing_history) > 0)
                                {{-- Show processing history (max 3) --}}
                                <div style="margin-top:.75rem; padding:1rem; background:#fff3cd; border-left:4px solid #ffc107; border-radius:.375rem;">
                                    <h4 style="margin:0 0 .75rem 0; color:#856404; font-size:.9rem; font-weight:600;">Processing History ({{ count($img->processing_history) }})</h4>
                                    <div style="display:grid; gap:.75rem;">
                                        @foreach($img->processing_history as $idx => $history)
                                        <div style="padding:.75rem; background:white; border-radius:.375rem; border:1px solid #ffc107;">
                                            <div style="display:flex; justify-content:space-between; align-items:start; margin-bottom:.5rem;">
                                                <strong style="color:#856404; font-size:.85rem;">{{ $idx + 1 }}. {{ $history['method'] ?? 'Unknown' }}</strong>
                                                <span style="font-size:.75rem; color:#6c757d;">{{ \Carbon\Carbon::parse($history['timestamp'] ?? now())->format('d/m/Y H:i') }}</span>
                                            </div>
                                            @if(isset($history['path']) && \Storage::disk('public')->exists($history['path']))
                                            <img src="{{ asset('storage/'.$history['path']) }}" alt="history" style="max-width:100%; height:auto; border-radius:.25rem; margin-bottom:.5rem; border:1px solid #dee2e6;" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                                            <div style="display:none; padding:1rem; background:#f8d7da; color:#721c24; border-radius:.25rem; text-align:center;">Image failed to load</div>
                                            @else
                                            <div style="padding:1rem; background:#f8d7da; color:#721c24; border-radius:.25rem; text-align:center;">Image file not found</div>
                                            @endif
                                            <p style="margin:0; font-size:.8rem; color:#6c757d; line-height:1.4;">{{ $history['features_text'] ?? 'No description' }}</p>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                            @auth
                            <form id="proc-{{ $img->id }}" method="post" action="{{ route('images.process', $img) }}">
                                @csrf
                                <input type="hidden" name="method" value="">
                            </form>
                            <form id="forensic-{{ $img->id }}" method="post" action="{{ route('images.forensic.analyze', $img) }}">
                                @csrf
                            </form>
                            @endauth
                        </div>
                        @empty
                        <div style="padding: 2rem; text-align: center; color: var(--text-sec);">
                            <p style="font-size: 1.1rem; margin: 0;">No images uploaded yet</p>
                            <p style="font-size: 0.9rem; margin: 0.5rem 0 0 0;">Upload an image above to get started</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <div id="controls-section" class="col-span-2">
                <div class="card space-y-4">
                    <h3 style="font-weight:bold; margin-bottom:1rem;">FILTER</h3>
                    <div id="accordion-container" class="space-y-2"></div>
                    @guest
                        <p style="color:var(--text-sec);">Login untuk memproses gambar.</p>
                    @endguest
                </div>
            </div>
        </div>
    </main>
    </div>

    <script>
        // Prevent form resubmission on page reload
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }

        // Clear URL hash on load to prevent scroll issues
        if (window.location.hash) {
            window.history.replaceState(null, null, window.location.pathname + window.location.search);
        }

        const processingMethods = {
            "AI Forensic Analysis": ["Forensic AI Analysis"],
            "Filters & Smoothing": ["Median Filter", "Gaussian Filter", "Bilateral Filter"],
            "Contrast & Enhancement": ["Histogram Equalization", "CLAHE", "Normalized"],
            "Edge Detection": ["Sobel X", "Sobel Y", "Laplacian", "Canny"],
            "Morphological Ops": ["Opening", "Closing", "Erosion"],
            "Thresholding": ["Global Threshold", "Adaptive Mean", "Adaptive Gaussian"],
            "Analysis": ["Fourier Spectrum", "Contour Detection", "GLCM Texture", "Shape Analysis"]
        };

        const originalImage = document.getElementById('original-image');
        const processedImage = document.getElementById('processed-image');
        const viewOriginalBtn = document.getElementById('view-original-btn');
        const viewProcessedBtn = document.getElementById('view-processed-btn');
        const accordionContainer = document.getElementById('accordion-container');

        function selectImage(id, originalUrl, processedUrl){
            if(originalImage){ originalImage.src = originalUrl; }
            if(processedImage){ processedImage.src = processedUrl || ''; if(!processedUrl){ processedImage.classList.add('hidden'); viewProcessedBtn?.classList.remove('active'); viewOriginalBtn?.classList.add('active'); } }
            window.currentImageId = id;
        }

        function createAccordions(){
            if(!accordionContainer) return;
            for(const category in processingMethods){
                const accordionItem = document.createElement('div');
                accordionItem.className = 'accordion-item';

                const header = document.createElement('button');
                header.className = 'accordion-header';
                header.innerHTML = `<span>${category}</span><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>`;

                const content = document.createElement('div');
                content.className = 'accordion-content';
                const buttonsGrid = document.createElement('div');
                buttonsGrid.className = 'accordion-grid';

                processingMethods[category].forEach(method => {
                    const button = document.createElement('button');
                    button.className = 'process-button';
                    button.textContent = method;
                    button.addEventListener('click', () => {
                        if(!window.currentImageId){ 
                            alert('Select an image (Preview) first.'); 
                            return; 
                        }
                        
                        // Check if it's forensic analysis
                        if(method === 'Forensic AI Analysis') {
                            const forensicForm = document.getElementById('forensic-'+window.currentImageId);
                            if(forensicForm) {
                                if(confirm('Run AI-powered forensic analysis? This will detect injuries, assess severity, and suggest probable causes.')) {
                                    forensicForm.submit();
                                }
                            } else {
                                alert('Form not found for image ID: ' + window.currentImageId);
                            }
                            return;
                        }
                        
                        // Regular processing
                        const form = document.getElementById('proc-'+window.currentImageId);
                        if(!form) {
                            alert('Processing form not found for image ID: ' + window.currentImageId + '. Please click Preview button first.');
                            return;
                        }
                        form.method.value = method;
                        form.submit();
                    });
                    buttonsGrid.appendChild(button);
                });

                content.appendChild(buttonsGrid);
                accordionItem.appendChild(header);
                accordionItem.appendChild(content);
                accordionContainer.appendChild(accordionItem);
            }
        }

        viewOriginalBtn?.addEventListener('click', () => {
            originalImage?.classList.remove('hidden');
            processedImage?.classList.add('hidden');
            viewOriginalBtn.classList.add('active');
            viewProcessedBtn?.classList.remove('active');
        });
        viewProcessedBtn?.addEventListener('click', () => {
            if(processedImage?.src){
                originalImage?.classList.add('hidden');
                processedImage?.classList.remove('hidden');
                viewProcessedBtn.classList.add('active');
                viewOriginalBtn?.classList.remove('active');
            }
        });

        accordionContainer?.addEventListener('click', (e) => {
            const header = e.target.closest('.accordion-header');
            if (header) {
                const content = header.nextElementSibling;
                header.classList.toggle('open');
                content.classList.toggle('open');
            }
        });

        createAccordions();
        
        // Auto-select first image on page load
        @if($first)
        window.currentImageId = {{ $first->id }};
        // Auto-update preview images when page loads
        const firstOriginal = '{{ asset('storage/'.$first->original_path) }}';
        const firstProcessed = '{{ $first->processed_path ? asset('storage/'.$first->processed_path) : '' }}';
        if(originalImage) originalImage.src = firstOriginal;
        if(processedImage && firstProcessed) {
            processedImage.src = firstProcessed;
            // If there's a processed image, show it automatically
            if(firstProcessed && '{{ $first->processed_path }}') {
                viewProcessedBtn?.click();
            }
        }
        @endif
    </script>
</body>
</html>


