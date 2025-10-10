<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Patient {{ $patient->name }}</title>
    <link rel="stylesheet" href="{{ asset('style.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
</head>
<body>
    <header>
        <div class="container">
            <div class="header-content">
                <div class="logo-group">
                    <div class="logo-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 7V5a2 2 0 0 1 2-2h2"/><path d="M17 3h2a2 2 0 0 1 2 2v2"/><path d="M21 17v2a2 2 0 0 1-2 2h-2"/><path d="M7 21H5a2 2 0 0 1-2-2v-2"/><path d="M7 12a5 5 0 0 1 5-5"/><path d="M12 17a5 5 0 0 1-5-5"/></svg>
                    </div>
                    <div>
                        <h1>Forensics Imaging</h1>
                        <p class="subtitle">Patient: {{ $patient->name }} ({{ $patient->identifier }})</p>
                    </div>
                </div>
                <div id="status-badge" class="status-badge status-green">
                    <div id="status-dot" class="status-dot dot-green"></div>
                    <span id="status-text">Ready</span>
                </div>
            </div>
        </div>
    </header>

    <main class="container">
        <div class="main-grid">
            <div class="col-span-3 space-y-6">
                <div class="card">
                    <h3 style="font-weight:bold; color:white; margin-top:0;">Upload Image</h3>
                    @auth
                    <form method="post" action="{{ route('images.upload', $patient) }}" enctype="multipart/form-data">
                        @csrf
                        <input type="file" name="image" accept=".png,.jpg,.jpeg,.dcm" required />
                        <button type="submit" class="process-button" style="margin-top:.75rem; width:auto;">Upload</button>
                        <a href="{{ route('patients.index') }}" class="process-button" style="margin-left:.5rem; width:auto; display:inline-block; text-decoration:none; text-align:center;">Back</a>
                    </form>
                    @else
                        <a href="{{ route('auth.login') }}" class="process-button" style="width:auto; display:inline-block; text-decoration:none; text-align:center;">Login to upload</a>
                    @endauth
                    @if(session('status'))
                        <p style="margin-top:.75rem; color:#6EE7B7;">{{ session('status') }}</p>
                    @endif
                    @if(session('error'))
                        <p style="margin-top:.75rem; color:#F87171;">{{ session('error') }}</p>
                    @endif
                </div>

                <div id="image-preview-section" class="card" style="padding:0; {{ $patient->images->count() ? '' : 'display:none;' }}">
                    <div class="preview-header">
                        <h3 style="font-weight:bold; color:white; margin:0;">Image Preview</h3>
                        <div class="preview-toggle">
                            <button id="view-original-btn" class="active">Original</button>
                            <button id="view-processed-btn">Processed</button>
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
                    <h3 style="font-weight:bold; color:white; margin-top:0;">Images</h3>
                    <div class="space-y-2">
                        @foreach($patient->images as $img)
                        <div class="card" style="padding: .75rem;">
                            <div class="row" style="justify-content: space-between;">
                                <div style="color:var(--text-dark);">#{{ $img->id }}</div>
                                @auth
                                <form method="post" action="{{ route('images.destroy', $img) }}" onsubmit="return confirm('Delete this image?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="process-button" style="width:auto;">Delete</button>
                                </form>
                                @endauth
                            </div>
                            <div class="row" style="margin-top:.5rem; gap: .75rem;">
                                <img src="{{ asset('storage/'.$img->original_path) }}" alt="original" style="max-width:120px; border-radius:.375rem;">
                                @if($img->processed_path)
                                <img src="{{ asset('storage/'.$img->processed_path) }}" alt="processed" style="max-width:120px; border-radius:.375rem;">
                                @endif
                                <button class="process-button" style="width:auto;" onclick="selectImage({{ $img->id }}, '{{ asset('storage/'.$img->original_path) }}', '{{ $img->processed_path ? asset('storage/'.$img->processed_path) : '' }}')">Preview</button>
                            </div>
                            @if($img->features_text)
                                <pre style="white-space:pre-wrap; color:#fff; margin-top:.5rem;">{{ $img->features_text }}</pre>
                            @endif
                            @auth
                            <form id="proc-{{ $img->id }}" method="post" action="{{ route('images.process', $img) }}">
                                @csrf
                                <input type="hidden" name="method" value="">
                            </form>
                            @endauth
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div id="controls-section" class="col-span-2">
                <div class="card space-y-4">
                    <h3 style="font-weight:bold; color:white; margin-bottom:1rem;">Control Panel</h3>
                    <div id="accordion-container" class="space-y-2"></div>
                    @guest
                        <p style="color:#D1D5DB;">Login untuk memproses gambar.</p>
                    @endguest
                </div>
            </div>
        </div>
    </main>

    <script>
        const processingMethods = {
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
                        if(!window.currentImageId){ alert('Select an image (Preview) first.'); return; }
                        const form = document.getElementById('proc-'+window.currentImageId);
                        if(!form) return;
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
        @if($first)
        window.currentImageId = {{ $first->id }};
        @endif
    </script>
</body>
</html>


